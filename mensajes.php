<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) { 
    echo "<script>alert('Debes iniciar sesión para acceder a los mensajes'); window.location.href='iniciosesion.php';</script>";
    exit;
}

require_once 'conexion.php';

// Verificar sesión única
$correo_usuario = $_SESSION['usuario'];
$stmt = $pdo->prepare("SELECT session_id FROM usuarios WHERE correo = ?");
$stmt->execute([$correo_usuario]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && $user['session_id'] !== session_id()) {
    echo "<script>alert('Ya hay una sesión activa con este usuario en otro dispositivo.'); window.location.href='cerrarsesion.php';</script>";
    exit;
}

// Obtener lista de contactos (excluyendo al usuario actual)
$stmt = $pdo->prepare("SELECT id, nombres, apellidos, correo FROM usuarios WHERE correo != ? ORDER BY nombres");
$stmt->execute([$correo_usuario]);
$contactos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener conversación si hay un destinatario seleccionado
$destinatario_actual = $_GET['contacto'] ?? "";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensajes - UniRide</title>
    <link rel="stylesheet" href="mensajes.css">
</head>
<body>
<div class="messages-container">
    <div class="contacts-list">
        <h3>Contactos</h3>
        <div class="search-box">
            <input type="text" placeholder="Buscar contacto..." id="searchContact">
        </div>
        <ul id="contactsList">
            <?php foreach ($contactos as $contacto): ?>
            <li class="contact-item <?= ($contacto['correo'] == $destinatario_actual) ? 'active' : '' ?>" 
                data-email="<?= htmlspecialchars($contacto['correo']) ?>">
                <a href="#" class="contact-link">
                    <div class="contact-avatar"><?= strtoupper(substr($contacto['nombres'], 0, 1)) ?></div>
                    <div class="contact-info">
                        <span class="contact-name"><?= htmlspecialchars($contacto['nombres'] . ' ' . $contacto['apellidos']) ?></span>
                        <span class="contact-email"><?= htmlspecialchars($contacto['correo']) ?></span>
                    </div>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="chat-container" id="chatContainer">
        <?php if (!empty($destinatario_actual)): ?>
            <div class="loading-chat">
                <div class="spinner"></div>
                <p>Cargando conversación...</p>
            </div>
            <script>
                loadChat('<?= $destinatario_actual ?>');
            </script>
        <?php else: ?>
            <div class="no-chat-selected">
                <div class="icon">○</div>
                <h4>Selecciona un contacto para comenzar a chatear</h4>
                <p>Elige un contacto de la lista para ver la conversación</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Variables para el polling
let pollingInterval;
let currentChatEmail = '';

// Función para cargar chat
function loadChat(email) {
    currentChatEmail = email;
    const chatContainer = document.getElementById('chatContainer');
    
    fetch(`obtener_conversacion.php?contacto=${encodeURIComponent(email)}`, {
        credentials: 'include'
    })
    .then(response => response.text())
    .then(html => {
        chatContainer.innerHTML = html;
        setupChatForm();
        scrollToBottom();
        startPolling(email);
    })
    .catch(error => {
        chatContainer.innerHTML = `
            <div class="error-chat">
                <p>Error al cargar la conversación</p>
                <button onclick="location.reload()">Reintentar</button>
            </div>
        `;
    });
}

// Configurar formulario de chat
function setupChatForm() {
    const chatForm = document.getElementById('chatForm');
    if (chatForm) {
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            sendMessage(this);
        });
    }
}

// Enviar mensaje
function sendMessage(form) {
    const formData = new FormData(form);
    const chatMessages = document.getElementById('chatMessages');
    
    fetch('enviar_mensaje.php', {
        method: 'POST',
        body: formData,
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const messageDiv = document.createElement('div');
            messageDiv.className = 'message sent';
            messageDiv.dataset.messageId = data.id;
            messageDiv.innerHTML = `
                <div class="message-content">${data.mensaje}</div>
                <div class="message-time">${data.hora}</div>
            `;
            chatMessages.appendChild(messageDiv);
            form.reset();
            scrollToBottom();
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al enviar el mensaje');
    });
}

// Scroll al final del chat
function scrollToBottom() {
    const chatMessages = document.getElementById('chatMessages');
    if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
}

// Sistema de polling
function startPolling(email) {
    stopPolling();
    pollingInterval = setInterval(() => {
        checkNewMessages(email);
    }, 3000);
}

function stopPolling() {
    if (pollingInterval) {
        clearInterval(pollingInterval);
    }
}

function checkNewMessages(email) {
    if (!email || email !== currentChatEmail) return;
    
    const chatMessages = document.getElementById('chatMessages');
    if (!chatMessages) return;
    
    const lastMessage = chatMessages.querySelector('.message:last-child');
    const lastMessageId = lastMessage ? lastMessage.dataset.messageId : 0;
    
    fetch(`obtener_nuevos_mensajes.php?contacto=${encodeURIComponent(email)}&ultimo_id=${lastMessageId}`, {
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.mensajes && data.mensajes.length > 0) {
            data.mensajes.forEach(msg => {
                const messageDiv = document.createElement('div');
                messageDiv.className = `message ${msg.remitente === '<?= $_SESSION['usuario'] ?>' ? 'sent' : 'received'}`;
                messageDiv.dataset.messageId = msg.id;
                messageDiv.innerHTML = `
                    <div class="message-content">${msg.mensaje}</div>
                    <div class="message-time">${msg.hora}</div>
                `;
                chatMessages.appendChild(messageDiv);
            });
            scrollToBottom();
        }
    })
    .catch(error => console.error('Error al verificar nuevos mensajes:', error));
}

// Eventos
document.addEventListener('DOMContentLoaded', function() {
    // Delegación de eventos para contactos
    document.getElementById('contactsList').addEventListener('click', function(e) {
        if (e.target.closest('.contact-link')) {
            e.preventDefault();
            const contacto = e.target.closest('.contact-item');
            const email = contacto.getAttribute('data-email');
            
            document.querySelectorAll('.contact-item').forEach(li => {
                li.classList.remove('active');
            });
            contacto.classList.add('active');
            
            loadChat(email);
        }
    });

    // Búsqueda de contactos
    const searchInput = document.getElementById('searchContact');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const term = this.value.toLowerCase();
            document.querySelectorAll('.contact-item').forEach(item => {
                const name = item.querySelector('.contact-name').textContent.toLowerCase();
                const email = item.querySelector('.contact-email').textContent.toLowerCase();
                item.style.display = (name.includes(term) || email.includes(term)) ? 'flex' : 'none';
            });
        });
    }
});
</script>
</body>
</html>