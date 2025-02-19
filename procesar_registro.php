<?php
// Habilitar errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Datos de conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "uniride";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname, 3306);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si los datos fueron enviados
if (isset($_POST['nombres'], $_POST['apellidos'], $_POST['matricula'], $_POST['correo'], $_POST['password'], $_POST['confirm_password'])) {
    // Recibir y sanitizar datos del formulario
    $nombres = htmlspecialchars($conn->real_escape_string($_POST['nombres']));
    $apellidos = htmlspecialchars($conn->real_escape_string($_POST['apellidos']));
    $matricula = htmlspecialchars($conn->real_escape_string($_POST['matricula']));
    $correo = htmlspecialchars($conn->real_escape_string($_POST['correo']));
    $passwordInput = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Verificar que las contraseñas coincidan
    if ($passwordInput !== $confirmPassword) {
        echo "<script>alert('Las contraseñas no coinciden.'); window.location.href='registrar.php';</script>";
        exit();
    }

    // Encriptar la contraseña
    $passwordHash = password_hash($passwordInput, PASSWORD_DEFAULT);

    // Verificar si el correo ya está registrado (Prepared Statements para seguridad)
    $stmt = $conn->prepare("SELECT correo FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('El correo ya está registrado. Intenta con otro.'); window.location.href='registrar.php';</script>";
        exit();
    }

    $stmt->close();

    // Insertar datos en la base de datos con Prepared Statements
    $stmt = $conn->prepare("INSERT INTO usuarios (nombres, apellidos, matricula, correo, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nombres, $apellidos, $matricula, $correo, $passwordHash);

    if ($stmt->execute()) {
        // Registro exitoso, redirigir a InicioSesion.html
        echo "<script>alert('Registro exitoso. Inicia sesión.'); window.location.href='InicioSesion.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error en el registro: " . $stmt->error . "'); window.location.href='registrar.php';</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Error: Formulario incompleto.'); window.location.href='registrar.php';</script>";
}

// Cerrar conexión
$conn->close();
?>
