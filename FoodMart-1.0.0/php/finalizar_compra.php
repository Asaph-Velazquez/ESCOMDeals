<?php
session_start();
header('Content-Type: application/json');

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Por favor inicia sesión para realizar la compra.']);
    exit;
}

// Conectar a la base de datos
$conn = new mysqli("localhost", "root", "", "escomdeals");
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']));
}

// Obtener los datos del carrito
$sql_carrito = "SELECT c.id_producto, c.cantidad, p.precio, p.id_vendedor, p.nombre as producto_nombre 
                FROM carrito c 
                JOIN producto p ON c.id_producto = p.id_producto 
                WHERE c.id_usuario = ?";
$stmt_carrito = $conn->prepare($sql_carrito);
$stmt_carrito->bind_param("i", $_SESSION['id_usuario']);
$stmt_carrito->execute();
$result_carrito = $stmt_carrito->get_result();

$total_pedido = 0;
$productos = [];
while ($row = $result_carrito->fetch_assoc()) {
    $productos[] = $row;
    $total_pedido += $row['cantidad'] * $row['precio'];
}

if (count($productos) > 0) {
    // Insertar el pedido
    $sql_pedido = "INSERT INTO pedido (fecha, total, id_comprador, id_vendedor) VALUES (NOW(), ?, ?, ?)";
    $stmt_pedido = $conn->prepare($sql_pedido);
    $id_vendedor = $productos[0]['id_vendedor']; // Asumimos un solo vendedor
    $stmt_pedido->bind_param("dii", $total_pedido, $_SESSION['id_usuario'], $id_vendedor);
    $stmt_pedido->execute();
    $id_pedido = $stmt_pedido->insert_id;

    // Insertar detalles del pedido
    $sql_detalle = "INSERT INTO detalle_pedido (cantidad, id_pedido, id_producto, direccionActual) VALUES (?, ?, ?, ?)";
    $stmt_detalle = $conn->prepare($sql_detalle);
    $direccionActual = $_POST['direccionActual'];

    foreach ($productos as $producto) {
        $stmt_detalle->bind_param("iiis", $producto['cantidad'], $id_pedido, $producto['id_producto'], $direccionActual);
        $stmt_detalle->execute();
    }

    // Crear notificación para el vendedor
    $sql_vendedor = "SELECT u.id_usuario, u.alias 
                     FROM vendedor v 
                     JOIN usuario u ON v.id_usuario = u.id_usuario 
                     WHERE v.id_vendedor = ?";
    $stmt_vendedor = $conn->prepare($sql_vendedor);
    $stmt_vendedor->bind_param("i", $id_vendedor);
    $stmt_vendedor->execute();
    $result_vendedor = $stmt_vendedor->get_result();
    $vendedor = $result_vendedor->fetch_assoc();

    $usuario_comprador = $_SESSION['alias']; // Alias del comprador
    foreach ($productos as $producto) {
        $mensaje = "$usuario_comprador ha realizado una compra de {$producto['producto_nombre']}";
        $sql_notificacion = "INSERT INTO notificacion (mensaje, fecha, id_usuario, id_producto) VALUES (?, NOW(), ?, ?)";
        $stmt_notificacion = $conn->prepare($sql_notificacion);
        $stmt_notificacion->bind_param("sii", $mensaje, $vendedor['id_usuario'], $producto['id_producto']);
        $stmt_notificacion->execute();
    }

    // Vaciar el carrito
    $sql_vaciar = "DELETE FROM carrito WHERE id_usuario = ?";
    $stmt_vaciar = $conn->prepare($sql_vaciar);
    $stmt_vaciar->bind_param("i", $_SESSION['id_usuario']);
    $stmt_vaciar->execute();

    echo json_encode(['success' => true, 'message' => 'Compra realizada con éxito']);
} else {
    echo json_encode(['success' => false, 'message' => 'El carrito está vacío.']);
}

$conn->close();
?>
