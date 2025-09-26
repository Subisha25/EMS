<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, PUT");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once('../../config/db.php');

if (isset($_POST['id'])) {
    $id = $conn->real_escape_string($_POST['id']);
    $first_name = $conn->real_escape_string($_POST['first_name'] ?? '');
    $last_name = $conn->real_escape_string($_POST['last_name'] ?? '');
    $email = $conn->real_escape_string($_POST['email'] ?? '');
    $mobile = $conn->real_escape_string($_POST['mobile'] ?? '');
    $designation = $conn->real_escape_string($_POST['designation'] ?? '');
    $dob = $conn->real_escape_string($_POST['dob'] ?? '');
    $doj = $conn->real_escape_string($_POST['doj'] ?? '');
    $country = $conn->real_escape_string($_POST['country'] ?? '');
    $state = $conn->real_escape_string($_POST['state'] ?? '');
    $city = $conn->real_escape_string($_POST['city'] ?? '');
    $address = $conn->real_escape_string($_POST['address'] ?? '');

    $uploadFileName = null;

    // Handle file upload
    if (isset($_FILES['upload']) && $_FILES['upload']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "../../uploads/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $originalName = basename($_FILES["upload"]["name"]);
        $filename = time() . "" . preg_replace("/[^a-zA-Z0-9.\-]/", "_", $originalName);
        $targetFilePath = $targetDir . $filename;

        if (move_uploaded_file($_FILES["upload"]["tmp_name"], $targetFilePath)) {
            $uploadFileName = $filename;
        } else {
            echo json_encode(["status" => false, "message" => "File upload failed."]);
            exit;
        }
    }

    // Prepare employee update
    $updateFields = "
        first_name = '$first_name',
        last_name = '$last_name',
        email = '$email',
        mobile = '$mobile',
        designation = '$designation',
        dob = '$dob',
        doj = '$doj',
        country = '$country',
        state = '$state',
        city = '$city',
        address = '$address'
    ";

    if ($uploadFileName) {
        $updateFields .= ", photo = '$uploadFileName'";
    }

    $sql = "UPDATE employees SET $updateFields WHERE id = $id";

    if ($conn->query($sql)) {

        // ✅ Get the employee_id from employees table
        $empRes = $conn->query("SELECT emp_id FROM employees WHERE id = $id");
        $empRow = $empRes->fetch_assoc();
        $employee_id = $empRow['emp_id'];

        // ✅ Update payslip details
        $name = $first_name . ' ' . $last_name;
        $updatePayslip = "
            UPDATE payslips 
            SET name = '$name',
                designation = '$designation',
                doj = '$doj'
            WHERE employee_id = '$employee_id'
        ";
        $conn->query($updatePayslip);

        echo json_encode(["status" => true, "message" => "Employee and Payslip updated successfully."]);
    } else {
        echo json_encode(["status" => false, "message" => "Error updating: " . $conn->error]);
    }
} else {
    echo json_encode(["status" => false, "message" => "Invalid request."]);
}
?>
