<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Registrar</title>
  <link rel="stylesheet" href="registrar.css" />
</head>
<body>
  <div class="register-container">
    <h2>Registro de Usuario</h2>
    <!-- El action apunta al script del servidor que procesa el registro -->
    <form id="registerForm" action="procesar_registro.php" method="POST">
      <div class="form-group">
        <label for="nombres">Nombres</label>
        <input type="text" id="nombres" name="nombres" placeholder="Ingresar nombres" required>
      </div>
      <div class="form-group">
        <label for="apellidos">Apellidos</label>
        <input type="text" id="apellidos" name="apellidos" placeholder="Ingresar apellidos" required>
      </div>
      <div class="form-group">
        <label for="matricula">Matrícula</label>
        <input type="text" id="matricula" name="matricula" placeholder="Ingresar matrícula" required>
      </div>
      <div class="form-group">
        <label for="correo">Correo</label>
        <input type="email" id="correo" name="correo" placeholder="Ingresar correo" required>
      </div>
      <div class="form-group">
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" placeholder="Ingresar contraseña" required minlength="8">
      </div>
      <div class="form-group">
        <label for="confirm_password">Confirmar Contraseña</label>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirmar contraseña" required minlength="8">
      </div>
      <button type="submit" class="btn-register">Confirmar Registro</button>
    </form>
  </div>

  <!-- Validación básica para que las contraseñas coincidan -->
  <script>
    document.getElementById("registerForm").addEventListener("submit", function(e) {
      const password = document.getElementById("password").value;
      const confirmPassword = document.getElementById("confirm_password").value;
      if (password !== confirmPassword) {
        e.preventDefault();
        alert("Las contraseñas no coinciden. Por favor, verifica.");
      }
    });
  </script>
</body>
</html>
