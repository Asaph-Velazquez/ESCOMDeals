<?php
session_start();

// Verificar que el usuario esté logueado
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Por favor, inicia sesión para modificar tu carrito.']);
    exit();
}

// Conectar a la base de datos
include('conexion.php');

// Obtener el id del usuario de la sesión
$id_usuario = $_SESSION['id_usuario'];

// Verificar si los datos de cantidad y producto han sido enviados
if (isset($_POST['quantity']) && isset($_POST['product_id'])) {
    $quantity = $_POST['quantity'];
    $product_id = $_POST['product_id'];

    // Validar la cantidad
    if ($quantity < 1) {
        echo json_encode(['success' => false, 'message' => 'La cantidad debe ser al menos 1.']);
        exit();
    }

    // Consulta para obtener el stock disponible del producto
    $sql = "SELECT stock FROM producto WHERE id_producto = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->bind_result($stock);
    $stmt->fetch();
    $stmt->close();

    // Verificar si la cantidad solicitada es mayor que el stock disponible
    if ($quantity > $stock) {
        echo json_encode([
            'success' => false,
            'message' => "La cantidad solicitada excede el stock disponible. Solo hay $stock unidades disponibles.",
            'previous_quantity' => $quantity // Enviar la cantidad previa para restaurar
           
        ]);
        exit();
    }

    // Consulta para actualizar la cantidad del producto en el carrito
    $sql = "UPDATE carrito SET cantidad = ? WHERE id_producto = ? AND id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $quantity, $product_id, $id_usuario);

    if ($stmt->execute()) {
        // Responder éxito
        echo json_encode(['success' => true, 'message' => 'Cantidad actualizada con éxito']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar la cantidad. Intenta nuevamente.']);
   
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Datos no válidos.']);
   
}

// Cerrar la conexión
$conn->close();
?>
