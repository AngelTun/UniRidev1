<?php
session_start();
require_once 'conexion.php';

header('Content-Type: text/html; charset=utf-8');

if (!isset($_SESSION['usuario'])) {
    echo '<div class="error-chat"><p>Debes iniciar sesión para ver esta página</p></div>';
    exit;
}

$correo_usuario = $_SESSION['usuario'];
$destinatario_actual = $_GET['contacto'] ?? '';

if (empty($destinatario_actual)) {
    echo '<div class="error-chat"><p>No se especificó un contacto</p></div>';
    exit;
}

// Verificar que el destinatario existe
$stmt = $pdo->prepare("SELECT nombres, apellidos, correo FROM usuarios WHERE correo = ?");
$stmt->execute([$destinatario_actual]);
$destinatario_info = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$destinatario_info) {
    echo '<div class="error-chat"><p>El contacto seleccionado no existe</p></div>';
    exit;
}

// Obtener mensajes de la conversación
$stmt = $pdo->prepare("SELECT id, remitente, destinatario, mensaje, fecha 
                       FROM mensajes 
                       WHERE (remitente = ? AND destinatario = ?) OR (remitente = ? AND destinatario = ?) 
                       ORDER BY fecha ASC");
$stmt->execute([$correo_usuario, $destinatario_actual, $destinatario_actual, $correo_usuario]);
$conversacion = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener el último ID de mensaje para el polling
$last_message_id = 0;
if (!empty($conversacion)) {
    $last_message = end($conversacion);
    $last_message_id = $last_message['id'];
}
?>

<div class="chat-header">
    <div class="chat-avatar"><?= strtoupper(substr($destinatario_info['nombres'], 0, 1)) ?></div>
    <div class="chat-title">
        <h4><?= htmlspecialchars($destinatario_info['nombres'] . ' ' . $destinatario_info['apellidos']) ?></h4>
        <small><?= htmlspecialchars($destinatario_actual) ?></small>
    </div>
</div>

<div class="chat-messages" id="chatMessages">
    <?php if (empty($conversacion)): ?>
        <div class="no-messages">
            <p>No hay mensajes todavía</p>
            <p>Envía tu primer mensaje a <?= htmlspecialchars($destinatario_info['nombres']) ?></p>
        </div>
    <?php else: ?>
        <?php foreach ($conversacion as $msg): ?>
            <div class="message <?= ($msg['remitente'] == $correo_usuario) ? 'sent' : 'received' ?>" data-message-id="<?= $msg['id'] ?>">
                <div class="message-content"><?= htmlspecialchars($msg['mensaje']) ?></div>
                <div class="message-time"><?= date('H:i', strtotime($msg['fecha'])) ?></div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<form method="POST" class="chat-input" id="chatForm">
    <input type="hidden" name="destinatario" value="<?= htmlspecialchars($destinatario_actual) ?>">
    <input type="text" name="mensaje" placeholder="Escribe un mensaje..." required autocomplete="off">
    <button type="submit" name="enviar_mensaje">Enviar</button>
</form>

<script>
// Iniciar el polling con el último ID de mensaje
if (typeof startPolling === 'function') {
    startPolling('<?= $destinatario_actual ?>', <?= $last_message_id ?>);
}
</script>