<?php
header("Content-Type: application/json");
include_once("../../config/db.php");

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;

if (!$id) {
  echo json_encode(["status" => false, "message" => "ID is required"]);
  exit;
}

$sql = "SELECT * FROM admin_profiles WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row) {
  $row['image'] = 'http://localhost/EMS-backend/uploads/' . $row['image'];
  echo json_encode(["status" => true, "admin" => $row]);
} else {
  echo json_encode(["status" => false, "message" => "Admin not found"]);
}
?>
