<?php
$servername = "monorail.proxy.rlwy.net";
$username = "root";
$password = "caWwMDivmKxSvWSRDhxjhwFcuWfDBJzJ";
$dbname = "railway";
$port = 22980;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>