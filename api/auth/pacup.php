<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once('../../config/db.php');

$data = json_decode(file_get_contents("php://input"));

if (isset($data->email) && isset($data->password) && isset($data->role)) {
    $email = $conn->real_escape_string($data->email);
    $password = $data->password;
    $role = strtolower(trim($data->role)); // Clean role

    if ($role === 'admin') {
        $query = "SELECT * FROM admin_profiles WHERE email = '$email' LIMIT 1";
    } elseif ($role === 'employee') {
        $query = "SELECT * FROM employees WHERE email = '$email' LIMIT 1";
    } else {
        echo json_encode([
            "status" => false,
            "message" => "Invalid role"
        ]);
        exit;
    }

    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            // Optional: attach role to user before sending back
            $user['role'] = $role;

            echo json_encode([
                "status" => true,
                "message" => "Login successful",
                "user" => $user
            ]);
        } else {
            echo json_encode([
                "status" => false,
                "message" => "Incorrect password"
            ]);
        }
    } else {
        echo json_encode([
            "status" => false,
            "message" => "User not found for selected role"
        ]);
    }
} else {
    echo json_encode([
        "status" => false,
        "message" => "Missing email, password, or role"
    ]);
}
