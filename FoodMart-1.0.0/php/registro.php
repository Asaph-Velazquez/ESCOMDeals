<?php
session_start(); // Inicia la sesión

$conexion = mysqli_connect('localhost', 'root', '', 'escomdeals');

// Verificar conexión
if (!$conexion) {
    die('Error al conectar con la base de datos: ' . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recuperar valores del formulario
    $nombre = $_POST['nombre'] ?? '';
    $email = $_POST['email'] ?? '';
    $contrasena = $_POST['password'] ?? '';
    $APaterno = $_POST['apellidoPaterno'] ?? '';
    $AMaterno = $_POST['apellidoMaterno'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $usuario = $_POST['usuario'] ?? '';

    // Validar datos obligatorios
    if (empty($nombre) || empty($email) || empty($contrasena) || empty($APaterno) || empty($AMaterno) || empty($telefono) || empty($usuario)) {
        echo json_encode(['success' => false, 'message' => 'Error: Todos los campos obligatorios deben ser completados.']);
        exit;
    }

    // Validar formato de correo
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Error: El formato del correo electrónico es inválido.']);
        exit;
    }

    // Comprobar si el correo o el usuario ya existen
    $consultaVerificacion = "SELECT * FROM usuario WHERE correo = ? OR alias = ?";
    $stmtVerificacion = mysqli_prepare($conexion, $consultaVerificacion);
    mysqli_stmt_bind_param($stmtVerificacion, 'ss', $email, $usuario);
    mysqli_stmt_execute($stmtVerificacion);
    $resultadoVerificacion = mysqli_stmt_get_result($stmtVerificacion);

    if (mysqli_num_rows($resultadoVerificacion) > 0) {
        echo json_encode(['success' => false, 'message' => 'Error: El correo o el nombre de usuario ya estan registrados.']);
        exit;
    }

    // Cifrado de contraseña
    $contrasenaCifrada = password_hash($contrasena, PASSWORD_BCRYPT);

    // Insertar datos en la base de datos
    $stmt = mysqli_prepare($conexion, "INSERT INTO usuario (nombre, correo, contraseña, apellido_paterno, apellido_materno, telefono, alias) VALUES (?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'sssssss', $nombre, $email, $contrasenaCifrada, $APaterno, $AMaterno, $telefono, $usuario);
    
    if (mysqli_stmt_execute($stmt)) {
        // Guardar el usuario en la sesión
        $usuarioRegistrado = mysqli_insert_id($conexion);
        $_SESSION['id_usuario'] = $usuarioRegistrado;
        $_SESSION['alias'] = $usuario;
        $_SESSION['foto_perfil'] = 'uploads/perfil.png'; // Ruta por defecto si no hay foto
        $_SESSION['telefono'] = $telefono;
        $_SESSION['correo'] = $email;
        $_SESSION['nombre'] = $nombre;
        $_SESSION['apellido_paterno'] = $APaterno;
        $_SESSION['apellido_materno'] = $AMaterno;

                // Insertar en la tabla vendedor
                $stmtVendedor = mysqli_prepare($conexion, "INSERT INTO vendedor (id_usuario) VALUES (?)");
                mysqli_stmt_bind_param($stmtVendedor, 'i', $usuarioRegistrado);
                mysqli_stmt_execute($stmtVendedor);
                mysqli_stmt_close($stmtVendedor);
        
                // Insertar en la tabla comprador
                $stmtComprador = mysqli_prepare($conexion, "INSERT INTO comprador (id_usuario) VALUES (?)");
                mysqli_stmt_bind_param($stmtComprador, 'i', $usuarioRegistrado);
                mysqli_stmt_execute($stmtComprador);
                mysqli_stmt_close($stmtComprador);

        // Redirigir a la página de inicio
        header('Location: ../index.html');
        exit();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al registrar los datos: ' . mysqli_error($conexion)]);
    }

    mysqli_stmt_close($stmt);    
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}

mysqli_close($conexion);
?>
