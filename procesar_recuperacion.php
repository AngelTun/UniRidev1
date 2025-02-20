<?php
// Habilitar errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexión a la base de datos
require 'conexion.php';

// Incluir PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'lib/phpmailer/src/PHPMailer.php';
require 'lib/phpmailer/src/SMTP.php';
require 'lib/phpmailer/src/Exception.php';

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
            // Crear una instancia de PHPMailer
            $mail = new PHPMailer(true);
            try {
                // Configuración del servidor SMTP de Gmail
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'therapers100@gmail.com';  // Tu correo de Gmail
                $mail->Password = 'qudsagkdkwzulufg'; // Tu contraseña de aplicación de Gmail
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Asegurar que el correo use UTF-8 para caracteres especiales
                $mail->CharSet = 'UTF-8';

                // Destinatario
                $mail->setFrom('no-reply@UniRide.com', 'Recuperación de Contraseña');
                $mail->addAddress($correo);

                // Contenido del correo
                $mail->isHTML(true);
                $mail->Subject = 'Recuperación de Contraseña';
                $mail->Body    = "Hola, <br><br>Tu nueva contraseña temporal es: <strong>$nuevaPassword</strong><br><br>Por favor, inicia sesión y cámbiala lo antes posible.";

                // Depuración
                $mail->SMTPDebug = 2;  
                $mail->Debugoutput = 'html';  

                // Enviar el correo
                if ($mail->send()) {
                    echo "<script>alert('Se ha enviado una nueva contraseña a tu correo.'); window.location.href='InicioSesion.php';</script>";
                } else {
                    echo "<script>alert('Error al enviar el correo: " . $mail->ErrorInfo . "'); window.location.href='recuperar.php';</script>";
                }
            } catch (Exception $e) {
                echo "<script>alert('Error en el envío: " . $mail->ErrorInfo . "'); window.location.href='recuperar.php';</script>";
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
