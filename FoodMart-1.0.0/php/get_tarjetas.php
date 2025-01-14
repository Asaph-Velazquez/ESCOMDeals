<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['error' => 'Usuario no autenticado.']);
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "escomdeals";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['error' => 'Error de conexión a la base de datos.']);
    exit;
}

// Obtener el ID de la tarjeta desde la solicitud
$id_tarjeta = isset($_GET['id_tarjeta']) ? intval($_GET['id_tarjeta']) : 0;

if ($id_tarjeta > 0) {
    $id_usuario = $_SESSION['id_usuario'];

    // Consulta para obtener los datos de la tarjeta
    $query = "SELECT id_tarjeta, numero, tipo, banco, limite, nombre_titular 
              FROM tarjeta 
              WHERE id_tarjeta = ? AND id_usuario = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $id_tarjeta, $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $tarjeta = $result->fetch_assoc();
        echo json_encode($tarjeta);
    } else {
        echo json_encode(['error' => 'Tarjeta no encontrada.']);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'ID de tarjeta inválido.']);
}

$conn->close();
?>
