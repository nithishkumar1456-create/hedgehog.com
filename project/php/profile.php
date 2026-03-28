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
require_once '../db/redis.php';

// Try standard apache_request_headers or fallback
$headers = [];
if (function_exists('apache_request_headers')) {
    $headers = apache_request_headers();
} else {
    $headers = $_SERVER;
    // Map HTTP_AUTHORIZATION to Authorization
    if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $headers['Authorization'] = $_SERVER['HTTP_AUTHORIZATION'];
    }
}

$authHeader = $headers['Authorization'] ?? '';

if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized: Token missing."]);
    exit();
}

$token = $matches[1];

try {
    $user_id = $redis->get("session:" . $token);
    if (!$user_id) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized: Invalid or expired token."]);
        exit();
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Redis connection failed: " . $e->getMessage()]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $mysqli->prepare("SELECT username, email FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $mysql_user_data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $mysqli->close();

    try {
        $filter = ['user_id' => (int)$user_id];
        $query = new MongoDB\Driver\Query($filter);
        $cursor = $mongo_manager->executeQuery("user_system.profiles", $query);
        $profile_data = current($cursor->toArray());

        if (!$profile_data) {
            http_response_code(404);
            echo json_encode(["error" => "Profile not found."]);
            exit();
        }

        echo json_encode([
            "user" => $mysql_user_data,
            "profile" => $profile_data
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "MongoDB error: " . $e->getMessage()]);
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (!$data) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid input data."]);
        exit();
    }

    $name = $data['name'] ?? null;
    $age = $data['age'] ?? null;
    $mobile = $data['mobile'] ?? null;
    $password = $data['password'] ?? null;

    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $mysqli->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $user_id);
        $stmt->execute();
        $stmt->close();
    }
    $mysqli->close();

    try {
        $bulk = new MongoDB\Driver\BulkWrite;
        $set_data = [];
        if ($name !== null) $set_data['name'] = $name;
        if ($age !== null) $set_data['age'] = (int)$age;
        if ($mobile !== null) $set_data['mobile'] = $mobile;

        if (!empty($set_data)) {
            $bulk->update(
                ['user_id' => (int)$user_id],
                ['$set' => $set_data],
                ['multi' => false, 'upsert' => false]
            );
            $mongo_manager->executeBulkWrite("user_system.profiles", $bulk);
        }
        
        echo json_encode(["message" => "Profile updated successfully."]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Failed to update profile: " . $e->getMessage()]);
    }
}
?>