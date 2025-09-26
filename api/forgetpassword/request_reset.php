<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once("../../config/db.php");
require_once("../../phpmailer/PHPMailer.php");
require_once("../../phpmailer/SMTP.php");
require_once("../../phpmailer/Exception.php");

use PHPMailer\PHPMailer\PHPMailer;

$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'] ?? '';
$role = strtolower(trim($data['role'] ?? '')); // "admin" or "employee"

if (!$email || !$role) {
    echo json_encode(["status" => false, "message" => "Email & role required"]);
    exit;
}

// Choose table
$table = ($role === "admin") ? "admin_profiles" : "employees";

// Check email
$sql = "SELECT id FROM $table WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => false, "message" => "Email not found in $role records"]);
    exit;
}

$row = $result->fetch_assoc();
$userId = $row['id'];

// Generate OTP
$otp = rand(100000, 999999);

// Save OTP in a password_resets table
$conn->query("CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255),
    role ENUM('admin','employee'),
    otp VARCHAR(6),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$conn->query("DELETE FROM password_resets WHERE email='$email' AND role='$role'"); // clear old OTP
$sqlInsert = "INSERT INTO password_resets (email, role, otp) VALUES (?, ?, ?)";
$stmt2 = $conn->prepare($sqlInsert);
$stmt2->bind_param("sss", $email, $role, $otp);
$stmt2->execute();

// Send email
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = "smtp.gmail.com"; 
    $mail->SMTPAuth = true;
    $mail->Username = "pcstech2021@gmail.com"; 
    $mail->Password = "ndkx mmtq mhtz fxug"; 
    $mail->SMTPSecure = "tls";
    $mail->Port = 587;

    $mail->setFrom("pcstech2021@gmail.com", "EMS Support");
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = "Password Reset OTP";
    $mail->Body = "<h3>Your OTP is <b>$otp</b></h3><p>Valid for 5 minutes.</p>";

    $mail->send();
    echo json_encode(["status" => true, "message" => "OTP sent to email"]);
} catch (Exception $e) {
    echo json_encode(["status" => false, "message" => "Mailer Error: " . $mail->ErrorInfo]);
}
?>
