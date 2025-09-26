<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once("../../config/db.php");

$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'] ?? '';
$role = strtolower(trim($data['role'] ?? ''));
$newPassword = $data['new_password'] ?? '';
$confirmPassword = $data['confirm_password'] ?? '';

if (!$email || !$role || !$newPassword || !$confirmPassword) {
    echo json_encode(["status" => false, "message" => "All fields required"]);
    exit;
}

if ($newPassword !== $confirmPassword) {
    echo json_encode(["status" => false, "message" => "Passwords do not match"]);
    exit;
}

$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
$table = ($role === "admin") ? "admin_profiles" : "employees";

$sql = "UPDATE $table SET password=? WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $hashedPassword, $email);

if ($stmt->execute()) {
    // remove OTP after success
    $conn->query("DELETE FROM password_resets WHERE email='$email' AND role='$role'");
    echo json_encode(["status" => true, "message" => "Password updated successfully"]);
} else {
    echo json_encode(["status" => false, "message" => "Password update failed"]);
}
?>
