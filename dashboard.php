<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard - Compartir Viajes</title>
  <!-- TailwindCSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- API de Google Maps -->
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB-VtkPeG2cL2SjoAIufnNf39U-RA0qQRc"></script>
  <!-- Nuestro CSS personalizado -->
  <link rel="stylesheet" href="dashboard.css" />
  <link rel="stylesheet" href="perfil.css" />
  <link rel="stylesheet" href="seguridad.css" />
  <link rel="stylesheet" href="cerrarsesion.css" />
  <script>
    // Inicializa el mapa para la sección "Inicio"
    function initMap() {
      var map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: 20.9671, lng: -89.6236 },
        zoom: 12
      });
      var marker = new google.maps.Marker({
        position: { lat: 20.9671, lng: -89.6236 },
        map: map,
        title: "Ubicación del Conductor"
      });
    }

    // Función para cambiar de sección y cargar contenido externo si es necesario
    function changeSection(page) {
      // Quitar la clase 'active' de todos los enlaces
      document.querySelectorAll('.sidebar-link').forEach(link => {
        link.classList.remove('active');
      });
      // Agregar 'active' al enlace seleccionado
      document.querySelector(`.sidebar-link[data-page="${page}"]`).classList.add('active');

      // Si se selecciona "Inicio", mostramos el contenido integrado
      if(page === "Inicio") {
        document.getElementById('contentInicio').classList.remove('hidden');
        document.getElementById('contentContainer').classList.add('hidden');
        initMap();
      } else {
        // Ocultar el contenido integrado de "Inicio"
        document.getElementById('contentInicio').classList.add('hidden');
        // Cargar el contenido del archivo PHP externo en contentContainer
        fetch(page + ".php", { credentials: 'include' })
          .then(response => response.text())
          .then(data => {
            document.getElementById('contentContainer').innerHTML = data;
            document.getElementById('contentContainer').classList.remove('hidden');
          })
          .catch(error => {
            document.getElementById('contentContainer').innerHTML = "<p>Error al cargar el contenido.</p>";
            document.getElementById('contentContainer').classList.remove('hidden');
          });
      }
    }

    // Al cargar la página, mostrar por defecto la sección "Inicio"
    window.addEventListener("DOMContentLoaded", function() {
      changeSection("Inicio");
    });
  </script>
</head>
<body class="bg-gray-100">
  <!-- Encabezado fijo -->
  <header class="header fixed top-0 left-0 right-0 z-50 bg-white shadow-md">
    <div class="container mx-auto flex items-center px-4 py-2">
      <button id="toggleSidebar" class="toggle-btn">☰</button>
      <div class="logo-container ml-4">
        <img src="Images/image.png" alt="Logo" class="logo" />
        <span class="logo-text">UniRide</span>
      </div>
    </div>
  </header>

  <!-- Contenedor principal -->
  <div class="flex h-screen pt-[60px]">
    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar fixed left-0 top-[60px] h-full transition-transform duration-300 ease-in-out">
      <nav>
        <ul>
          <li>
            <a href="#" data-page="Inicio" class="sidebar-link active" onclick="changeSection('Inicio'); return false;">Inicio</a>
          </li>
          <li>
            <a href="#" data-page="misViajes" class="sidebar-link" onclick="changeSection('misViajes'); return false;">Mis Viajes</a>
          </li>
          <li>
            <a href="#" data-page="publicarViaje" class="sidebar-link" onclick="changeSection('publicarViaje'); return false;">Publicar Viaje</a>
          </li>
          <li>
            <a href="#" data-page="mensajes" class="sidebar-link" onclick="changeSection('mensajes'); return false;">Mensajes</a>
          </li>
          <li>
            <a href="#" data-page="perfil" class="sidebar-link" onclick="changeSection('perfil'); return false;">Perfil</a>
          </li>
          <li>
            <a href="#" data-page="seguridad" class="sidebar-link" onclick="changeSection('seguridad'); return false;">Seguridad</a>
          </li>
          <li>
            <a href="#" data-page="cerrarSesion" class="sidebar-link" onclick="changeSection('cerrarSesion'); return false;">Cerrar Sesión</a>
          </li>
        </ul>
      </nav>
    </aside>

    <!-- Contenido Principal -->
    <main id="mainContent" class="main-content ml-[200px] transition-all duration-300">
      <!-- Sección integrada "Inicio" -->
      <div id="contentInicio" class="content-section">
        <h2 class="main-title">Viajes Disponibles</h2>
        <div class="grid-container">
          <div class="card">
            <h3 class="card-title">Mérida → Progreso</h3>
            <p class="card-text">Fecha: 22 Feb 2025 - 10:00 AM</p>
            <p class="card-text">Conductor: Juan Pérez</p>
            <p class="card-text">Espacios disponibles: 2</p>
            <button class="btn">Reservar</button>
          </div>
          <div class="card">
            <h3 class="card-title">Centro → Universidad</h3>
            <p class="card-text">Fecha: 22 Feb 2025 - 7:30 AM</p>
            <p class="card-text">Conductor: Ana López</p>
            <p class="card-text">Espacios disponibles: 3</p>
            <button class="btn">Reservar</button>
          </div>
          <div class="card">
            <h3 class="card-title">Universidad → Plaza Mayor</h3>
            <p class="card-text">Fecha: 22 Feb 2025 - 5:00 PM</p>
            <p class="card-text">Conductor: Carlos Méndez</p>
            <p class="card-text">Espacios disponibles: 1</p>
            <button class="btn">Reservar</button>
          </div>
        </div>
        <h2 class="main-title mt-8">Seguimiento del Conductor</h2>
        <div id="map" class="map-container" style="height: 400px;"></div>
      </div>
      
      <!-- Contenedor para cargar el contenido de los archivos PHP externos -->
      <div id="contentContainer" class="content-section hidden"></div>
    </main>
  </div>

  <!-- Script para el toggle del sidebar y las funciones fetch -->
  <script>
    document.getElementById('toggleSidebar').addEventListener('click', function () {
      const sidebar = document.getElementById('sidebar');
      const mainContent = document.getElementById('mainContent');
      sidebar.classList.toggle('sidebar-hidden');
      mainContent.classList.toggle('expanded');
    });

    // Función para enviar datos a perfil.php vía fetch (igual que ya tienes)
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
          // Actualiza el contenido del contenedor con la respuesta
          document.getElementById('contentContainer').innerHTML = data;

          // Eliminar el mensaje de éxito/error después de 5 segundos
          const mensajeExito = document.getElementById('mensajeExito');
          const mensajeError = document.getElementById('mensajeError');

          if (mensajeExito) {
              setTimeout(() => {
                  mensajeExito.remove();
              }, 5000);
          }

          if (mensajeError) {
              setTimeout(() => {
                  mensajeError.remove();
              }, 5000);
          }
      })
      .catch(error => {
          console.error("Error en la solicitud fetch:", error);
      });
    }

    // Función para enviar datos a seguridad.php vía fetch, de forma similar a la de perfil.php
    function enviarFormularioSeguridad() {
      const form = document.getElementById('formSeguridad');
      const formData = new FormData(form);

      fetch('seguridad.php', {
          method: 'POST',
          body: formData,
          credentials: 'include'
      })
      .then(response => response.text())
      .then(data => {
          // Actualiza el contenido del contenedor con la respuesta
          document.getElementById('contentContainer').innerHTML = data;

          // Eliminar el mensaje de éxito/error después de 5 segundos
          const mensajeExito = document.getElementById('mensajeExito');
          const mensajeError = document.getElementById('mensajeError');

          if (mensajeExito) {
              setTimeout(() => {
                  mensajeExito.remove();
              }, 5000);
          }

          if (mensajeError) {
              setTimeout(() => {
                  mensajeError.remove();
              }, 5000);
          }
      })
      .catch(error => {
          console.error("Error en la solicitud fetch:", error);
      });
    }
  </script>
</body>
</html>
