<?php
session_start();
// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login.html");
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
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Verificar la conexión
        if ($conn->connect_error) {
            throw new Exception("Conexión fallida: " . $conn->connect_error);
        }

        // Verificar si el usuario es vendedor
        $id_vendedor = esVendedor($conn, $_SESSION['id_usuario']);
        if (!$id_vendedor) {
            throw new Exception("Usuario no autorizado para crear productos");
        }

        // Recoger y sanitizar datos del formulario
        $nombre = htmlspecialchars($_POST['nombreProducto']);
        $precio = filter_var($_POST['precioDescuento'], FILTER_VALIDATE_FLOAT);
        $descripcion = htmlspecialchars($_POST['descripcionProducto']);
        $stock = ($_POST['disponibilidadProducto'] === 'disponible') ? 1 : 0;
        $categoria = htmlspecialchars($_POST['categoriaProducto'] ?? '');

        // Validar datos
        if (empty($nombre) || $precio <= 0) {
            throw new Exception("Datos del producto inválidos");
        }

        // Manejar la subida de la imagen
        $imagen_producto = null;
        if (isset($_FILES['imagenProducto']) && $_FILES['imagenProducto']['error'] == UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['imagenProducto']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (!in_array($ext, $allowed)) {
                throw new Exception("Tipo de archivo no permitido");
            }

            $targetDirectory = "../imgsVentas/";
            $newFilename = uniqid() . "." . $ext;
            $targetFile = $targetDirectory . $newFilename;

            if (!move_uploaded_file($_FILES['imagenProducto']['tmp_name'], $targetFile)) {
                throw new Exception("Error al subir la imagen");
            }

            $imagen_producto = $newFilename;
        }

        // Iniciar transacción
        $conn->begin_transaction();

        // Insertar producto
        $stmt = $conn->prepare("INSERT INTO producto (nombre, descripcion, precio, stock, categoria, imagen_producto, id_vendedor) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdiisi", $nombre, $descripcion, $precio, $stock, $categoria, $imagen_producto, $id_vendedor);

        if (!$stmt->execute()) {
            throw new Exception("Error al crear el producto: " . $stmt->error);
        }

        $id_producto = $stmt->insert_id;

        // Si tienes una tabla específica para horarios, aquí iría el código para guardarlos
        // Por ahora, comento esta parte ya que no está en tu esquema de base de datos
        /*
        $dias = $_POST['dias'] ?? [];
        foreach ($dias as $dia) {
            $horas = $_POST['horas' . ucfirst($dia)] ?? '';
            if (!empty($horas)) {
                $stmtHoras = $conn->prepare("INSERT INTO horarios (id_producto, dia, horas) VALUES (?, ?, ?)");
                $stmtHoras->bind_param("iss", $id_producto, $dia, $horas);
                $stmtHoras->execute();
            }
        }
        */

        // Confirmar transacción
        $conn->commit();

        // Redireccionar al panel de ventas
        header("Location: ../panel_ventas.html?success=true");
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

        header("Location: ../panel_ventas.html?error=" . urlencode($e->getMessage()));
        exit();
    } finally {
        // Cerrar la conexión
        if (isset($stmt)) $stmt->close();
        if (isset($conn)) $conn->close();
    }
} else {
    header("Location: ../panel_ventas.html?error=metodo_no_permitido");
    exit();
}
?>