<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once('../../config/db.php');

// Get last emp_id
$sql = "SELECT emp_id FROM employees ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

$nextEmpId = "PCS001"; // default if no employee

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $lastEmpId = $row['emp_id'];

    // Extract number from emp_id (e.g., PCS005 → 5)
    $num = intval(substr($lastEmpId, 3));
    $nextNum = $num + 1;

    // Format with leading zeros (3 digits)
    $nextEmpId = "PCS" . str_pad($nextNum, 3, "0", STR_PAD_LEFT);
}

echo json_encode(["next_emp_id" => $nextEmpId]);
?>
