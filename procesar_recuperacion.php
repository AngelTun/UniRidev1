<?php
// Habilitar errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
                // Configuración del servidor SMTP
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';  // Usa tu servidor SMTP
                $mail->SMTPAuth = true;
                $mail->Username = 'agonzaleztun@gmail.com';  // Tu correo
                $mail->Password = 'cmbomkxfewscezzn';  // Tu contraseña
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Destinatario
                $mail->setFrom('no-reply@UniRide.com', 'Recuperación de Contraseña');
                $mail->addAddress($correo);     // Agregar destinatario

                // Contenido del correo
                $mail->isHTML(true);
                $mail->Subject = 'Recuperación de Contraseña';
                $mail->Body    = "Hola, <br><br>Tu nueva contraseña temporal es: <strong>$nuevaPassword</strong><br><br>Por favor, inicia sesión y cámbiala lo antes posible.";

                // Habilitar depuración para ver detalles del envío
                $mail->SMTPDebug = 3;  // 0 = Desactivado, 1 = Errores, 2 = Errores y mensajes detallados
                $mail->Debugoutput = 'html';  // Para mostrar la salida en formato HTML

                // Enviar el correo
                if ($mail->send()) {
                    echo "<script>alert('Se ha enviado una nueva contraseña a tu correo.'); window.location.href='InicioSesion.php';</script>";
                } else {
                    echo "<script>alert('Error al enviar el correo.'); window.location.href='recuperar.php';</script>";
                }
            } catch (Exception $e) {
                echo "<script>alert('Error al enviar el correo: " . $mail->ErrorInfo . "'); window.location.href='recuperar.php';</script>";
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
