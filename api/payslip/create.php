<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}


require_once('../../config/db.php');

$data = json_decode(file_get_contents("php://input"));

if (
    isset($data->employeeId) &&
    isset($data->name) &&
    isset($data->designation) &&
    isset($data->doj) &&
    isset($data->basicSalary) &&
    isset($data->professionalTax) &&
    isset($data->incomeTax) &&
    isset($data->lop) &&
    isset($data->salaryMonth)
) {
    $stmt = $conn->prepare("INSERT INTO payslips (employee_id, name, designation, doj, basic_salary, professional_tax, income_tax, lop, salary_month)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "ssssdddis",
        $data->employeeId,
        $data->name,
        $data->designation,
        $data->doj,
        $data->basicSalary,
        $data->professionalTax,
        $data->incomeTax,
        $data->lop,
        $data->salaryMonth
    );

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Payslip added"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to add"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Missing fields"]);
}
?>
