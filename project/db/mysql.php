<?php
$mysql_host = getenv("MYSQLHOST") !== false ? getenv("MYSQLHOST") : "gondola.proxy.rlwy.net";
$mysql_port = getenv("MYSQLPORT") !== false ? getenv("MYSQLPORT") : 45986;
$mysql_user = getenv("MYSQLUSER") !== false ? getenv("MYSQLUSER") : "root";
$mysql_pass = getenv("MYSQLPASSWORD") !== false ? getenv("MYSQLPASSWORD") : "DEXLvQAIknfwXuVjiXCArfrWKWSbWNtv";
$mysql_db   = getenv("MYSQLDATABASE") !== false ? getenv("MYSQLDATABASE") : "railway";

$mysqli = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db, $mysql_port);

if ($mysqli->connect_error) {
    http_response_code(500);
    die(json_encode(["error" => "MySQL Connection Failed: " . $mysqli->connect_error]));
}
?>