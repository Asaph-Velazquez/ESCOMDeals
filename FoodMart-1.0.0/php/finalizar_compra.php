<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    echo "<script>
        alert('Por favor, inicia sesión para finalizar la compra.');
        window.location.href = '../login.html';
    </script>";
    exit;
}

// Verificar si se recibió la dirección
if (!isset($_POST['direccionActual']) || empty($_POST['direccionActual'])) {
    echo "<script>
        alert('Por favor, indica tu ubicación actual.');
        window.location.href = '../carrito.html';
    </script>";
    exit;
}

$direccionActual = $_POST['direccionActual'];

// Conectar a la base de datos
$conn = new mysqli("localhost", "root", "", "escomdeals");

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener los productos del carrito
$sql_carrito = "SELECT c.id_producto, c.cantidad, p.precio, p.id_vendedor 
                FROM carrito c 
                JOIN producto p ON c.id_producto = p.id_producto 
                WHERE c.id_usuario = ?";
$stmt_carrito = $conn->prepare($sql_carrito);

if ($stmt_carrito) {
    $stmt_carrito->bind_param("i", $_SESSION['id_usuario']);
    $stmt_carrito->execute();
    $result_carrito = $stmt_carrito->get_result();

    // Calcular el total del pedido
    $total_pedido = 0;
    $productos = [];
    while ($producto = $result_carrito->fetch_assoc()) {
        $productos[] = $producto;
        $total_pedido += $producto['cantidad'] * $producto['precio'];
    }

    if (count($productos) > 0) {
        // Insertar el pedido en la tabla `pedido`
        $sql_pedido = "INSERT INTO pedido (fecha, total, id_comprador, id_vendedor) VALUES (NOW(), ?, ?, ?)";
        $stmt_pedido = $conn->prepare($sql_pedido);

        if ($stmt_pedido) {
            // Asumimos que todos los productos del carrito pertenecen a un solo vendedor
            $id_vendedor = $productos[0]['id_vendedor'];
            $stmt_pedido->bind_param("dii", $total_pedido, $_SESSION['id_usuario'], $id_vendedor);
            $stmt_pedido->execute();
            $id_pedido = $stmt_pedido->insert_id; // Obtener el ID del pedido insertado

            // Insertar detalles del pedido en `detalle_pedido` incluyendo la dirección
            foreach ($productos as $producto) {
                $sql_detalle = "INSERT INTO detalle_pedido (cantidad, id_pedido, id_producto, direccionActual) VALUES (?, ?, ?, ?)";
                $stmt_detalle = $conn->prepare($sql_detalle);

                if ($stmt_detalle) {
                    $stmt_detalle->bind_param("iiis", $producto['cantidad'], $id_pedido, $producto['id_producto'], $direccionActual);
                    $stmt_detalle->execute();
                }
            }

            // Vaciar el carrito después de registrar las compras
            $sql_vaciar_carrito = "DELETE FROM carrito WHERE id_usuario = ?";
            $stmt_vaciar_carrito = $conn->prepare($sql_vaciar_carrito);
            $stmt_vaciar_carrito->bind_param("i", $_SESSION['id_usuario']);
            $stmt_vaciar_carrito->execute();

            echo "<script>
                alert('¡Compra finalizada con éxito!');
                window.location.href = '../index.html';
            </script>";
        } else {
            echo "<script>alert('Error al registrar el pedido.');</script>";
        }
    } else {
        echo "<script>
            alert('Tu carrito está vacío.');
            window.location.href = '../carrito.html';
        </script>";
    }
}

// Cerrar conexiones
$stmt_carrito->close();
$conn->close();
?>