<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once('../../config/db.php');

$data = json_decode(file_get_contents("php://input"));

if (isset($data->id)) {
    $id = intval($data->id);
    $sql = "UPDATE employees SET available = 0 WHERE id = $id";
    if ($conn->query($sql)) {
        echo json_encode(["status" => true, "message" => "Employee restored successfully."]);
    } else {
        echo json_encode(["status" => false, "message" => "Error: " . $conn->error]);
    }
} else {
    echo json_encode(["status" => false, "message" => "Invalid ID provided."]);
}
?>
