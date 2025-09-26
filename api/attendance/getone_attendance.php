<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once('../../config/db.php');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['emp_id'])) {
    echo json_encode(["status" => false, "message" => "Employee ID is required"]);
    exit;
}

$emp_id = $conn->real_escape_string($data['emp_id']);

if ($emp_id === "ALL") {
    $query = "SELECT * FROM attendance ORDER BY date DESC";
} else {
    $query = "SELECT * FROM attendance WHERE emp_id = '$emp_id' ORDER BY date DESC";
}

$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $records = [];
    while ($row = $result->fetch_assoc()) {
        $records[] = [
            "emp_id" => $row['emp_id'],
            "first_name" => $row['first_name'],
            "last_name" => $row['last_name'],
            "status" => $row['status'],
            "date" => $row['date'],
            "day" => $row['day'],
            "time" => $row['time']
        ];
    }

    echo json_encode([
        "status" => true,
        "message" => "Attendance records fetched successfully",
        "data" => $records
    ]);
} else {
    echo json_encode([
        "status" => false,
        "message" => "No attendance records found"
    ]);
}

$conn->close();
?>
