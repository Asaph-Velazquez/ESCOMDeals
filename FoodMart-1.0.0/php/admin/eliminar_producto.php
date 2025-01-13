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

    // Consulta para eliminar el producto
    $sql = "DELETE FROM producto WHERE id_producto = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id_producto); // Vincula el parámetro
        if ($stmt->execute()) {
            // Redirigir a la ventana anterior
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        } else {
            echo "Error al eliminar el producto.";
        }
        $stmt->close();
    } else {
        echo "Error en la consulta.";
    }
} else {
    echo "No se ha proporcionado un ID de producto.";
}

// Cerrar la conexión
$conn->close();
?>
