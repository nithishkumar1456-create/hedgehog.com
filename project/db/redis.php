<?php
$redis_host = getenv("REDIS_HOST") !== false ? getenv("REDIS_HOST") : "powerful-anemone-38915.upstash.io";
$redis_port = getenv("REDIS_PORT") !== false ? getenv("REDIS_PORT") : 6379;
$redis_pass = getenv("REDIS_PASSWORD") !== false ? getenv("REDIS_PASSWORD") : "AZgDAAIncDExMjNiOGUxNWMwM2E0MDU5OWVlODBjNTEwMDhmNTk2Y3AxMzg5MTU";

try {
    $redis = new Redis();
    // Upstash requires TLS for external connections
    $redis->connect("tls://" . $redis_host, $redis_port);
    $redis->auth($redis_pass);
} catch (Exception $e) {
    http_response_code(500);
    die(json_encode(["error" => "Redis Connection Failed: " . $e->getMessage()]));
}
?>