<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.html");
    exit;
}

// Configurar la conexión a la base de datos
$servername = "localhost";
$username = "root";        
$password = "";            
$dbname = "escomdeals";     

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Validar que los datos se hayan enviado correctamente
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero = $_POST['numero'];
    $tipo = $_POST['tipo'];
    $banco = $_POST['banco'];
    $limite = $_POST['limite'];
    $nombre_titular = $_POST['nombre_titular'];
    $id_usuario = $_SESSION['id_usuario']; // Asociar tarjeta al usuario logueado

    // Preparar y ejecutar la consulta de inserción
    $query = "INSERT INTO tarjeta (numero, tipo, banco, limite, nombre_titular, id_usuario) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sssisi', $numero, $tipo, $banco, $limite, $nombre_titular, $id_usuario);

    if ($stmt->execute()) {
        // Redirigir con un mensaje de éxito
        header("Location: /ESCOMDeals/FoodMart-1.0.0/tarjetas.html?mensaje=Tarjeta registrada correctamente");
        exit;
    } else {
        // Redirigir con un mensaje de error
        header("Location: /ESCOMDeals/FoodMart-1.0.0/tarjetas.html?error=Error al registrar la tarjeta");
        exit;
    }
} else {
    header("Location: /ESCOMDeals/FoodMart-1.0.0/tarjetas.html");
    exit;
}
?>
