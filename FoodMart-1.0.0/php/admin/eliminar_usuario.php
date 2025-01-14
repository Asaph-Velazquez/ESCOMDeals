<?php
session_start();
include_once '../conexion.php';

// Verificar si el ID está presente en la URL
if (isset($_GET['id'])) {
    $id_usuario = $_GET['id'];

    // Consulta para eliminar el usuario
    $deleteQuery = "DELETE FROM usuario WHERE id_usuario = ?";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->bind_param('i', $id_usuario);

    if ($deleteStmt->execute()) {
        // Si la eliminación fue exitosa, redirigir a crud.php
        header('Location: crud.php');
        exit; // Importante para detener la ejecución del código
    } else {
        echo "Error al eliminar el usuario.";
    }
} else {
    // Si no se encuentra el ID en la URL
    echo "ID de usuario no proporcionado.";
    exit;
}
?>
