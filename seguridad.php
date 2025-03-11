<?php
session_start();

// Verifica si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    echo "<p>Debe iniciar sesión para ver esta página.</p>";
    exit;
}

require_once 'conexion.php';

$correo_usuario = $_SESSION['usuario'];

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE correo = ?");
$stmt->execute([$correo_usuario]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "<p>No se encontró el usuario con el correo: $correo_usuario.</p>";
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
            $errors[] = "La contraseña antigua es incorrecta.";
        }
        if ($new_password !== $confirm_password) {
            $errors[] = "La nueva contraseña y su confirmación no coinciden.";
        }
    }

    if (empty($errors)) {
        $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE correo = ?");
        $updated = $stmt->execute([$new_password_hash, $correo_usuario]);

        echo $updated ? "<p style='color: green;'>Contraseña actualizada correctamente.</p>" : "<p style='color: red;'>Error al actualizar la contraseña.</p>";
    } else {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    }
}
?>

<div>
    <h2 class="main-title">Cambiar Contraseña</h2>

    <form id="formSeguridad" method="post">
        <input type="hidden" name="change_password" value="1">

        <label for="old_password">Contraseña antigua:</label><br>
        <input type="password" id="old_password" name="old_password"><br>

        <label for="new_password">Nueva Contraseña:</label><br>
        <input type="password" id="new_password" name="new_password"><br>

        <label for="confirm_password">Confirmar Nueva Contraseña:</label><br>
        <input type="password" id="confirm_password" name="confirm_password"><br>

        <button type="submit">Actualizar Contraseña</button>
    </form>
</div>
