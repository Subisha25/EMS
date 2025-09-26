<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once('../../config/db.php'); // adjust your DB connection path
date_default_timezone_set('Asia/Kolkata'); // Add this line


error_log("Raw POST: " . file_get_contents("php://input"));



$data = json_decode(file_get_contents("php://input"), true);


error_log("Decoded JSON: " . print_r($data, true));


if (!isset($data['emp_id']) || !isset($data['status'])) {
    echo json_encode(["status" => false, "message" => "Invalid input"]);
    exit;
}

$emp_id = $conn->real_escape_string($data['emp_id']);
$status = $conn->real_escape_string($data['status']);
$first_name = $conn->real_escape_string($data['first_name']);
$last_name = $conn->real_escape_string($data['last_name']);

$date = date('Y-m-d');
$day = date('l'); // like Monday, Tuesday
$time = date('H:i:s');

// Check if already marked
$check = $conn->query("SELECT * FROM attendance WHERE emp_id = '$emp_id' AND date = '$date'");

if ($check && $check->num_rows > 0) {
    echo json_encode(["status" => false, "message" => "Attendance already marked for today"]);
    exit;
}

// Insert new attendance
$sql = "INSERT INTO attendance (emp_id, first_name, last_name, status, date, day, time)
        VALUES ('$emp_id', '$first_name', '$last_name', '$status', '$date', '$day', '$time')";


if ($conn->query($sql)) {
    echo json_encode([
        "status" => true,
        "message" => "Attendance marked as $status at " . date('h:i A') . " on $day"
    ]);
} else {
    echo json_encode(["status" => false, "message" => "Error: " . $conn->error]);
}

$conn->close();
?>
