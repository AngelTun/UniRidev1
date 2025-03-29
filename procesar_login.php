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

    // Verificar si ya hay una sesión activa para este usuario
    $checkSession = $conn->prepare("SELECT session_id FROM usuarios WHERE correo = ?");
    $checkSession->bind_param("s", $correo);
    $checkSession->execute();
    $sessionResult = $checkSession->get_result();
    
    if ($sessionResult->num_rows > 0) {
        $sessionRow = $sessionResult->fetch_assoc();
        if (!empty($sessionRow['session_id'])) {
            echo "<script>alert('Ya hay una sesión activa con este usuario en otro dispositivo.'); window.location.href='iniciosesion.php';</script>";
            exit;
        }
    }

    // Usar Prepared Statements para evitar inyección SQL
    $stmt = $conn->prepare("SELECT id, nombres, correo, password FROM usuarios WHERE correo = ?");
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
            $_SESSION['usuario'] = $row['correo'];
            
            // Actualizar el session_id en la base de datos
            $updateSession = $conn->prepare("UPDATE usuarios SET session_id = ? WHERE correo = ?");
            $sessionId = session_id();
            $updateSession->bind_param("ss", $sessionId, $row['correo']);
            $updateSession->execute();
            $updateSession->close();

            // Redirigir al perfil
            header("Location: dashboard.php");
            exit();
        } else {
            echo "<script>alert('Contraseña incorrecta.'); window.location.href='iniciosesion.php';</script>";
        }
    } else {
        echo "<script>alert('El correo no está registrado.'); window.location.href='iniciosesion.php';</script>";
    }

    // Cerrar la consulta
    $stmt->close();
}

// Cerrar conexión
$conn->close();
?>
