<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <link rel="stylesheet" href="login.css" />
</head>
<body>
  <div class="login-container">
    <!-- Logo de la app -->
    <div class="logo-container">
      <img src="Images/image.png" alt="Logo de la app" class="logo" />
    </div>

    <!-- Formulario de Login -->
    <form id="loginForm" action="procesar_login.php" method="POST">
      <!-- Campo de correo -->
      <div class="form-group">
        <label for="email">Correo</label>
        <input
          type="email"
          id="email"
          name="email"
          placeholder="Correo electrónico"
          required
        />
      </div>

      <!-- Campo de contraseña con botón para mostrar/ocultar -->
      <div class="form-group password-group">
        <label for="password">Contraseña</label>
        <input
          type="password"
          id="password"
          name="password"
          placeholder="Contraseña"
          required
          minlength="8"
        />
        <button type="button" id="togglePassword" class="toggle-password">
          Mostrar
        </button>
      </div>

      <!-- Botón de inicio de sesión -->
      <button type="submit" class="btn-login">Iniciar Sesión</button>

      <!-- Botones de recuperar contraseña y registrarse -->
      <div class="aux-buttons">
        <button
          type="button"
          class="btn-recover"
          onclick="window.location.href='recuperar.php'"
        >
          Recuperar Contraseña
        </button>
        <button
          type="button"
          class="btn-register"
          onclick="window.location.href='registrar.php'"
        >
          Registrarse
        </button>
      </div>
    </form>
  </div>

  <!-- Script para validación y toggle de contraseña -->
  <script>
    // Mostrar/Ocultar contraseña
    document.getElementById("togglePassword").addEventListener("click", function () {
      const passwordInput = document.getElementById("password");
      if (passwordInput.type === "password") {
        passwordInput.type = "text";
        this.textContent = "Ocultar";
      } else {
        passwordInput.type = "password";
        this.textContent = "Mostrar";
      }
    });

    // Validación básica del formulario antes de enviarlo
    document.getElementById("loginForm").addEventListener("submit", function (event) {
      const emailInput = document.getElementById("email");
      const passwordInput = document.getElementById("password");

      // Validación de correo (expresión regular básica)
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(emailInput.value)) {
        event.preventDefault();
        alert("Por favor, introduce un correo electrónico válido.");
      }

      // Validación de la longitud de la contraseña
      if (passwordInput.value.length < 8) {
        event.preventDefault();
        alert("La contraseña debe tener al menos 8 caracteres.");
      }
      // Si pasa las validaciones, se envía el formulario a BD/procesar_login.php
    });
  </script>
</body>
</html>
