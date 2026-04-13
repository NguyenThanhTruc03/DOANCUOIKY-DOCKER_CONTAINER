<?php
$servername = "containers-xxx.railway.app";
$username = "root";
$password = "PASSWORD_MOI";
$dbname = "railway";

$conn = new mysqli($servername, $username, $password, $dbname, 3306);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>