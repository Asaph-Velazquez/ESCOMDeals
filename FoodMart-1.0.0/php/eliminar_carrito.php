<?php
session_start();

// Verificar que el usuario esté logueado
if (!isset($_SESSION['id_usuario'])) {
    echo "<script>alert('Por favor, inicia sesión para eliminar productos.'); window.location.href = 'login.html';</script>";
    exit();
}

// Conectar a la base de datos
include('conexion.php');

// Obtener el id del producto desde la URL
if (isset($_GET['id_producto'])) {
    $id_producto = $_GET['id_producto'];
    $id_usuario = $_SESSION['id_usuario'];

    // Preparar la consulta para eliminar el producto del carrito
    $sql = "DELETE FROM carrito WHERE id_producto = ? AND id_usuario = ?";
    $stmt = $conn->prepare($sql);

    // Vinculamos los parámetros
    $stmt->bind_param("ii", $id_producto, $id_usuario);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        // Redirigir al carrito después de eliminar el producto
        echo "<script>window.location.href = '../carrito.html';</script>";
    } else {
        echo "<script>alert('Hubo un error al eliminar el producto.'); window.location.href = 'carrito.html';</script>";
    }

    // Cerrar la declaración
    $stmt->close();
} else {
    echo "<script>alert('Producto no encontrado.'); window.location.href = 'carrito.html';</script>";
}

// Cerrar la conexión
$conn->close();
?>
