<?php
function sendMailToAdmin($id, $name, $email, $type, $from, $to, $reason) {
  $adminEmail = "pcstech2021@gmail.com";
  $subject = "New Leave Request from $name";

  $approveLink = "http://localhost/ems-backend/api/leave/update_status.php?id=$id&action=approve";
  $rejectLink = "http://localhost/ems-backend/api/leave/update_status.php?id=$id&action=reject";

  $message = "
    <h3>Leave Request Details</h3>
    <p><strong>Name:</strong> $name</p>
    <p><strong>Email:</strong> $email</p>
    <p><strong>Type:</strong> $type</p>
    <p><strong>From:</strong> $from</p>
    <p><strong>To:</strong> $to</p>
    <p><strong>Reason:</strong> $reason</p>
    <p>
      <a href='$approveLink' style='padding:10px 15px;background-color:green;color:white;text-decoration:none;'>Approve</a>
      &nbsp;
      <a href='$rejectLink' style='padding:10px 15px;background-color:red;color:white;text-decoration:none;'>Reject</a>
    </p>
  ";

  $headers = "MIME-Version: 1.0\r\n";
  $headers .= "Content-type: text/html\r\n";
  $headers .= "From: no-reply@ems.com\r\n";

  mail($adminEmail, $subject, $message, $headers);
}
?>
