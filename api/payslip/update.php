<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once('../../config/db.php');

$data = json_decode(file_get_contents("php://input"));

if (isset($data->id)) {
    $stmt = $conn->prepare("UPDATE payslips SET employee_id=?, name=?, designation=?, doj=?, basic_salary=?, professional_tax=?, income_tax=?, lop=?, salary_month=? WHERE id=?");

    $stmt->bind_param(
        "ssssdddisi",
        $data->employeeId,
        $data->name,
        $data->designation,
        $data->doj,
        $data->basicSalary,
        $data->professionalTax,
        $data->incomeTax,
        $data->lop,
        $data->salaryMonth,
        $data->id
    );

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Payslip updated"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Update failed"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Missing ID"]);
}
?>
