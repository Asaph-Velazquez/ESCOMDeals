<?php
session_start();

header('Content-Type: application/json');

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

// Obtener y decodificar los datos JSON recibidos
$datos = json_decode(file_get_contents('php://input'), true);

if (!isset($datos['id_pedido']) || !isset($datos['nuevo_estado'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "escomdeals");

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $conn->connect_error]);
    exit;
}

// Verificar que el vendedor sea el propietario del pedido
$stmt = $conn->prepare("
    SELECT p.id_pedido 
    FROM pedido p 
    JOIN vendedor v ON p.id_vendedor = v.id_vendedor 
    WHERE v.id_usuario = ? 
    AND p.id_pedido = ?
");

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error en la preparación de la consulta']);
    exit;
}

$stmt->bind_param("ii", $_SESSION['id_usuario'], $datos['id_pedido']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'No tienes permiso para modificar este pedido']);
    $stmt->close();
    $conn->close();
    exit;
}

// Actualizar el estado en detalle_pedido
$update_stmt = $conn->prepare("
    UPDATE detalle_pedido 
    SET estado = ? 
    WHERE id_pedido = ?
");

if (!$update_stmt) {
    echo json_encode(['success' => false, 'message' => 'Error en la preparación de la actualización']);
    exit;
}

$update_stmt->bind_param("si", $datos['nuevo_estado'], $datos['id_pedido']);

if ($update_stmt->execute()) {
    echo json_encode([
        'success' => true, 
        'message' => 'Estado actualizado correctamente',
        'nuevo_estado' => $datos['nuevo_estado']
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado: ' . $update_stmt->error]);
}

$stmt->close();
$update_stmt->close();
$conn->close();
?>