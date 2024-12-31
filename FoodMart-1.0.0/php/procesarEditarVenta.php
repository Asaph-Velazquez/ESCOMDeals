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
    return $result->num_rows > 0 ? $result->fetch_assoc()['id_vendedor'] : false;
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
            throw new Exception('Error: Conexión fallida.');
        }

        // Verificar si el usuario es vendedor
        $id_vendedor = esVendedor($conn, $_SESSION['id_usuario']);
        if (!$id_vendedor) {
            throw new Exception('Error: Usuario no autorizado para crear productos.');
        }

        // Recoger y sanitizar datos del formulario
        $nombre = htmlspecialchars($_POST['nombreProducto']);
        $precio = filter_var($_POST['precioDescuento'], FILTER_VALIDATE_FLOAT);
        $descripcion = htmlspecialchars($_POST['descripcionProducto']);
        $stock = ($_POST['stockProducto']);
        $disponible = htmlspecialchars($_POST['disponibilidadProducto']);
        $categoria = htmlspecialchars($_POST['categoriaProducto'] ?? '');
        $id_producto = $_POST['id_producto'] ?? null;

        if (empty($categoria)) {
            throw new Exception('Error: Seleccione una categoría válida.');
        }

        // Validar datos
        if (empty($nombre) || $precio === false || $precio <= 0) {
            throw new Exception('Error: Datos del producto inválidos.');
        }

        // Manejar la subida de la imagen
        $imagen_producto = null; // Inicializa la variable

        // Obtener la imagen existente del producto desde la base de datos
        if ($id_producto) {
            $stmt = $conn->prepare("SELECT imagen_producto FROM producto WHERE id_producto = ?");
            $stmt->bind_param("i", $id_producto);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $imagen_producto = $row['imagen_producto']; // Asignar la imagen existente
            }
        }

        if (isset($_FILES['imagenProducto']) && $_FILES['imagenProducto']['error'] == UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['imagenProducto']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (!in_array($ext, $allowed)) {
                throw new Exception('Error: Tipo de archivo no permitido.');
            }

            $targetDirectory = "imgsVentas/";
            $newFilename = uniqid() . "." . $ext;
            $targetFile = $targetDirectory . $newFilename;

            if (!move_uploaded_file($_FILES['imagenProducto']['tmp_name'], $targetFile)) {
                throw new Exception('Error: Error al subir la imagen.');
            }

            $imagen_producto = $newFilename;
        }

        // Iniciar transacción
        $conn->begin_transaction();

        // Actualizar producto
        $stmt = $conn->prepare("UPDATE producto SET nombre = ?, descripcion = ?, precio = ?, stock = ?, categoria = ?, imagen_producto = ?, disponibilidad = ? WHERE id_producto = ?");
        $stmt->bind_param("ssdisssi", $nombre, $descripcion, $precio, $stock, $categoria, $imagen_producto, $disponible, $id_producto);

        if (!$stmt->execute()) {
            throw new Exception("Error al actualizar producto: " . $stmt->error);
        }

        // Insertar o actualizar horarios en la tabla `horario`
        if (isset($_POST['dias']) && is_array($_POST['dias'])) {
            foreach ($_POST['dias'] as $dia) {
                $horario = $_POST["horas" . ucfirst($dia)] ?? null;

                if (!empty($horario)) {
                    // Intentar actualizar el horario
                    $stmt = $conn->prepare("UPDATE horario SET horario = ? WHERE id_producto = ? AND dia = ?");
                    $stmt->bind_param("sis", $horario, $id_producto, $dia);

                    if (!$stmt->execute() || $stmt->affected_rows === 0) {
                        // Si no se actualizó, insertar nuevo horario
                        $stmt = $conn->prepare("INSERT INTO horario (id_producto, dia, horario) VALUES (?, ?, ?)");
                        $stmt->bind_param("iss", $id_producto, $dia, $horario);

                        if (!$stmt->execute()) {
                            throw new Exception("Error al insertar horario: " . $stmt->error);
                        }
                    }
                }
            }
        }

        // Confirmar transacción
        $conn->commit();

        // Retornar éxito
        header('Location: ../panel_ventas.html');
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
