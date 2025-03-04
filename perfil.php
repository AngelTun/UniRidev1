<?php
session_start();

// Verifica si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    echo "<p>Debe iniciar sesión para ver esta página.</p>";
    exit;
}

require_once 'conexion.php'; // Asegúrate de que el archivo 'conexion.php' esté en la misma carpeta

// Obtener los datos del usuario actual usando su correo
$correo_usuario = $_SESSION['usuario']; // Ahora obtenemos el correo desde la sesión

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE correo = ?");
$stmt->execute([$correo_usuario]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "<p>No se encontró el usuario con el correo: $correo_usuario.</p>";
    exit;
}

// Si se envía el formulario para actualizar perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    // Recoger datos del formulario
    $nombres          = trim($_POST['nombres']);
    $apellidos        = trim($_POST['apellidos']);
    $matricula        = trim($_POST['matricula']);
    $correo           = trim($_POST['correo']);
    $old_password     = $_POST['old_password'] ?? '';
    $new_password     = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $errors = [];

    // Verificar si se desea cambiar la contraseña
    if (!empty($old_password) || !empty($new_password) || !empty($confirm_password)) {
        // Deben completarse todos los campos de contraseña
        if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
            $errors[] = "Para cambiar la contraseña, debe completar todos los campos de contraseña.";
        } else {
            // Validar que la contraseña antigua coincida con la almacenada
            if (!password_verify($old_password, $user['password'])) {
                $errors[] = "La contraseña antigua es incorrecta.";
            }
            // Validar que la nueva contraseña y su confirmación coincidan
            if ($new_password !== $confirm_password) {
                $errors[] = "La nueva contraseña y su confirmación no coinciden.";
            }
        }
    }

    if (empty($errors)) {
        // Si se cambió la contraseña
        if (!empty($new_password)) {
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE usuarios 
                                   SET nombres = ?, apellidos = ?, matricula = ?, correo = ?, password = ? 
                                   WHERE correo = ?");
            $updated = $stmt->execute([
                $nombres, $apellidos, $matricula, $correo, $new_password_hash, $correo_usuario
            ]);
        } else {
            // Actualizar solo los datos personales
            $stmt = $pdo->prepare("UPDATE usuarios 
                                   SET nombres = ?, apellidos = ?, matricula = ?, correo = ? 
                                   WHERE correo = ?");
            $updated = $stmt->execute([
                $nombres, $apellidos, $matricula, $correo, $correo_usuario
            ]);
        }

        if ($updated) {
            // Redirigir al mismo perfil después de la actualización
            header("Location: dashboard.php?status=success");
            exit;
        } else {
            echo "<p style='color:red;'>Error al actualizar el perfil.</p>";
        }
    } else {
        // Mostrar errores
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    }
}
?>


<div>
    <h2 class="main-title">Perfil</h2>

    <!-- Mostrar el mensaje de éxito o error -->
    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] === 'success'): ?>
            <p style="color: green;">Perfil actualizado correctamente.</p>
        <?php else: ?>
            <p style="color: red;">Hubo un error al actualizar el perfil. Intenta de nuevo.</p>
        <?php endif; ?>
    <?php endif; ?>

    <form method="post" action="perfil.php">
        <fieldset style="margin-bottom: 1rem;">
            <legend><strong>Datos Personales</strong></legend>
            <label for="nombres">Nombres:</label><br>
            <input type="text" id="nombres" name="nombres" 
                   value="<?php echo htmlspecialchars($user['nombres']); ?>" required><br>

            <label for="apellidos">Apellidos:</label><br>
            <input type="text" id="apellidos" name="apellidos" 
                   value="<?php echo htmlspecialchars($user['apellidos']); ?>" required><br>

            <label for="matricula">Matrícula:</label><br>
            <input type="text" id="matricula" name="matricula" 
                   value="<?php echo htmlspecialchars($user['matricula']); ?>" required><br>

            <label for="correo">Correo:</label><br>
            <input type="email" id="correo" name="correo" 
                   value="<?php echo htmlspecialchars($user['correo']); ?>" required><br>
        </fieldset>

        <fieldset style="margin-bottom: 1rem;">
            <legend><strong>Cambiar Contraseña</strong></legend>
            <label for="old_password">Contraseña antigua:</label><br>
            <input type="password" id="old_password" name="old_password"><br>

            <label for="new_password">Nueva Contraseña:</label><br>
            <input type="password" id="new_password" name="new_password"><br>

            <label for="confirm_password">Confirmar Nueva Contraseña:</label><br>
            <input type="password" id="confirm_password" name="confirm_password"><br>
        </fieldset>

        <button type="submit" name="update">Actualizar Perfil</button>
    </form>
</div>
