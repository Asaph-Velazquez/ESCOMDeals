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

    // Actualizar datos en la base de datos
    $stmt = mysqli_prepare($conexion, "UPDATE usuario SET alias = ?, nombre = ?, correo = ?, telefono = ?, apellido_paterno=?, apellido_materno = ? WHERE id_usuario = ?");
    mysqli_stmt_bind_param($stmt, 'ssssssi', $usuario, $nombre, $email, $telefono, $Apaterno, $Amaterno,  $user_id);

    if (mysqli_stmt_execute($stmt)) {
        // Actualizar variables de sesión con los nuevos datos
        $_SESSION['nombre'] = $nombre;
        $_SESSION['correo'] = $email;
        $_SESSION['telefono'] = $telefono;
        $_SESSION['alias'] = $usuario;
        $_SESSION['apellido_paterno'] = $Apaterno;
        $_SESSION['apellido_materno'] = $Amaterno;

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
