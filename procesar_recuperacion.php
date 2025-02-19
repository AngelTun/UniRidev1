<?php
// Habilitar errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conexión a la base de datos
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $conn->real_escape_string($_POST['email']);

    // Verificar si el correo existe en la base de datos
    $sql = "SELECT * FROM usuarios WHERE correo = '$correo'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Generar nueva contraseña aleatoria
        $nuevaPassword = substr(md5(time()), 0, 8);
        $passwordHash = password_hash($nuevaPassword, PASSWORD_DEFAULT);

        // Actualizar en la base de datos
        $sqlUpdate = "UPDATE usuarios SET password='$passwordHash' WHERE correo='$correo'";
        if ($conn->query($sqlUpdate)) {
            // Enviar correo con la nueva contraseña
            $para = $correo;
            $asunto = "Recuperación de Contraseña";
            $mensaje = "Hola,\n\nTu nueva contraseña temporal es: $nuevaPassword\n\nPor favor, inicia sesión y cámbiala lo antes posible.";
            $cabeceras = "From: no-reply@tu-sitio.com\r\n";
            $cabeceras .= "Content-Type: text/plain; charset=UTF-8\r\n";

            if (mail($para, $asunto, $mensaje, $cabeceras)) {
                echo "<script>alert('Se ha enviado una nueva contraseña a tu correo.'); window.location.href='InicioSesion.php';</script>";
            } else {
                echo "<script>alert('Error al enviar el correo.'); window.location.href='recuperar.php';</script>";
            }
        } else {
            echo "<script>alert('Error al actualizar la contraseña.'); window.location.href='recuperar.php';</script>";
        }
    } else {
        echo "<script>alert('El correo no está registrado.'); window.location.href='recuperar.php';</script>";
    }
}

$conn->close();
?>

