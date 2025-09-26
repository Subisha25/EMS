<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include_once("../../config/db.php");

$id = $_POST['id'] ?? '';
$name = $_POST['name'] ?? '';
$phone = $_POST['phone'] ?? '';
$email = $_POST['email'] ?? '';
$location = $_POST['location'] ?? '';
$officeDate = $_POST['office_opening_date'] ?? '';
$role = $_POST['role'] ?? '';

// Validate ID
if (empty($id)) {
        header("Content-Type: application/json; charset=UTF-8");

    echo json_encode(["status" => false, "message" => "Admin ID is missing"]);
    exit;
}

// Check existing admin
$checkSql = "SELECT * FROM admin_profiles WHERE id = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("i", $id);
$checkStmt->execute();
$result = $checkStmt->get_result();
$existingAdmin = $result->fetch_assoc();

if (!$existingAdmin) {
    echo json_encode(["status" => false, "message" => "Admin not found"]);
    exit;
}

// Handle image
$existingImage = $_POST['existing_image'] ?? '';
$existingImage = basename($existingImage);
$imageName = $existingImage;

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $cleanName = preg_replace('/[^a-zA-Z0-9.\-_]/', '_', basename($_FILES['image']['name']));
    $imageName = time() . '_' . $cleanName;
    $uploadDir = __DIR__ . '/../../uploads/';
    $imagePath = $uploadDir . $imageName;

    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
        $oldImagePath = $uploadDir . $existingImage;
        if (!empty($existingImage) && file_exists($oldImagePath)) unlink($oldImagePath);
    } else {
        echo json_encode(["status" => false, "message" => "Image upload failed"]);
        exit;
    }
}

// Update query (without password)
$sql = "UPDATE admin_profiles SET name=?, phone=?, email=?, location=?, office_opening_date=?, role=?, image=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssi", $name, $phone, $email, $location, $officeDate, $role, $imageName, $id);

if ($stmt->execute()) {
    echo json_encode([
        "status" => true,
        "message" => $stmt->affected_rows > 0 ? "Admin profile updated" : "Profile already up to date",
        "image" => $imageName
    ]);
} else {
    echo json_encode(["status" => false, "message" => "Update failed: " . $stmt->error]);
}

$stmt->close();
$checkStmt->close();
$conn->close();
?>
