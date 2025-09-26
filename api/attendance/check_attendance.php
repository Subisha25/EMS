<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once('../../config/db.php'); // ✅ use your DB path

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['emp_id'])) {
    echo json_encode(["status" => false, "message" => "Employee ID is required"]);
    exit;
}

$emp_id = $conn->real_escape_string($data['emp_id']);
$date = date('Y-m-d');

$query = "SELECT status, time, day FROM attendance WHERE emp_id = '$emp_id' AND date = '$date'";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $formattedTime = date('h:i A', strtotime($row['time']));
    echo json_encode([
        "status" => true,
        "message" => "You marked attendance as <b>{$row['status']}</b> at <b>{$formattedTime}</b> on <b>{$row['day']}</b>."
    ]);
} else {
    echo json_encode(["status" => false, "message" => "You have not marked attendance today."]);
}

$conn->close();
