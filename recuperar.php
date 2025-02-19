<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recuperar Contraseña</title>
  <link rel="stylesheet" href="recuperar.css">
</head>
<body>
  <div class="login-container">
    <h2>Recuperar Contraseña</h2>
    <form id="recoverForm" action="procesar_recuperacion.php" method="POST">
      <div class="form-group">
        <label for="email">Correo electrónico</label>
        <input type="email" id="email" name="email" placeholder="Ingresa tu correo" required>
      </div>
      <button type="submit" class="btn-recover">Enviar</button>
    </form>
  </div>

  <script>
    document.getElementById("recoverForm").addEventListener("submit", function(event) {
      const emailInput = document.getElementById("email");
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(emailInput.value)) {
        event.preventDefault();
        alert("Por favor, introduce un correo electrónico válido.");
      }
    });
  </script>
</body>
</html>
