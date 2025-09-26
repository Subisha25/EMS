
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

require_once('../../config/db.php');

$data = $_POST;

if (!empty($data)) {
    $emp_id     = $conn->real_escape_string($data['emp_id']);
    $first_name = $conn->real_escape_string($data['first_name']);
    $last_name  = $conn->real_escape_string($data['last_name']);
    $designation = $conn->real_escape_string($data['designation']);
    $email      = $conn->real_escape_string($data['email']);
    $mobile     = $conn->real_escape_string($data['mobile']);
    $country    = $conn->real_escape_string($data['country']);
    $state      = $conn->real_escape_string($data['state']);
    $city       = $conn->real_escape_string($data['city']);
    $dob        = $conn->real_escape_string($data['dob']);
    $doj        = $conn->real_escape_string($data['doj']);
    $address    = $conn->real_escape_string($data['address']);
    $password   = password_hash($data['password'], PASSWORD_DEFAULT);

    // 🔒 Check for duplicate emp_id or email
    $checkSql = "SELECT id FROM employees WHERE emp_id = '$emp_id' OR email = '$email'";
    $checkResult = $conn->query($checkSql);

    if ($checkResult->num_rows > 0) {
        echo json_encode(["status" => false, "message" => "Employee ID or Email already exists."]);
        exit;
    }

    // 🖼️ File Upload
    $photo = "";
  if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $file_name = time() . "_" . basename($_FILES['photo']['name']);
    $upload_path = __DIR__ . '/../../uploads/' . $file_name;

    if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
        $photo = $file_name;
    } else {
        echo json_encode(["status" => false, "message" => "Image upload failed."]);
        exit;
    }
}

$sql = "INSERT INTO employees (emp_id, first_name, last_name, designation, email, mobile, country, state, city, dob, doj, photo, address, password, available) 
        VALUES ('$emp_id', '$first_name', '$last_name', '$designation', '$email', '$mobile', '$country', '$state', '$city', '$dob', '$doj', '$photo', '$address', '$password', 0)";


    // 📥 Insert Query
    // $sql = "INSERT INTO employees (emp_id, first_name, last_name, designation, email, mobile, country, state, city, dob, doj, photo, address, password) 
    //         VALUES ('$emp_id', '$first_name', '$last_name', '$designation', '$email', '$mobile', '$country', '$state', '$city', '$dob', '$doj', '$photo', '$address', '$password')";

    if ($conn->query($sql)) {
        echo json_encode(["status" => true, "message" => "Employee added successfully."]);
    } else {
        echo json_encode(["status" => false, "message" => "Error: " . $conn->error]);
    }
} else {
    echo json_encode(["status" => false, "message" => "Invalid data."]);
}
?>
