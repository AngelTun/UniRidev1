<?php
// Configuración de la base de datos
$servername = "localhost";
$username = "root"; // Ajusta según tu configuración
$password = ""; // Añade tu contraseña si tienes
$database = "UniRide";

// Conexión mysqli
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Error de conexión (mysqli): " . $conn->connect_error);
}

// Conexión PDO
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$database;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión (PDO): " . $e->getMessage());
}
?>
