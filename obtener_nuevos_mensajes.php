<?php
session_start();
require_once 'conexion.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario'])) {
    echo json_encode(['success' => false, 'error' => 'No autenticado']);
    exit;
}

$correo_usuario = $_SESSION['usuario'];
$destinatario_actual = $_GET['contacto'] ?? '';
$ultimo_id = (int)($_GET['ultimo_id'] ?? 0);

if (empty($destinatario_actual)) {
    echo json_encode(['success' => false, 'error' => 'No se especificó un contacto']);
    exit;
}

// Obtener nuevos mensajes (solo los que no hemos recibido aún)
$stmt = $pdo->prepare("SELECT id, remitente, mensaje, DATE_FORMAT(fecha, '%H:%i') as hora 
                       FROM mensajes 
                       WHERE ((remitente = ? AND destinatario = ?) OR (remitente = ? AND destinatario = ?))
                       AND id > ?
                       ORDER BY fecha ASC");
$stmt->execute([$correo_usuario, $destinatario_actual, $destinatario_actual, $correo_usuario, $ultimo_id]);
$mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'mensajes' => $mensajes
]);
?>