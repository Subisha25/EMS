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
$month  = isset($data['month']) ? $data['month'] : date('m'); 
$year   = isset($data['year'])  ? $data['year']  : date('Y');

// Total days in this month
$total_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

// Fetch attendance records
$query = "
    SELECT status, date 
    FROM attendance 
    WHERE emp_id = '$emp_id' 
      AND MONTH(date) = '$month' 
      AND YEAR(date) = '$year'
";
$result = $conn->query($query);

$present = 0;
$absent  = 0;
$markedDays = 0;

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $markedDays++;
        if (strtolower($row['status']) === 'present') {
            $present++;
        } elseif (strtolower($row['status']) === 'absent') {
            $absent++;
        }
    }
}

// Remaining unmarked days
$unmarked_days = $total_days - $markedDays;

// Attendance rate (only based on present vs total days)
$attendance_rate = $total_days > 0 ? round(($present / $total_days) * 100) : 0;

echo json_encode([
    "status"        => true,
    "emp_id"        => $emp_id,
    "month"         => $month,
    "year"          => $year,
    "total_days"    => $total_days,
    "present_days"  => $present,
    "absent_days"   => $absent,
    "unmarked_days" => $unmarked_days,
    "attendance_rate" => $attendance_rate
]);

$conn->close();
?>
