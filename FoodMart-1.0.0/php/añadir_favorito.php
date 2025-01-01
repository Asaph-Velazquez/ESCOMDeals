<?php
session_start();

// Verificar que el usuario esté logueado
if (!isset($_SESSION['id_usuario'])) {
    echo "<script>alert('Por favor, inicia sesión para añadir productos a favoritos.'); window.location.href = 'login.html';</script>";
    exit();
}

// Conectar a la base de datos
include('conexion.php');

// Obtener el id del usuario de la sesión
$id_usuario = $_SESSION['id_usuario'];

// Verificar si el id del producto ha sido enviado
if (isset($_POST['id_producto'])) {
    $id_producto = intval($_POST['id_producto']);

    // Consulta para verificar si el producto ya está en favoritos
    $sql = "SELECT 1 FROM favoritos WHERE id_producto = ? AND id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_producto, $id_usuario);
    $stmt->execute();
    $producto_existe = $stmt->fetch();
    $stmt->close();

    if ($producto_existe) {
        // Si el producto ya está en favoritos, mostrar alerta
        echo "<script>alert('Este producto ya está en tus favoritos.'); window.location.href = '" . $_SERVER['HTTP_REFERER'] . "';</script>";
    } else {
        // Si el producto no está en favoritos, añadirlo
        $sql_insert = "INSERT INTO favoritos (id_producto, id_usuario) VALUES (?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("ii", $id_producto, $id_usuario);

        if ($stmt_insert->execute()) {
            echo "<script>alert('Producto añadido a favoritos.'); window.location.href = '" . $_SERVER['HTTP_REFERER'] . "';</script>";
        } else {
            echo "<script>alert('Error al añadir el producto a favoritos.'); window.location.href = '" . $_SERVER['HTTP_REFERER'] . "';</script>";
        }

        $stmt_insert->close();
    }
} else {
    echo "<script>alert('Datos no válidos.'); window.location.href = '" . $_SERVER['HTTP_REFERER'] . "';</script>";
}

// Cerrar la conexión
$conn->close();
?>
