<?php
session_start();
include_once '../conexion.php';

// Verificar si el ID está presente en la URL
if (isset($_GET['id'])) {
    $id_usuario = $_GET['id'];
    
    // Consulta para obtener los datos del usuario
    $query = "SELECT * FROM usuario WHERE id_usuario = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        // Asignar los datos del usuario a variables
        $usuario = $resultado->fetch_assoc();
    } else {
        // Si no se encuentra el usuario
        echo "Usuario no encontrado.";
        exit;
    }
} else {
    echo "ID de usuario no proporcionado.";
    exit;
}

// Procesar la actualización del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir los datos del formulario
    $nombre = $_POST['nombre'];
    $apellidoPaterno = $_POST['apellidoPaterno'];
    $apellidoMaterno = $_POST['apellidoMaterno'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $usuario = $_POST['usuario'];

    // Actualizar los datos en la base de datos
    $updateQuery = "UPDATE usuario SET nombre = ?, apellido_paterno = ?, apellido_materno = ?, correo = ?, telefono = ?, alias = ? WHERE id_usuario = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param('ssssssi', $nombre, $apellidoPaterno, $apellidoMaterno, $email, $telefono, $usuario, $id_usuario);
    if ($updateStmt->execute()) {
        header('Location: crud.php');
        } else {
        echo "Error al actualizar los datos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>ESCOMDeals - Editar usuario</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ESCOMDeals: plataforma para compra y venta entre estudiantes de ESCOM">

    <link rel="stylesheet" type="text/css" href="../../style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../../css/registro.css">
    <link rel="stylesheet" href="../../css/footer.css">
    <link rel="stylesheet" href="../../css/los_chidos/sesion.css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</head>
<body>
<header class="fixed-top bg-light">
        <div class="container-fluid">
            <div class="row py-3 border-bottom">
                <div class="col-sm-4 col-lg-3 text-center text-sm-start">
                    <div class="main-logo">
                        <a href="index.php">
                            <img src="../../images/logo.png" alt="ESCOMDeals" class="img-fluid">
                        </a>
                    </div>
                </div>
                
                <!-- Search bar -->
                <div class="col-sm-6 col-lg-5 d-none d-lg-block">

        
                </div>
                
                <!-- User options -->
                <div class="col-sm-8 col-lg-4 d-flex justify-content-end gap-5 align-items-center mt-4 mt-sm-0">        
                    <ul class="d-flex list-unstyled m-0">
                        <li><a href="index.php" class="rounded-circle bg-light p-2 mx-1">Inicio</a></li>
                        <?php if (isset($_SESSION['alias'])): ?>
                            <li><a href="crud.php" class="rounded-circle bg-light p-2 mx-1">Panel</a></li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle d-flex align-items-center" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="../<?php echo $_SESSION['foto_perfil']; ?>" alt="Foto de perfil" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                    <span class="ms-2"><?php echo $_SESSION['alias']; ?></span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                                    <li><a class="dropdown-item" href="perfil.php">Ver Perfil</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="./../logout.php"><span>Cerrar Sesión</span></a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li><a href="login.html" class="rounded-circle bg-light p-2 mx-1">Ingresar</a></li>
                        <?php endif; ?>
                    </ul>
                </div>                
            </div>
        </div>
    </header>

<div class="main-content">
    <div class="loginrey">
        <img src="../../images/login.png" alt="Logo" class="logo">
        
        <h1 class="text-center my-4">Editar Usuario</h1>

        <!-- Formulario de edición de usuario -->
        <form id="registroForm" method="post" >
            <div class="row g-4 justify-content-center">
                <div class="col-md-4 d-flex justify-content-center">
                    <div class="form-floating w-100">
                        <input type="text" class="form-control" id="nombres" placeholder="Nombres" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                        <label for="nombres">Nombre</label>
                    </div>
                </div>
                <div class="col-md-4 d-flex justify-content-center">
                    <div class="form-floating w-100">
                        <input type="text" class="form-control" id="apellidoPaterno" placeholder="Apellido paterno" name="apellidoPaterno" value="<?php echo htmlspecialchars($usuario['apellido_paterno']); ?>" required>
                        <label for="apellidoPaterno">Apellido paterno</label>
                    </div>
                </div>
                <div class="col-md-4 d-flex justify-content-center">
                    <div class="form-floating w-100">
                        <input type="text" class="form-control" id="apellidoMaterno" placeholder="Apellido materno" name="apellidoMaterno" value="<?php echo htmlspecialchars($usuario['apellido_materno']); ?>" required>
                        <label for="apellidoMaterno">Apellido materno</label>
                    </div>
                </div>
                <div class="col-md-6 d-flex justify-content-center">
                    <div class="form-floating w-100">
                        <input type="email" class="form-control" id="correoElectronico" placeholder="Correo electrónico" name="email" value="<?php echo htmlspecialchars($usuario['correo']); ?>" required>
                        <label for="correoElectronico">Correo electrónico</label>
                    </div>
                </div>
                <div class="col-md-6 d-flex justify-content-center">
                    <div class="form-floating w-100">
                        <input type="tel" class="form-control" id="numeroTelefono" placeholder="Número de teléfono" name="telefono" value="<?php echo htmlspecialchars($usuario['telefono']); ?>" required maxlength="10">
                        <label for="numeroTelefono">Número de teléfono</label>
                    </div>
                </div>
                <div class="col-md-4 d-flex justify-content-center">
                    <div class="form-floating w-100">
                        <input type="text" class="form-control" id="usuario" placeholder="Usuario" name="usuario" value="<?php echo htmlspecialchars($usuario['alias']); ?>" required>
                        <label for="usuario">Usuario</label>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary" name="actualizar">Actualizar</button>
            </div>
        </form>
    </div>
</div>

<footer>
    <div class="container-fluid">
        <p>&copy; 2024 ESCOMDeals - Todos los derechos reservados.</p>
        <a href="terminos.html" class="text-white">Términos y Condiciones</a> |
        <a href="privacidad.html" class="text-white">Política de Privacidad</a>
    </div>
</footer>

<script src="./validacionesForm.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script src="./../../js/main.js"></script>

</body>
</html>
