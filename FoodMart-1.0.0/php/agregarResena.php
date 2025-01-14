<?php
session_start();
header('Content-Type: application/json');

// Verificar que el usuario esté logueado
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Por favor, inicia sesión para añadir una reseña.']);
    exit();
}

// Conectar a la base de datos
include('conexion.php');

// Obtener los datos del POST
$id_producto = isset($_POST['id_producto']) ? intval($_POST['id_producto']) : 0;
$calificacion = isset($_POST['calificacion']) ? intval($_POST['calificacion']) : 0;
$comentario = isset($_POST['comentario']) ? trim($_POST['comentario']) : '';

// Validar que todos los campos necesarios estén presentes
if (!$id_producto || !$calificacion || !$comentario) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos necesarios']);
    exit();
}

// Validar la calificación
if ($calificacion < 1 || $calificacion > 5) {
    echo json_encode(['success' => false, 'message' => 'La calificación debe estar entre 1 y 5']);
    exit();
}

try {
    // Obtener el id_comprador del usuario
    $query_comprador = "SELECT id_comprador FROM comprador WHERE id_usuario = ?";
    $stmt_comprador = $conexion->prepare($query_comprador);
    $stmt_comprador->bind_param('i', $_SESSION['id_usuario']);
    $stmt_comprador->execute();
    $resultado_comprador = $stmt_comprador->get_result();

    if ($resultado_comprador->num_rows === 0) {
        throw new Exception('Usuario no registrado como comprador');
    }

    $comprador = $resultado_comprador->fetch_assoc();
    $id_comprador = $comprador['id_comprador'];

    // Verificar si el usuario ya ha hecho una reseña para este producto
    $query_verificar = "SELECT id_resena FROM resena WHERE id_comprador = ? AND id_producto = ?";
    $stmt_verificar = $conexion->prepare($query_verificar);
    $stmt_verificar->bind_param('ii', $id_comprador, $id_producto);
    $stmt_verificar->execute();
    
    if ($stmt_verificar->get_result()->num_rows > 0) {
        throw new Exception('Ya has publicado una reseña para este producto');
    }

    // Insertar la reseña
    $query = "INSERT INTO resena (calificacion, comentario, fecha, id_comprador, id_producto) 
              VALUES (?, ?, CURDATE(), ?, ?)";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param('isii', $calificacion, $comentario, $id_comprador, $id_producto);

    if (!$stmt->execute()) {
        throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
    }

    echo json_encode(['success' => true, 'message' => 'Reseña publicada con éxito']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    // Cerrar todas las conexiones
    if (isset($stmt_comprador)) $stmt_comprador->close();
    if (isset($stmt_verificar)) $stmt_verificar->close();
    if (isset($stmt)) $stmt->close();
    if (isset($conexion)) $conexion->close();
}
?>