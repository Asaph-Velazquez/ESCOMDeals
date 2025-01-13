<?php
session_start();
include '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['usuario'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $response = ['status' => 'success', 'message' => ''];

    // Validación de número de teléfono (10 dígitos)
    if (strlen($phone) != 10 || !preg_match('/^\d+$/', $phone)) {
        $response['status'] = 'error';
        $response['message'] = "El número de teléfono debe tener 10 dígitos.";
        echo json_encode($response);
        exit;
    }

    // Verificar si el usuario, correo o número ya existen en la base de datos
    $conn = new mysqli("localhost", "usuario", "contraseña", "base_de_datos");
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Comprobar si el nombre de usuario ya existe
    $result = $conn->query("SELECT * FROM usuario WHERE usuario = '$usuario'");
    if ($result->num_rows > 0) {
        $response['status'] = 'error';
        $response['message'] = "El nombre de usuario ya está registrado.";
        echo json_encode($response);
        exit;
    }

    // Comprobar si el correo electrónico ya existe
    $result = $conn->query("SELECT * FROM usuario WHERE email = '$email'");
    if ($result->num_rows > 0) {
        $response['status'] = 'error';
        $response['message'] = "El correo electrónico ya está registrado.";
        echo json_encode($response);
        exit;
    }

    // Comprobar si el número de teléfono ya existe
    $result = $conn->query("SELECT * FROM usuario WHERE phone = '$phone'");
    if ($result->num_rows > 0) {
        $response['status'] = 'error';
        $response['message'] = "El número de teléfono ya está registrado.";
        echo json_encode($response);
        exit;
    }

    // Si todo está bien, insertar el nuevo usuario
    $stmt = $conn->prepare("INSERT INTO usuario (usuario, email, phone) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $usuario, $email, $phone);
    $stmt->execute();

    $response['message'] = "¡Usuario registrado con éxito!";
    echo json_encode($response);
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Nuevo Usuario - ESCOMDeals</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Registro de nuevo usuario en ESCOMDeals.">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" href="../../css/perfil.css">
    <link rel="stylesheet" href="../../css/footer.css">
    <link rel="stylesheet" href="../../css/los_chidos/sesion.css">

    <script>
        // Validación del formulario
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('profile-form');
            form.addEventListener('submit', function(event) {
                let errors = [];

                const usuario = document.getElementById('usuario').value;
                const email = document.getElementById('email').value;
                const phone = document.getElementById('phone').value;

                // Validación del número de teléfono (10 dígitos)
                if (phone.length !== 10 || !/^\d+$/.test(phone)) {
                    errors.push("El número de teléfono debe tener 10 dígitos.");
                }

                // Validación de campos vacíos
                if (usuario === "" || email === "" || phone === "") {
                    errors.push("Todos los campos son obligatorios.");
                }

                // Mostrar errores si existen
                if (errors.length > 0) {
                    event.preventDefault();  // Evitar el envío del formulario
                    document.getElementById('error-messages').innerHTML = errors.join("<br>");
                }
            });
        });
    </script>
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
                            <li><a href="login.php" class="rounded-circle bg-light p-2 mx-1">Ingresar</a></li>
                        <?php endif; ?>
                    </ul>
                </div>                
            </div>
        </div>
    </header>

    <!-- Nuevo Usuario Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-4">Nuevo Usuario</h2>
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card p-4 shadow-sm">
                        <form id="profile-form" method="POST" action="crud.php" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="usuario" class="form-label">Usuario</label>
                                <input type="text" name="usuario" id="usuario" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" name="nombre" id="nombre" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="apellido_paterno" class="form-label">Apellido Paterno</label>
                                <input type="text" name="apellido_paterno" id="apellido_paterno" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="apellido_materno" class="form-label">Apellido Materno</label>
                                <input type="text" name="apellido_materno" id="apellido_materno" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email" name="email" id="email" class="form-control" required 
                                    pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" 
                                    title="El correo electrónico debe ser válido (ejemplo: ejemplo@dominio.com)">
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Teléfono</label>
                                <input type="text" name="phone" id="phone" class="form-control" maxlength="10" required 
                                    pattern="\d{10}" title="El número de teléfono debe tener solo 10 dígitos" oninput="this.value=this.value.replace(/\D/g,'');">
                            </div>

                            <div class="d-flex justify-content-center">
                                <button type="submit" class="btn btn-success">Registrar Usuario</button>
                            </div>
                        </form>                        
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mostrar mensajes de error -->
    <div id="error-messages" style="color: red;"></div>
    <script>
    // Validación del formulario usando AJAX
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('profile-form');
        form.addEventListener('submit', function(event) {
            event.preventDefault(); // Evitar el envío del formulario tradicional

            let errors = [];
            const usuario = document.getElementById('usuario').value;
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;

            // Validación del número de teléfono (10 dígitos)
            if (phone.length !== 10 || !/^\d+$/.test(phone)) {
                errors.push("El número de teléfono debe tener 10 dígitos.");
            }

            // Validación de campos vacíos
            if (usuario === "" || email === "" || phone === "") {
                errors.push("Todos los campos son obligatorios.");
            }

            // Mostrar errores si existen
            if (errors.length > 0) {
                document.getElementById('error-messages').innerHTML = errors.join("<br>");
            } else {
                // Si no hay errores, se hace la solicitud AJAX
                const formData = new FormData(form);
                fetch('crud.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'error') {
                        document.getElementById('error-messages').innerHTML = data.message;
                    } else {
                        // Mostrar éxito o redirigir si es necesario
                        alert(data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        });
    });
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
