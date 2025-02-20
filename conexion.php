<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "uniride";

$conn = new mysqli($servername, $username, $password, $dbname, 3306);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
