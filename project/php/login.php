<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../db/mysql.php';
require_once '../db/redis.php';

$data = json_decode(file_get_contents("php://input"), true);
$identifier = $data['identifier'] ?? '';
$password = $data['password'] ?? '';

if (empty($identifier) || empty($password)) {
    http_response_code(400);
    echo json_encode(["error" => "Missing credentials."]);
    exit();
}

$stmt = $mysqli->prepare("SELECT id, password FROM users WHERE email = ? OR username = ?");
$stmt->bind_param("ss", $identifier, $identifier);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(401);
    echo json_encode(["error" => "Invalid credentials."]);
    $stmt->close();
    $mysqli->close();
    exit();
}

$user = $result->fetch_assoc();
$stmt->close();
$mysqli->close();

if (!password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode(["error" => "Invalid credentials."]);
    exit();
}

$token = bin2hex(random_bytes(32));

try {
    $redis->setex("session:" . $token, 3600, $user['id']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Redis connection failed: " . $e->getMessage()]);
    exit();
}

echo json_encode([
    "message" => "Login successful",
    "token" => $token
]);
?>