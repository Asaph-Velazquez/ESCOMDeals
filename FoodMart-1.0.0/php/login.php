<?php
session_start(); // Inicia la sesión

$conexion = mysqli_connect('localhost', 'root', '', 'escomdeals');

if (!$conexion) {
    die('Error al conectar con la base de datos: ' . mysqli_connect_error());
}

$email = $_POST['email'] ?? '';
$contrasena = $_POST['password'] ?? '';

$consulta = "SELECT * FROM usuario WHERE correo = '$email'";
$resultado = mysqli_query($conexion, $consulta);

if (!$resultado) {
    die('Error en la consulta: ' . mysqli_error($conexion));
}

// Validar si el usuario existe
if (mysqli_num_rows($resultado) > 0) {
    $usuario = mysqli_fetch_assoc($resultado);
    if (password_verify($contrasena, $usuario['contraseña'])) {
        // Guardar información en la sesión
        $_SESSION['alias'] = $usuario['alias']; // Cambia 'nombre' por el nombre real de la columna en tu tabla
        $_SESSION['foto_perfil'] = $usuario['foto_perfil'] ?? './images/las_chidas/perfil.png'; // Ruta por defecto si no hay foto
        $_SESSION['telefono'] = $usuario['telefono'];
        $_SESSION['correo'] = $usuario['correo'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['apellido_paterno'] = $usuario['apellido_paterno'];
        $_SESSION['apellido_materno'] = $usuario['apellido_materno'];
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        
        // Redirigir al inicio
        header("Location: ../index.html");
        exit();
    } else {
        echo 'La contraseña es incorrecta.';
    }
} else {
    echo 'El usuario no existe.';
}

mysqli_close($conexion);
?>
