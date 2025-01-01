<?php
// Conexión a la base de datos
$host = "localhost";
$usuario = "root"; // Cambia por tu usuario de la base de datos
$contraseña = ""; // Cambia por tu contraseña de la base de datos
$base_datos = "escomdeals"; // Cambia por el nombre de tu base de datos

$conn = new mysqli($host, $usuario, $contraseña, $base_datos);

// Verifica si la conexión fue exitosa
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Inicia la sesión para obtener el id del usuario
session_start();

// Verifica si el usuario está logueado
if (isset($_SESSION['id_usuario'])) {
    $id_usuario = $_SESSION['id_usuario'];

    // Consulta para obtener los productos favoritos del usuario
    $sql = "SELECT p.id_producto, p.nombre, p.descripcion, p.precio, p.imagen_producto 
            FROM favoritos f
            JOIN producto p ON f.id_producto = p.id_producto
            WHERE f.id_usuario = $id_usuario";

    $resultado = $conn->query($sql);

    // Verifica si se encontraron productos favoritos
    if ($resultado->num_rows > 0) {
        $productos = $resultado->fetch_all(MYSQLI_ASSOC);
    } else {
        $productos = [];
    }
} else {
    $productos = [];
}

$conn->close();
?>
