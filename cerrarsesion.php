<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    echo "<p>Debe iniciar sesión para ver esta página.</p>";
    exit;
}
// Si la solicitud es POST, manejar la acción de cerrar sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion'])) {
        if ($_POST['accion'] === 'si') {
            // Cerrar la sesión
            session_unset(); // Elimina todas las variables de sesión
            session_destroy(); // Destruye la sesión

            // Eliminar la cookie de sesión en el navegador
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }

            // Redirigir al usuario a la página de inicio de sesión
            header('Location: iniciosesion.php');
            exit(); // Detener la ejecución
        } else {
            // Si elige "No", redirigir al dashboard
            header('Location: dashboard.php');
            exit(); // Detener la ejecución
        }
    }
}
?>



<section id="cerrarSesion" class="p-6 bg-white rounded-lg shadow-md max-w-md mx-auto mt-10">
    <h2 class="text-2xl font-bold mb-4">¿Deseas cerrar sesión?</h2>
    <form method="POST" action="cerrarSesion.php">
        <div class="flex justify-between">
            <button type="submit" name="accion" value="si" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">Sí</button>
            <button type="submit" name="accion" value="no" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">No</button>
        </div>
    </form>
</section>
