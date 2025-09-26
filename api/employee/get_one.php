<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once('../../config/db.php');

// Get emp_id from query params
$emp_id = $_GET['emp_id'] ?? '';

// Validate emp_id
if (empty($emp_id)) {
    echo json_encode(["status" => false, "message" => "emp_id is required."]);
    exit;
}

$sql = "SELECT * FROM employees WHERE emp_id = '$emp_id'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode(["status" => false, "message" => "No employee found."]);
}
?>
