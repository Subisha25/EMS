<?php
header("Content-Type: application/json");
include_once("../../config/db.php");

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;

if (!$id) {
    echo json_encode(["status" => false, "message" => "ID is required"]);
    exit;
}

// Get image name to delete file
$getImgSql = "SELECT image FROM admin_profiles WHERE id = ?";
$getStmt = $conn->prepare($getImgSql);
$getStmt->bind_param("i", $id);
$getStmt->execute();
$result = $getStmt->get_result();
$admin = $result->fetch_assoc();
$imageName = $admin['image'];

$sql = "DELETE FROM admin_profiles WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Delete image
    $imgPath = '../uploads/' . $imageName;
    if (file_exists($imgPath)) {
        unlink($imgPath);
    }

    echo json_encode(["status" => true, "message" => "Admin deleted"]);
} else {
    echo json_encode(["status" => false, "message" => "Delete failed"]);
}
?>
