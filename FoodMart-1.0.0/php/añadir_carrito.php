<?php
session_start();

// Verificar que el usuario esté logueado
if (!isset($_SESSION['id_usuario'])) {
    echo "<script>alert('Por favor, inicia sesión para añadir productos al carrito.'); window.location.href = 'login.html';</script>";
    exit();
}

// Conectar a la base de datos
include('conexion.php');

// Obtener el id del usuario de la sesión
$id_usuario = $_SESSION['id_usuario'];

// Verificar si el id del producto ha sido enviado
if (isset($_POST['id_producto'])) {
    $id_producto = intval($_POST['id_producto']);

    // Consulta para verificar si el producto ya está en el carrito
    $sql = "SELECT cantidad FROM carrito WHERE id_producto = ? AND id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_producto, $id_usuario);
    $stmt->execute();
    $stmt->bind_result($cantidad_actual);
    $producto_existe = $stmt->fetch();
    $stmt->close();

    if ($producto_existe) {
        // Si el producto ya está en el carrito, incrementa la cantidad en 1
        $nueva_cantidad = $cantidad_actual + 1;

        // Consulta para verificar el stock del producto
        $sql_stock = "SELECT stock FROM producto WHERE id_producto = ?";
        $stmt_stock = $conn->prepare($sql_stock);
        $stmt_stock->bind_param("i", $id_producto);
        $stmt_stock->execute();
        $stmt_stock->bind_result($stock);
        $stmt_stock->fetch();
        $stmt_stock->close();

        if ($nueva_cantidad > $stock) {
            echo "<script>alert('No hay suficiente stock disponible.'); window.location.href = '" . $_SERVER['HTTP_REFERER'] . "';</script>";
            exit();
        }

        // Actualizar la cantidad en el carrito
        $sql_update = "UPDATE carrito SET cantidad = ? WHERE id_producto = ? AND id_usuario = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("iii", $nueva_cantidad, $id_producto, $id_usuario);
        $stmt_update->execute();
        $stmt_update->close();

        echo "<script>alert('Cantidad incrementada en el carrito.'); window.location.href = '" . $_SERVER['HTTP_REFERER'] . "';</script>";
    } else {
        // Si el producto no está en el carrito, añadirlo con cantidad 1
        $sql_insert = "INSERT INTO carrito (id_producto, id_usuario, cantidad) VALUES (?, ?, 1)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("ii", $id_producto, $id_usuario);

        if ($stmt_insert->execute()) {
            echo "<script>alert('Producto añadido al carrito.'); window.location.href = '" . $_SERVER['HTTP_REFERER'] . "';</script>";
        } else {
            echo "<script>alert('Error al añadir el producto al carrito.'); window.location.href = '" . $_SERVER['HTTP_REFERER'] . "';</script>";
        }

        $stmt_insert->close();
    }
} else {
    echo "<script>alert('Datos no válidos.'); window.location.href = '" . $_SERVER['HTTP_REFERER'] . "';</script>";
}

// Cerrar la conexión
$conn->close();
?>
