<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Registrar</title>
  <link rel="stylesheet" href="registrar.css" />
  <style>
    /* Puedes agregar o ajustar estos estilos para el botón toggle */
    .password-group {
      position: relative;
    }
    .toggle-password {
      position: absolute;
      right: 10px;
      top: 43px;
      transform: translateY(-50%);
      background: none;
      border: none;
      cursor: pointer;
      font-size: 0.9rem;
      color: #ffd500;
    }

    /* Estilo para el mensaje de error */
    .password-error {
      color: #e74c3c;
      font-size: 0.9rem;
      margin-top: 5px;
    }
  </style>
</head>
<body>
  <div class="register-container">
    <h2>Registro de Usuario</h2>
    <!-- El action apunta al script del servidor que procesa el registro -->
    <form id="registerForm" action="procesar_registro.php" method="POST">
      <div class="form-group">
        <label for="nombres">Nombres</label>
        <input type="text" id="nombres" name="nombres" placeholder="Ingresar nombres (Obligatorio)*" required>
      </div>
      <div class="form-group">
        <label for="apellidos">Apellidos</label>
        <input type="text" id="apellidos" name="apellidos" placeholder="Ingresar apellidos (Obligatorio)*" required>
      </div>
      <div class="form-group">
        <label for="matricula">Matrícula</label>
        <input type="text" id="matricula" name="matricula" placeholder="Ingresar matrícula (Obligatorio)*" required>
      </div>
      <div class="form-group">
        <label for="correo">Correo</label>
        <input type="email" id="correo" name="correo" placeholder="Ingresar correo (Obligatorio)*" required>
      </div>
      <div class="form-group password-group">
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" placeholder="Ingresar contraseña (Obligatorio)*" required minlength="8">
        <button type="button" class="toggle-password" data-target="password">Mostrar</button>
        <div id="passwordError" class="password-error"></div>
      </div>
      <div class="form-group password-group">
        <label for="confirm_password">Confirmar Contraseña</label>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirmar contraseña (Obligatorio)*" required minlength="8">
        <button type="button" class="toggle-password" data-target="confirm_password">Mostrar</button>
      </div>
      <button type="submit" class="btn-register">Confirmar Registro</button>
    </form>
  </div>

  <!-- Validación básica para que las contraseñas coincidan y nueva validación en tiempo real -->
  <script>
    // Validación al enviar el formulario
    document.getElementById("registerForm").addEventListener("submit", function(e) {
      const password = document.getElementById("password").value;
      const confirmPassword = document.getElementById("confirm_password").value;
      if (password !== confirmPassword) {
        e.preventDefault();
        alert("Las contraseñas no coinciden. Por favor, verifica.");
      }
    });

    // Función para mostrar/ocultar contraseña mediante delegación de eventos
    document.addEventListener("click", function(e) {
      if (e.target && e.target.classList.contains("toggle-password")) {
        const targetId = e.target.getAttribute("data-target");
        const passwordInput = document.getElementById(targetId);
        if (passwordInput) {
          if (passwordInput.type === "password") {
            passwordInput.type = "text";
            e.target.textContent = "Ocultar";
          } else {
            passwordInput.type = "password";
            e.target.textContent = "Mostrar";
          }
        }
      }
    });

    // Validación en tiempo real para el campo de contraseña
    document.getElementById("password").addEventListener("input", function() {
      const password = this.value;
      const hasUpperCase = /[A-Z]/.test(password);
      const hasNumber = /[0-9]/.test(password);
      const isValid = password.length >= 8 && hasUpperCase && hasNumber;

      // Mostrar mensaje de error si no se cumple la restricción
      const errorElement = document.getElementById("passwordError");
      if (password.length > 0 && !isValid) {
        errorElement.textContent = "La contraseña debe tener al menos 8 caracteres, incluir una mayúscula y un número.";
      } else {
        errorElement.textContent = "";
      }

      // Cambiar el color del borde según la validez de la contraseña
      if (password.length > 0) {
        this.style.borderColor = isValid ? "#27ae60" : "#e74c3c";
      } else {
        this.style.borderColor = "#ddd";
      }
    });
  </script>
</body>
</html>
