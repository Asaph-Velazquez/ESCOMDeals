<?php
session_start(); // Inicia la sesión

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../login.html");
    exit();
}

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "escomdeals";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Verificar si se recibió el ID del producto a eliminar
if (isset($_POST['id_producto'])) {
    $id_producto = $_POST['id_producto'];

    // Eliminar filas relacionadas en 'horario'
    $sql_horario = "DELETE FROM horario WHERE id_producto = ?";
    if ($stmt_horario = $conn->prepare($sql_horario)) {
        $stmt_horario->bind_param("i", $id_producto);
        $stmt_horario->execute();
        $stmt_horario->close();
    }

    // Eliminar el producto
    $sql_producto = "DELETE FROM producto WHERE id_producto = ?";
    if ($stmt_producto = $conn->prepare($sql_producto)) {
        $stmt_producto->bind_param("i", $id_producto);
        if ($stmt_producto->execute()) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        } else {
            echo "Error al eliminar el producto.";
        }
        $stmt_producto->close();
    }
}
 else {
    echo "No se ha proporcionado un ID de producto.";
}

// Cerrar la conexión
$conn->close();
?>
