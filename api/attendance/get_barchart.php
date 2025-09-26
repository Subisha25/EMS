<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once('../../config/db.php');

$year = isset($_GET['year']) ? $conn->real_escape_string($_GET['year']) : date('Y');

// Prepare array for 12 months
$attendanceSummary = [];
for ($i = 1; $i <= 12; $i++) {
    $attendanceSummary[$i] = 0;
}

// Fetch total present per month for all employees
$query = "
    SELECT MONTH(date) as month, COUNT(*) as present_count
    FROM attendance
    WHERE status = 'Present' AND YEAR(date) = '$year'
    GROUP BY MONTH(date)
";

$result = $conn->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $month = (int)$row['month'];
        $attendanceSummary[$month] = (int)$row['present_count'];
    }

    $response = [
        "status" => true,
        "message" => "Monthly attendance fetched successfully",
        "data" => [
            "labels" => ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
            "present" => array_values($attendanceSummary)
        ]
    ];
    echo json_encode($response);
} else {
    echo json_encode([
        "status" => false,
        "message" => "Failed to fetch attendance"
    ]);
}

$conn->close();
?>
