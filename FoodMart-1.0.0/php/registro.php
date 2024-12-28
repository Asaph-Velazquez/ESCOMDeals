<?php
    session_start();
    $conexion = mysqli_connect('localhost', 'root', '', 'escomdeals');

    // Verificar conexión
    if (!$conexion) {
        die('Error al conectar con la base de datos: ' . mysqli_connect_error());
    }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recuperar valores del formulario
    $nombre = $_POST['nombre']??'';
    $email = $_POST['email']??'';
    $contrasena = $_POST['password']??'';
    $APaterno = $_POST['apellidoPaterno']??'';
    $AMaterno = $_POST['apellidoMaterno']??'';
    $telefono = $_POST['telefono']??'';

    
    // Validar datos obligatorios
    if (empty($nombre) || empty($email) || empty($contrasena) || empty($APaterno) || empty($AMaterno) || empty($telefono)) {
        echo json_encode(['success' => false, 'message' => 'Error: Todos los campos obligatorios deben ser completados.']);
        exit;
    }

    // Comprobar si el correo ya existe
    $consultaVerificacion = "SELECT * FROM usuario WHERE correo = ? OR nombre = ? OR apellido_paterno = ? OR apellido_materno = ? OR telefono = ?";
    $stmtVerificacion = mysqli_prepare($conexion, $consultaVerificacion);
    mysqli_stmt_bind_param($stmtVerificacion, 'sssss', $email, $nombre, $APaterno, $AMaterno, $telefono);
    mysqli_stmt_execute($stmtVerificacion);
    $resultadoVerificacion = mysqli_stmt_get_result($stmtVerificacion);

    if(mysqli_num_rows($resultadoVerificacion) > 0){
        echo json_encode(['success' => false, 'message' => 'Error: El correo o nombre ya están registrados.']);
        exit;
    }

    //cifrado de contraseña
    $contrasenaCifrada = password_hash($contrasena, PASSWORD_BCRYPT);

    // Insertar datos en la base de datos
    $stmt= mysqli_prepare($conexion, "INSERT INTO usuario (nombre, correo, contraseña, apellido_paterno, apellido_materno, telefono) VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'ssssss', $nombre, $email, $contrasenaCifrada,$APaterno, $AMaterno, $telefono);
    
    if(mysqli_stmt_execute($stmt)){
        $idUsuario = mysqli_insert_id($conexion);

        echo json_encode(['success' => true, 'message' => 'Registro exitoso.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al registrar los datos: ' . mysqli_error($conexion)]);
    }
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);    
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}

mysqli_close($conexion);
    
?>