<?php
    session_start();
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
