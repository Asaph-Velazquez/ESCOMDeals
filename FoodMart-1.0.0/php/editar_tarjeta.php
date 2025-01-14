<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado.']);
    exit;
}

// Configurar la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "escomdeals";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos.']);
    exit;
}

// Leer los datos enviados en JSON
$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['id_tarjeta'], $input['numero'], $input['tipo'], $input['banco'], $input['limite'], $input['nombre_titular'])) {
    $id_tarjeta = $input['id_tarjeta'];
    $numero = $input['numero'];
    $tipo = $input['tipo'];
    $banco = $input['banco'];
    $limite = $input['limite'];
    $nombre_titular = $input['nombre_titular'];
    $id_usuario = $_SESSION['id_usuario'];

    // Actualizar los datos en la base de datos
    $query = "UPDATE tarjeta SET numero = ?, tipo = ?, banco = ?, limite = ?, nombre_titular = ? 
              WHERE id_tarjeta = ? AND id_usuario = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sssissi', $numero, $tipo, $banco, $limite, $nombre_titular, $id_tarjeta, $id_usuario);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Tarjeta actualizada correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar la tarjeta.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos enviados.']);
}
?>
