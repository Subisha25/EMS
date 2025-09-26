<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
require_once("../../config/db.php");

$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'] ?? '';
$role = strtolower(trim($data['role'] ?? ''));
$otp = $data['otp'] ?? '';

if (!$email || !$role || !$otp) {
    echo json_encode(["status" => false, "message" => "Email, role & OTP required"]);
    exit;
}

$sql = "SELECT * FROM password_resets WHERE email=? AND role=? AND otp=? AND created_at >= (NOW() - INTERVAL 5 MINUTE)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $email, $role, $otp);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["status" => true, "message" => "OTP verified"]);
} else {
    echo json_encode(["status" => false, "message" => "Invalid or expired OTP"]);
}
?>
