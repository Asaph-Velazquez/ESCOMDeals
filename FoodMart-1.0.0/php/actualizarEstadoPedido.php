<?php
session_start();
header('Content-Type: application/json');

// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "escomdeals");

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
    exit;
}

// Obtener los datos enviados desde el frontend
$data = json_decode(file_get_contents('php://input'), true);
$id_pedido = $data['id_pedido'];
$nuevo_estado = $data['nuevo_estado'];

// Actualizar el estado del pedido en la tabla `detalle_pedido`
$sql_update = "UPDATE detalle_pedido SET estado = ? WHERE id_pedido = ?";
$stmt_update = $conn->prepare($sql_update);

if ($stmt_update) {
    $stmt_update->bind_param("si", $nuevo_estado, $id_pedido);
    $stmt_update->execute();

    if ($stmt_update->affected_rows > 0) {
        // Si el estado es "Entregado", crear una notificación para el comprador
        if ($nuevo_estado === "Entregado") {
            // Obtener el comprador asociado al pedido
            $sql_comprador = "
                SELECT p.id_comprador, u.id_usuario, pr.nombre AS producto_nombre
                FROM pedido p
                JOIN detalle_pedido dp ON p.id_pedido = dp.id_pedido
                JOIN producto pr ON dp.id_producto = pr.id_producto
                JOIN comprador c ON p.id_comprador = c.id_comprador
                JOIN usuario u ON c.id_usuario = u.id_usuario
                WHERE p.id_pedido = ?
                LIMIT 1
            ";
            $stmt_comprador = $conn->prepare($sql_comprador);
            $stmt_comprador->bind_param("i", $id_pedido);
            $stmt_comprador->execute();
            $result_comprador = $stmt_comprador->get_result();

            if ($result_comprador->num_rows > 0) {
                $comprador = $result_comprador->fetch_assoc();
                $id_usuario_comprador = $comprador['id_usuario'];
                $producto_nombre = $comprador['producto_nombre'];

                // Crear la notificación
                $mensaje = "Tu pedido del producto '$producto_nombre' ha sido marcado como Entregado.";
                $sql_notificacion = "INSERT INTO notificacion (mensaje, fecha, id_usuario, id_producto) VALUES (?, NOW(), ?, ?)";
                $stmt_notificacion = $conn->prepare($sql_notificacion);

                if ($stmt_notificacion) {
                    $stmt_notificacion->bind_param("sii", $mensaje, $id_usuario_comprador, $id_pedido);
                    $stmt_notificacion->execute();
                }
            }
        }

        echo json_encode(['success' => true, 'message' => 'Estado actualizado correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se pudo actualizar el estado']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta']);
}

$conn->close();
?>
