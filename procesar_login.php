<?php
// Habilitar errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Datos de conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "uniride";

// Crear conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname, 3306);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si los datos fueron enviados
if (isset($_POST['email']) && isset($_POST['password'])) {
    // Recibir y sanitizar datos del formulario
    $correo = trim($_POST['email']);
    $passwordInput = $_POST['password'];

    // Usar Prepared Statements para evitar inyección SQL
    $stmt = $conn->prepare("SELECT nombres, password FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    // Si el usuario existe
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Verificar la contraseña
        if (password_verify($passwordInput, $row['password'])) {
            // Iniciar sesión
            session_start();
            $_SESSION['usuario'] = $row['nombres'];

            // Redirigir al dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            echo "<script>alert('Contraseña incorrecta.'); window.location.href='InicioSesion.php';</script>";
        }
    } else {
        echo "<script>alert('El correo no está registrado.'); window.location.href='InicioSesion.php';</script>";
    }

    // Cerrar la consulta
    $stmt->close();
}

// Cerrar conexión
$conn->close();
?>
