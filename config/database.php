<?php
$servername = "db";
$username = "root";
$password = "root";
$dbname = "truyentranh";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>
