<?php
session_start();  // Inicia la sesión para verificar si el usuario ha iniciado sesión

// Verificar que el usuario esté logueado
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(["error" => "Por favor inicia sesión para ver tu carrito."]);
    exit();
}

// Conectar a la base de datos
include('conexion.php');

// Obtener el id del usuario de la sesión
$id_usuario = $_SESSION['id_usuario'];

// Consulta preparada para obtener los productos del carrito
$sql = "SELECT p.nombre, p.precio, c.cantidad, p.id_producto 
        FROM carrito c 
        JOIN producto p ON c.id_producto = p.id_producto 
        WHERE c.id_usuario = ?";
$stmt = $conn->prepare($sql);

// Vinculamos el parámetro
$stmt->bind_param("i", $id_usuario);

// Ejecutamos la consulta
$stmt->execute();
$result = $stmt->get_result();

// Preparar los productos para devolverlos en formato JSON
$cartItems = [];
while ($row = $result->fetch_assoc()) {
    $cartItems[] = $row;
}

// Devolver los productos como un JSON
echo json_encode($cartItems);

// Cerramos la sentencia y la conexión
$stmt->close();
$conn->close();
?>
