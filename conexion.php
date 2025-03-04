
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "uniride";

// Crear una nueva conexión PDO
try {
    // Establecemos la conexión utilizando PDO
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    
    // Establecemos el modo de error de PDO a excepciones
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Si hay un error, lo mostramos
    die("Error de conexión: " . $e->getMessage());
}
?>
