<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE, POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once('../../config/db.php');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'])) {
    echo json_encode(["status" => false, "message" => "Attendance ID is required"]);
    exit;
}

$id = $conn->real_escape_string($data['id']);

$result = $conn->query("DELETE FROM attendance WHERE id = '$id'");

if ($result) {
    echo json_encode(["status" => true, "message" => "Attendance deleted successfully"]);
} else {
    echo json_encode(["status" => false, "message" => "Failed to delete attendance"]);
}

$conn->close();
