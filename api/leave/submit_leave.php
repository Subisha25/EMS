<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once __DIR__ . '/../../config/db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../phpmailer/PHPMailer.php';
require_once __DIR__ . '/../../phpmailer/SMTP.php';
require_once __DIR__ . '/../../phpmailer/Exception.php';

$data = json_decode(file_get_contents("php://input"), true);

// Basic field validation
if (
    empty($data['employeeId']) ||
    empty($data['employeeName']) ||
    empty($data['employeeEmail']) ||
    empty($data['type']) ||
    empty($data['from']) ||
    empty($data['to']) ||
    empty($data['reason'])
) {
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

// Sanitize input
$empId = $conn->real_escape_string($data['employeeId']);
$name = $conn->real_escape_string($data['employeeName']);
$email = $conn->real_escape_string($data['employeeEmail']);
$type = $conn->real_escape_string($data['type']);
$from = $conn->real_escape_string($data['from']);
$to = $conn->real_escape_string($data['to']);
$reason = $conn->real_escape_string($data['reason']);
$status = "Pending";

// Insert into DB
$query = "INSERT INTO leaves (employee_id, name, email, leave_type, leave_from, leave_to, reason, status)
          VALUES ('$empId', '$name', '$email', '$type', '$from', '$to', '$reason', '$status')";

if ($conn->query($query)) {
    $leaveId = $conn->insert_id;

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'pcstech2021@gmail.com';
        $mail->Password   = 'ndkxmmtqmhtzfxug'; 
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

     $mail->setFrom('pcstech2021@gmail.com', 'EMS Support');
$mail->addAddress('pcstech2021@gmail.com');  // HR/Admin mail
$mail->addReplyTo($email, $name);  // Employee mail

$mail->isHTML(true);
$mail->Subject = "Leave Request - $name ($empId)";

$mail->Body = "
    <h3>Leave Request Submitted</h3>
    <p><b>Employee Name:</b> $name</p>
    <p><b>Employee ID:</b> $empId</p>
    <p><b>Email:</b> $email</p>
    <p><b>Leave Type:</b> $type</p>
    <p><b>From:</b> $from</p>
    <p><b>To:</b> $to</p>
    <p><b>Reason:</b> $reason</p>
    <br>
    <p>Action:</p>
    <a href='http://localhost/EMS-backend/api/leave/approve_reject.php?id=$leaveId&status=Approved'>
        <button style='background:green;color:white;padding:10px 15px;border:none;'>Approve</button>
    </a>
    &nbsp;
    <a href='http://localhost/EMS-backend/api/leave/approve_reject.php?id=$leaveId&status=Rejected'>
        <button style='background:red;color:white;padding:10px 15px;border:none;'>Reject</button>
    </a>
";


        $mail->send();
        echo json_encode(["status" => "success"]);
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => $mail->ErrorInfo]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "DB insert failed"]);
}
?>
