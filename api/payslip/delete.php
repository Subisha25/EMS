<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once('../../config/db.php');

$data = json_decode(file_get_contents("php://input"));

if (isset($data->id)) {
    $stmt = $conn->prepare("DELETE FROM payslips WHERE id = ?");
    $stmt->bind_param("i", $data->id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Payslip deleted"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Delete failed"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Missing ID"]);
}
?>
