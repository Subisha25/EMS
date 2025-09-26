<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require_once __DIR__ . '/../../config/db.php';

$leaves = [];

if (isset($_GET['employee_id'])) {
    // Get leave requests for a specific employee
    $employeeId = $_GET['employee_id'];
    $query = "SELECT * FROM leaves WHERE employee_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $employeeId);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Admin: Get all leave requests
    $query = "SELECT * FROM leaves ORDER BY id DESC";
    $result = $conn->query($query);
}

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $leaves[] = $row;
    }
    echo json_encode(["status" => "success", "data" => $leaves]);
} else {
    echo json_encode(["status" => "error", "message" => "Query failed"]);
}
?>
