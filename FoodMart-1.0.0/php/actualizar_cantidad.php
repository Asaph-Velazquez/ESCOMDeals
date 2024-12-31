<?php
session_start();

// Verificar que el usuario esté logueado
if (!isset($_SESSION['id_usuario'])) {
    echo "<script>alert('Por favor, inicia sesión para modificar tu carrito.'); window.location.href = 'login.html';</script>";
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
        echo "<script>alert('La cantidad debe ser al menos 1.'); window.history.back();</script>";
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
        echo "<script>alert('La cantidad solicitada excede el stock disponible. Solo hay $stock unidades disponibles.'); window.history.back();</script>";
        exit();
    }

    // Consulta para actualizar la cantidad del producto en el carrito
    $sql = "UPDATE carrito SET cantidad = ? WHERE id_producto = ? AND id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $quantity, $product_id, $id_usuario);

    if ($stmt->execute()) {
        // Redirigir al carrito.html después de actualizar
        echo "<script>alert('Cantidad actualizada con éxito.'); window.location.href = '../carrito.html';</script>";
    } else {
        echo "<script>alert('Error al actualizar la cantidad. Intenta nuevamente.'); window.location.href = '../carrito.html';</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Datos no válidos.'); window.location.href = '../carrito.html';</script>";
}

// Cerrar la conexión
$conn->close();
?>
