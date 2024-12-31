<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'No has iniciado sesión.']);
    exit();
}

// Verificar si el método es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "escomdeals";

    $id_producto = intval($_GET['id_producto']); // ID del producto a eliminar

    try {
        // Conectar a la base de datos
        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos.']);
            exit();
        }

        // Verificar que el producto pertenece al vendedor que inició sesión
        $stmt = $conn->prepare("SELECT id_vendedor FROM producto WHERE id_producto = ?");
        $stmt->bind_param("i", $id_producto);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Producto no encontrado.']);
            exit();
        }

        $producto = $result->fetch_assoc();
        $id_vendedor_producto = $producto['id_vendedor'];

        // Verificar que el usuario es dueño del producto
        $id_usuario = $_SESSION['id_usuario'];
        $stmt = $conn->prepare("SELECT id_vendedor FROM vendedor WHERE id_usuario = ?");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        $id_vendedor_sesion = $result->fetch_assoc()['id_vendedor'];

        if ($id_vendedor_producto !== $id_vendedor_sesion) {
            echo json_encode(['success' => false, 'message' => 'No tienes permiso para eliminar este producto.']);
            exit();
        }

        // Eliminar inscripciones en la tabla horario relacionadas con el producto
        $stmt = $conn->prepare("DELETE FROM horario WHERE id_producto = ?");
        $stmt->bind_param("i", $id_producto);
        $stmt->execute(); // Ejecutar eliminación de inscripciones

        // Eliminar el producto
        $stmt = $conn->prepare("DELETE FROM producto WHERE id_producto = ?");
        $stmt->bind_param("i", $id_producto);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Producto eliminado correctamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar el producto.']);
        }

        $stmt->close();
        $conn->close();

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
?>
