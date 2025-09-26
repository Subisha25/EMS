<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include_once("../../config/db.php");


$name = $_POST['name'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$location = $_POST['location'];
$officeDate = $_POST['office_opening_date'];
$role = $_POST['role'];

// File upload
$image = $_FILES['image'];
$imageName = time() . '_' . $image['name'];
$imagePath = '../../uploads/' . $imageName;
move_uploaded_file($image['tmp_name'], $imagePath);

$sql = "INSERT INTO admin_profiles (name, phone, email, password, location, office_opening_date, role, image) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssss", $name, $phone, $email, $password, $location, $officeDate, $role, $imageName);

if ($stmt->execute()) {
  echo json_encode(["status" => true, "message" => "Admin added successfully"]);
} else {
  echo json_encode(["status" => false, "message" => "Insert failed"]);
}
?>
