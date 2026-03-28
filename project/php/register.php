<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../db/mysql.php';
require_once '../db/mongo.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid input data."]);
    exit();
}

$email = $data['email'] ?? '';
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';
$name = $data['name'] ?? '';
$age = $data['age'] ?? '';
$dob = $data['dob'] ?? '';
$mobile = $data['mobile'] ?? '';

if (empty($email) || empty($username) || empty($password)) {
    http_response_code(400);
    echo json_encode(["error" => "Missing required fields."]);
    exit();
}

$stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
$stmt->bind_param("ss", $email, $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    http_response_code(409);
    echo json_encode(["error" => "Email or Username already exists."]);
    $stmt->close();
    $mysqli->close();
    exit();
}
$stmt->close();

$hashed_password = password_hash($password, PASSWORD_BCRYPT);
$insert_stmt = $mysqli->prepare("INSERT INTO users (email, username, password) VALUES (?, ?, ?)");
$insert_stmt->bind_param("sss", $email, $username, $hashed_password);

if (!$insert_stmt->execute()) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to register user."]);
    $insert_stmt->close();
    $mysqli->close();
    exit();
}

$user_id = $insert_stmt->insert_id;
$insert_stmt->close();
$mysqli->close();

try {
    $bulk = new MongoDB\Driver\BulkWrite;
    
    $profile_data = [
        "user_id" => $user_id,
        "name" => $name,
        "age" => (int)$age,
        "dob" => $dob,
        "mobile" => $mobile,
        "profile_pic" => ""
    ];
    
    $bulk->insert($profile_data);
    $mongo_manager->executeBulkWrite("user_system.profiles", $bulk);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to create profile: " . $e->getMessage()]);
    exit();
}

echo json_encode(["message" => "User registered successfully."]);
?>