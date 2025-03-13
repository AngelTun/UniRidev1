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

    // Validar que no haya campos vacíos después de trim()
    if (empty($nombres) || empty($apellidos) || empty($matricula) || empty($correo)) {
        echo "<p id='mensajeError' style='color: red;'>Error: Todos los campos son obligatorios. No se permiten espacios en blanco.</p>";
    } else {
        // Validar formato de correo electrónico
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            echo "<p id='mensajeError' style='color: red;'>Error: El formato del correo electrónico no es válido.</p>";
        } else {
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

                if ($user) {
                    echo "<p id='mensajeExito' style='color: green;'>Perfil actualizado correctamente.</p>";
                } else {
                    echo "<p id='mensajeError' style='color: red;'>Error al actualizar el perfil.</p>";
                }
            }
        }
    }
}
?>
    <div>
        <h2 class="main-title">Perfil</h2>

        <div id="contentContainer">
            <?php if (isset($mensajeExito)) echo $mensajeExito; ?>
            <?php if (isset($mensajeError)) echo $mensajeError; ?>
        </div>

        <form id="formPerfil" method="post">
            <input type="hidden" name="update" value="1">
            <fieldset style="margin-bottom: 1rem;">
                <legend><strong>Datos Personales</strong></legend>
                <label for="nombres">Nombres</label><br>
                <input type="text" id="nombres" name="nombres" value="<?php echo htmlspecialchars($user['nombres']); ?>" placeholder="Campo obligatorio. No dejar vacío" required><br>

                <label for="apellidos">Apellidos</label><br>
                <input type="text" id="apellidos" name="apellidos" value="<?php echo htmlspecialchars($user['apellidos']); ?>" placeholder="Campo obligatorio. No dejar vacío" required><br>

                <label for="matricula">Matrícula</label><br>
                <input type="text" id="matricula" name="matricula" value="<?php echo htmlspecialchars($user['matricula']); ?>" placeholder="Campo obligatorio. No dejar vacío" required><br>

                <label for="correo">Correo</label><br>
                <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($user['correo']); ?>" placeholder="Campo obligatorio. No dejar vacío e insertar correo válido"  required><br>
            </fieldset>

            <button type="button" onclick="enviarFormularioPerfil()">Actualizar Perfil</button>
        </form>
    </div>

    <script>
    function validarFormulario() {
        const nombres = document.getElementById('nombres').value.trim();
        const apellidos = document.getElementById('apellidos').value.trim();
        const matricula = document.getElementById('matricula').value.trim();
        const correo = document.getElementById('correo').value.trim();

        // Validar espacios en blanco
        if (nombres === '' || apellidos === '' || matricula === '' || correo === '') {
            alert('Por favor, complete todos los campos. No se permiten espacios en blanco.');
            return false;
        }

        // Validar formato de correo electrónico
        const regexCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!regexCorreo.test(correo)) {
            alert('Por favor, ingrese un correo electrónico válido.');
            return false;
        }

        return true;
    }

    function enviarFormularioPerfil() {
        if (!validarFormulario()) {
            return; // Detener el envío si la validación falla
        }

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
