<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once('../../config/db.php');

if (isset($_GET['employee_id'])) {
    $employeeId = $_GET['employee_id'];

    if (isset($_GET['salary_month'])) {
        $salaryMonth = $_GET['salary_month'];
        $stmt = $conn->prepare("SELECT id, employee_id, name, designation, doj, basic_salary, professional_tax, income_tax, lop, salary_month FROM payslips WHERE employee_id = ? AND salary_month = ?");
        $stmt->bind_param("ss", $employeeId, $salaryMonth);
    } else {
        $stmt = $conn->prepare("SELECT id, employee_id, name, designation, doj, basic_salary, professional_tax, income_tax, lop, salary_month FROM payslips WHERE employee_id = ?");
        $stmt->bind_param("s", $employeeId);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode(["status" => "success", "data" => $data]);
} else {
    echo json_encode(["status" => "error", "message" => "employee_id is required"]);
}
?>
