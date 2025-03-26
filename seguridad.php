<?php 
session_start();

// Verifica si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    echo "<p id='mensajeError' style='color: red;'>Debe iniciar sesión para ver esta página.</p>";
    exit;
}

require_once 'conexion.php';

$correo_usuario = $_SESSION['usuario'];

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE correo = ?");
$stmt->execute([$correo_usuario]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "<p id='mensajeError' style='color: red;'>No se encontró el usuario con el correo: $correo_usuario.</p>";
    exit;
}

// Cambio de contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $old_password     = $_POST['old_password'] ?? '';
    $new_password     = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $errors = [];

    if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
        $errors[] = "Debe completar todos los campos de contraseña.";
    } else {
        if (!password_verify($old_password, $user['password'])) {
            $errors[] = "La contraseña actual es incorrecta.";
        }
        if ($new_password !== $confirm_password) {
            $errors[] = "La nueva contraseña y su confirmación no coinciden.";
        }
        if (strlen($new_password) < 8) {
            $errors[] = "La contraseña debe tener al menos 8 caracteres.";
        }
        if (!preg_match('/[A-Z]/', $new_password)) {
            $errors[] = "La contraseña debe contener al menos una letra mayúscula.";
        }
        if (!preg_match('/[0-9]/', $new_password)) {
            $errors[] = "La contraseña debe contener al menos un número.";
        }
    }

    if (empty($errors)) {
        $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE correo = ?");
        $updated = $stmt->execute([$new_password_hash, $correo_usuario]);

        if ($updated) {
            echo "<p id='mensajeExito' style='color: green;'>Contraseña actualizada correctamente.</p>";
        } else {
            echo "<p id='mensajeError' style='color: red;'>Error al actualizar la contraseña.</p>";
        }
    } else {
        echo "<p id='mensajeError' style='color: red;'>" . implode("<br>", $errors) . "</p>";
    }
    exit;
}
?>

<div class="security-page">
    <div class="security-container">
        <!-- Contenedor para mostrar los mensajes encima del contenido del formulario -->
        <div id="msgContainer"></div>

        <h2 class="main-title">Cambiar Contraseña</h2>

        <!-- Formulario de cambio de contraseña -->
        <form id="formSeguridad" method="post">
            <input type="hidden" name="change_password" value="1">

            <div class="form-group">
                <label for="old_password">Contraseña actual *</label>
                <div class="password-container">
                    <input type="password" id="old_password" name="old_password" required>
                    <button type="button" class="toggle-password" data-target="old_password">👁️</button>
                </div>
            </div>

            <div class="form-group">
                <label for="new_password">Nueva contraseña *</label>
                <div class="password-container">
                    <input type="password" id="new_password" name="new_password" required>
                    <button type="button" class="toggle-password" data-target="new_password">👁️</button>
                </div>
                <small class="hint">La contraseña debe tener al menos 8 caracteres, incluir una mayúscula y un número.</small>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirmar nueva contraseña *</label>
                <div class="password-container">
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <button type="button" class="toggle-password" data-target="confirm_password">👁️</button>
                </div>
            </div>

            <button type="button" class="btn-confirm" onclick="enviarFormularioSeguridad()">Confirmar</button>
        </form>
    </div>
</div>
