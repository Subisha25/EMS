<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once('../../config/db.php');

$sql = "SELECT id, emp_id, first_name, last_name, designation, email, mobile, doj FROM employees ORDER BY id DESC";
$result = $conn->query($sql);

$data = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
} else {
    echo json_encode([]);
}
?>
