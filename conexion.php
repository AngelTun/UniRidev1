<?php
// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$database = "UniRide";

// Conexión mysqli
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Error de conexión (mysqli): ". $conn->connect_error);
}

// Conexión PDO
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error de conexión (PDO): ". $e->getMessage());
}

// Función para depuración
function debug_db($var) {
    echo '<pre>';
    print_r($var);
    echo '</pre>';
}
?>
