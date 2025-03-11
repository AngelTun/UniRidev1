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
    $nombres   = trim($_POST['nombres']);
    $apellidos = trim($_POST['apellidos']);
    $matricula = trim($_POST['matricula']);
    $correo    = trim($_POST['correo']);

    // Actualizar solo los datos personales
    $stmt = $pdo->prepare("UPDATE usuarios SET nombres = ?, apellidos = ?, matricula = ?, correo = ? WHERE correo = ?");
    $updated = $stmt->execute([
        $nombres, $apellidos, $matricula, $correo, $correo_usuario
    ]);

    if ($updated) {
        // Actualizar el correo en la sesión si se cambió
        if ($correo !== $correo_usuario) {
            $_SESSION['usuario'] = $correo; // Actualiza la sesión con el nuevo correo
            $correo_usuario = $correo;
            }
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE correo = ?");
        $stmt->execute([$correo_usuario]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user){
        echo "<p id='mensajeExito' style='color: green;'>Perfil actualizado correctamente.</p>";
    } else {
        echo "<p id='mensajeError' style='color: red;'>Error al actualizar el perfil.</p>";
    }
}
}
?>

<div>
    <h2 class="main-title">Perfil</h2>

    <div id="contentContainer"></div>

    <form id="formPerfil" method="post">
        <input type="hidden" name="update" value="1">
        <fieldset style="margin-bottom: 1rem;">
            <legend><strong>Datos Personales</strong></legend>
            <label for="nombres">Nombres:</label><br>
            <input type="text" id="nombres" name="nombres" value="<?php echo htmlspecialchars($user['nombres']); ?>" required><br>

            <label for="apellidos">Apellidos:</label><br>
            <input type="text" id="apellidos" name="apellidos" value="<?php echo htmlspecialchars($user['apellidos']); ?>" required><br>

            <label for="matricula">Matrícula:</label><br>
            <input type="text" id="matricula" name="matricula" value="<?php echo htmlspecialchars($user['matricula']); ?>" required><br>

            <label for="correo">Correo:</label><br>
            <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($user['correo']); ?>" required><br>
        </fieldset>

        <button type="button" onclick="enviarFormularioPerfil()">Actualizar Perfil</button>
    </form>
</div>

<script>
function enviarFormularioPerfil() {
    const form = document.getElementById('formPerfil');
    const formData = new FormData(form);

    fetch('perfil.php', {
        method: 'POST',
        body: formData,
        credentials: 'include'
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById('contentContainer').innerHTML = data;
    })
    .catch(error => {
        console.error("Error en la solicitud fetch:", error);
    });
}
</script>
