<?php
require_once '../../config/db.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../../phpmailer/PHPMailer.php';
require_once '../../phpmailer/SMTP.php';
require_once '../../phpmailer/Exception.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$status = isset($_GET['status']) ? $_GET['status'] : '';

if (!$id || !in_array($status, ['Approved', 'Rejected'])) {
    echo "Invalid request";
    exit;
}

$res = $conn->query("SELECT * FROM leaves WHERE id = $id");

if ($res->num_rows > 0) {
    $row = $res->fetch_assoc();
    $email = $row['email'];
    $name  = $row['name'];
    $empId = $row['employee_id'];
    $leaveFrom = $row['leave_from'];
    $leaveTo   = $row['leave_to'];
    $currentStatus = $row['status'];

    if ($currentStatus !== 'Pending') {
        echo "<h2>Leave already <b>$currentStatus</b>.</h2>";
        exit;
    }

    // Update leave status
    $conn->query("UPDATE leaves SET status='$status' WHERE id = $id");

    // ✅ If Approved → insert absent records into attendance
    if ($status === 'Approved') {
        $start = new DateTime($leaveFrom);
        $end   = new DateTime($leaveTo);
        $end->modify('+1 day'); // include end date

        while ($start < $end) {
            $date = $start->format('Y-m-d');
            $day  = $start->format('l');

            // Check if already attendance marked (avoid duplicates)
            $check = $conn->query("SELECT * FROM attendance WHERE emp_id='$empId' AND date='$date'");
            if ($check->num_rows === 0) {
                $conn->query("INSERT INTO attendance (emp_id, first_name, last_name, status, date, day, time)
                              VALUES ('$empId', '$name', '', 'Absent', '$date', '$day', '00:00:00')");
            }
            $start->modify('+1 day');
        }
    }

    // Send Email Notification
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'pcstech2021@gmail.com';
        $mail->Password   = 'ndkx mmtq mhtz fxug'; 
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('pcstech2021@gmail.com', 'Leave System');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = "Leave $status";
        $mail->Body    = "Hello $name,<br>Your leave request has been <b>$status</b>.";

        $mail->send();
        echo "<h2>Leave has been <b>$status</b> and employee notified.</h2>";
    } catch (Exception $e) {
        echo "Email send failed: " . $mail->ErrorInfo;
    }
} else {
    echo "Invalid leave ID";
}
?>
