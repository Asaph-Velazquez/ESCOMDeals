<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'No has iniciado sesión.']);
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "escomdeals";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => "Error de conexión: " . $conn->connect_error]));
}

$id_producto = intval($_GET['id_producto']);

// Obtener el estado actual del producto
$stmt = $conn->prepare("SELECT disponibilidad, stock FROM producto WHERE id_producto = ?");
$stmt->bind_param("i", $id_producto);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'Producto no encontrado.']);
    exit();
}

$producto = $result->fetch_assoc();
$disponibilidad_actual = $producto['disponibilidad'];

// Cambiar la disponibilidad y stock
if ($disponibilidad_actual) {
    // Cambiar a "Agotado"
    $nueva_disponibilidad = 0;
    $nuevo_stock = 0;
} else {
    // Cambiar a "Disponible"
    $nueva_disponibilidad = 1;
    $nuevo_stock = 1;
}

// Actualizar el producto
$stmt = $conn->prepare("UPDATE producto SET disponibilidad = ?, stock = ? WHERE id_producto = ?");
$stmt->bind_param("iii", $nueva_disponibilidad, $nuevo_stock, $id_producto);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Estado de disponibilidad actualizado.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar el producto.']);
}

$stmt->close();
$conn->close();
?>
