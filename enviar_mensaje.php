<?php
session_start();
require_once 'conexion.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario'])) {
    echo json_encode(['success' => false, 'error' => 'No autenticado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

if (!isset($_POST['destinatario'], $_POST['mensaje'])) {  
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);  
    exit;  
}  

$remitente = $_SESSION['usuario'];  
$destinatario = trim($_POST['destinatario']);  
$mensaje = trim($_POST['mensaje']);  

if (empty($mensaje)) {  
    echo json_encode(['success' => false, 'error' => 'El mensaje no puede estar vacío']);  
    exit;  
}  

// Verificar que el destinatario existe  
$stmt = $pdo->prepare("SELECT correo FROM usuarios WHERE correo = ?");  
$stmt->execute([$destinatario]);  
if (!$stmt->fetch()) {  
    echo json_encode(['success' => false, 'error' => 'Destinatario no válido']);  
    exit;  
}  

try {  
    // Insertar mensaje  
    $stmt = $pdo->prepare("INSERT INTO mensajes (remitente, destinatario, mensaje, fecha) VALUES (?, ?, ?, NOW())");  
    $stmt->execute([$remitente, $destinatario, $mensaje]);  
    
    // Obtener el ID del mensaje recién insertado
    $mensaje_id = $pdo->lastInsertId();
    
    // Obtener la hora actual formateada
    $hora = date('H:i');

    echo json_encode([  
        'success' => true,  
        'mensaje' => htmlspecialchars($mensaje),  
        'hora' => $hora,
        'id' => $mensaje_id,
        'remitente' => $remitente
    ]);  

} catch (PDOException $e) {  
    error_log('Error al enviar mensaje: '. $e->getMessage());  
    echo json_encode(['success' => false, 'error' => 'Error al guardar el mensaje']);  
}