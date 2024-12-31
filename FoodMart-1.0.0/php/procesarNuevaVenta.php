<?php
session_start();
// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Error: No has iniciado sesión.']);
    exit();
}

// Verificar si el usuario es un vendedor
function esVendedor($conn, $id_usuario) {
    $stmt = $conn->prepare("SELECT id_vendedor FROM vendedor WHERE id_usuario = ?");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['id_vendedor'];
    }
    return false;
}

// Verificar si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Conexión a la base de datos
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "escomdeals";

    try {
        // Crear conexión
        $conn = mysqli_connect($servername, $username, $password, $dbname);

        // Verificar la conexión
        if ($conn->connect_error) {
            echo json_encode(['success' => false, 'message' => 'Error: Conexión fallida.']);
            exit();
        }

        // Verificar si el usuario es vendedor
        $id_vendedor = esVendedor($conn, $_SESSION['id_usuario']);
        if (!$id_vendedor) {
            echo json_encode(['success' => false, 'message' => 'Error: Usuario no autorizado para crear productos.']);
            exit();
        }

        // Recoger y sanitizar datos del formulario
        $nombre = htmlspecialchars($_POST['nombreProducto']);
        $precio = filter_var($_POST['precioDescuento'], FILTER_VALIDATE_FLOAT);
        $descripcion = htmlspecialchars($_POST['descripcionProducto']);
        $stock = ($_POST['disponibilidadProducto'] === 'disponible') ? 1 : 0;
        $categoria = htmlspecialchars($_POST['categoriaProducto'] ?? '');

        // Validar datos
        if (empty($nombre) || $precio === false || $precio <= 0) {
            echo json_encode(['success' => false, 'message' => 'Error: Datos del producto inválidos.']);
            exit();
        }

        // Manejar la subida de la imagen
        $imagen_producto = null;
        if (isset($_FILES['imagenProducto']) && $_FILES['imagenProducto']['error'] == UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['imagenProducto']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (!in_array($ext, $allowed)) {
                echo json_encode(['success' => false, 'message' => 'Error: Tipo de archivo no permitido.']);
                exit();
            }

            $targetDirectory = "imgsVentas/";
            $newFilename = uniqid() . "." . $ext;
            $targetFile = $targetDirectory . $newFilename;

            if (!move_uploaded_file($_FILES['imagenProducto']['tmp_name'], $targetFile)) {
                echo json_encode(['success' => false, 'message' => 'Error: Error al subir la imagen.']);
                exit();
            }

            $imagen_producto = $newFilename;
        }

        // Iniciar transacción
        $conn->begin_transaction();

        // Insertar producto
        $stmt = $conn->prepare("INSERT INTO producto (nombre, descripcion, precio, stock, categoria, imagen_producto, id_vendedor) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdiisi", $nombre, $descripcion, $precio, $stock, $categoria, $imagen_producto, $id_vendedor);

        if (!$stmt->execute()) {
            throw new Exception("Error: " . $stmt->error);
        }

        // Confirmar transacción
        $conn->commit();

        // Retornar éxito
        echo json_encode(['success' => true, 'message' => 'Producto creado exitosamente.']);
        exit();

    } catch (Exception $e) {
        // Si hay error, hacer rollback
        if (isset($conn) && $conn->connect_error === false) {
            $conn->rollback();
        }

        // Si se subió una imagen, eliminarla
        if (isset($targetFile) && file_exists($targetFile)) {
            unlink($targetFile);
        }

        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit();
    } finally {
        // Cerrar la conexión
        if (isset($stmt)) $stmt->close();
        if (isset($conn)) $conn->close();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error: Método no permitido.']);
    exit();
}
?>
