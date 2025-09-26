<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include_once("../../config/db.php");

$sql = "SELECT * FROM admin_profiles";
$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $row['image'] = 'http://localhost/EMS-backend/uploads/' . $row['image'];
    $data[] = $row;
}

echo json_encode(["status" => true, "admins" => $data]);
?>
