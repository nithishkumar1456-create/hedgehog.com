<?php
$mongo_uri = getenv("MONGO_URI") !== false ? getenv("MONGO_URI") : "mongodb+srv://iglcyborg143_db_user:tfG26jvGKFiTXkMQ@cluster0.nay1qeh.mongodb.net/user_system";

try {
    $mongo_manager = new MongoDB\Driver\Manager($mongo_uri);
} catch (Exception $e) {
    http_response_code(500);
    die(json_encode(["error" => "MongoDB Connection Failed: " . $e->getMessage()]));
}
?>