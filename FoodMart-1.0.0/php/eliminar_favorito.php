<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login.html");
    exit();
}

// Verificar si se recibió el ID del producto
if (!isset($_GET['id_producto'])) {
    header("Location: ../wishlist.html");
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

// Obtener los datos
$id_usuario = $_SESSION['id_usuario'];
$id_producto = $_GET['id_producto'];

// Preparar la consulta SQL para eliminar el favorito
$sql = "DELETE FROM favoritos WHERE id_usuario = ? AND id_producto = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_usuario, $id_producto);

// Ejecutar la consulta
if ($stmt->execute()) {
    // Éxito al eliminar
    header("Location: ../wishlist.html");
} else {
    // Error al eliminar
    echo "Error al eliminar el producto de favoritos: " . $conn->error;
}

// Cerrar conexiones
$stmt->close();
$conn->close();
?>