<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once('../../config/db.php');

$data = json_decode(file_get_contents("php://input"), true);

$month = isset($data['month']) ? $data['month'] : date('m');
$year = isset($data['year']) ? $data['year'] : date('Y');

$query = "
    SELECT 
        e.emp_id, e.first_name, e.last_name,
        a.status, a.date, a.day, a.time
    FROM employees e
    LEFT JOIN attendance a ON e.emp_id = a.emp_id 
        AND MONTH(a.date) = '$month' AND YEAR(a.date) = '$year'
    ORDER BY e.emp_id, a.date
";

$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $records = [];
    while ($row = $result->fetch_assoc()) {
        $records[] = [
            "emp_id" => $row['emp_id'],
            "first_name" => $row['first_name'],
            "last_name" => $row['last_name'],
            "status" => $row['status'],
    "date" => $row['date'] ? date('Y-m-d', strtotime($row['date'])) : null,
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
