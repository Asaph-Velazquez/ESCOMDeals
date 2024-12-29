<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conexion = mysqli_connect('localhost', 'root', '', 'escomdeals');

// Verificar conexión
if (!$conexion) {
    die(json_encode(['success' => false, 'message' => 'Error al conectar con la base de datos: ' . mysqli_connect_error()]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si el usuario está autenticado
    if (!isset($_SESSION['id_usuario'])) {
        die(json_encode(['success' => false, 'message' => 'Error: Usuario no autenticado.']));
    }

    // Recuperar valores del formulario
    $usuario = trim($_POST['usuario'] ?? '');
    $nombre = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['phone'] ?? '');
    $Apaterno = trim($_POST['apellido_paterno'] ?? '');
    $Amaterno = trim($_POST['apellido_materno'] ?? '');
    $user_id = $_SESSION['id_usuario'];

    // Validar datos obligatorios
    if (empty($nombre) || empty($email) || empty($telefono)) {
        echo json_encode(['success' => false, 'message' => 'Error: Todos los campos obligatorios deben ser completados.']);
        exit;
    }

    // Manejar la carga de la foto de perfil
    $targetFile = null;
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
        $targetDir = 'uploads/';
        /*
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true); // Crear directorio si no existe
            }
        */

        $targetFile = $targetDir . basename($_FILES['foto_perfil']['name']);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Verificar si el archivo es una imagen
        $check = getimagesize($_FILES['foto_perfil']['tmp_name']);
        if ($check === false) {
            echo json_encode(['success' => false, 'message' => 'El archivo no es una imagen válida.']);
            exit;
        }

        // Verificar el tamaño del archivo (por ejemplo, máximo 5MB)
        if ($_FILES['foto_perfil']['size'] > 5000000) {
            echo json_encode(['success' => false, 'message' => 'El archivo es demasiado grande.']);
            exit;
        }

        // Verificar el tipo de archivo
        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png'])) {
            echo json_encode(['success' => false, 'message' => 'Solo se permiten archivos JPG, JPEG o PNG.']);
            exit;
        }

        // Mover el archivo a la carpeta de destino
        if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $targetFile)) {
            // Si se subió una nueva foto, actualizar la base de datos
            $stmt = mysqli_prepare($conexion, "UPDATE usuario SET alias = ?, nombre = ?, correo = ?, telefono = ?, apellido_paterno=?, apellido_materno = ?, foto_perfil = ? WHERE id_usuario = ?");
            mysqli_stmt_bind_param($stmt, 'sssssssi', $usuario, $nombre, $email, $telefono, $Apaterno, $Amaterno, $targetFile, $user_id);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al mover el archivo.']);
            exit;
        }
    } else {
        // Si no se sube una nueva imagen, solo actualizar los demás datos
        $stmt = mysqli_prepare($conexion, "UPDATE usuario SET alias = ?, nombre = ?, correo = ?, telefono = ?, apellido_paterno=?, apellido_materno = ? WHERE id_usuario = ?");
        mysqli_stmt_bind_param($stmt, 'ssssssi', $usuario, $nombre, $email, $telefono, $Apaterno, $Amaterno, $user_id);
    }

    // Ejecutar la consulta de actualización
    if (mysqli_stmt_execute($stmt)) {
        // Actualizar variables de sesión con los nuevos datos
        $_SESSION['nombre'] = $nombre;
        $_SESSION['correo'] = $email;
        $_SESSION['telefono'] = $telefono;
        $_SESSION['alias'] = $usuario;
        $_SESSION['apellido_paterno'] = $Apaterno;
        $_SESSION['apellido_materno'] = $Amaterno;

        // Si se subió una foto, también actualizar la variable de sesión
        if (isset($targetFile)) {
            $_SESSION['foto_perfil'] = $targetFile;
        }

        echo json_encode(['success' => true, 'message' => 'Perfil actualizado con éxito.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar los datos: ' . mysqli_error($conexion)]);
    }

    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}

mysqli_close($conexion);
?>
