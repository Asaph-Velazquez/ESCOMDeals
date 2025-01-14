<?php
session_start();
include_once '../conexion.php';  // Asegúrate de incluir la conexión correctamente
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <title>ESCOMDeals - Registro de Usuario</title>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    

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
            
            <!-- Título principal -->
            <h1 class="text-center my-4">Registro de Usuario</h1>
                
                <form id="registroForm" method="post" action="registro.php">
                    <div class="row g-4 justify-content-center">
                        <!-- Primera fila: Nombre, Apellido Paterno, Apellido Materno -->
                        <div class="col-md-4 d-flex justify-content-center">
                            <div class="form-floating w-100">
                                <input type="text" class="form-control" id="nombres" placeholder="Nombres" name="nombre" required>
                                <div class="mensaje-invalido"></div>
                                <label for="nombres">Nombre</label>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex justify-content-center">
                            <div class="form-floating w-100">
                                <input type="text" class="form-control" id="apellidoPaterno" placeholder="Apellido paterno" name="apellidoPaterno" required>
                                <div class="mensaje-invalido"></div>
                                <label for="apellidoPaterno">Apellido paterno</label>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex justify-content-center">
                            <div class="form-floating w-100">
                                <input type="text" class="form-control" id="apellidoMaterno" placeholder="Apellido materno" name="apellidoMaterno" required>
                                <div class="mensaje-invalido"></div>
                                <label for="apellidoMaterno">Apellido materno</label>
                            </div>
                        </div>

                        <!-- Segunda fila: Correo Electrónico, Teléfono -->
                        <div class="col-md-6 d-flex justify-content-center">
                            <div class="form-floating w-100">
                                <input type="email" class="form-control" id="correoElectronico" placeholder="Correo electrónico" name="email" required>
                                <div class="mensaje-invalido"></div>
                                <label for="correoElectronico">Correo electrónico</label>
                            </div>
                        </div>
                        <div class="col-md-6 d-flex justify-content-center">
                            <div class="form-floating w-100">
                                <input type="tel" class="form-control" id="numeroTelefono" placeholder="Número de teléfono" name="telefono" required maxlength="10">
                                <div class="mensaje-invalido"></div>
                                <label for="numeroTelefono">Número de teléfono</label>
                            </div>
                        </div>

                        <!-- Tercera fila: Usuario, Contraseña, Confirmar Contraseña -->
                        <div class="col-md-4 d-flex justify-content-center">
                            <div class="form-floating w-100">
                                <input type="text" class="form-control" id="usuario" placeholder="Usuario" name="usuario" required>
                                <div class="mensaje-invalido"></div>
                                <label for="usuario">Usuario</label>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex justify-content-center">
                            <div class="form-floating w-100">
                                <input type="password" class="form-control" id="contraseña" placeholder="Contraseña" name="password" required>
                                <div class="mensaje-invalido"></div>
                                <label for="contraseña">Contraseña</label>
                            </div>
                            <div class="ojo">
                                <i class="fa-solid fa-eye ms-2" id="togglePassword" style="cursor: pointer;"></i>
                            </div>
                        </div>

                        <div class="col-md-4 d-flex justify-content-center">
                            <div class="form-floating w-100">
                                <input type="password" class="form-control" id="confirmarContraseña" placeholder="Confirmar contraseña" name="confirmarPassword" required>
                                <div class="mensaje-invalido"></div>
                                <label for="confirmarContraseña">Confirmar contraseña</label>
                            </div>
                            <div class="ojo">
                                <i class="fa-solid fa-eye ms-2" id="toggleConfirmPassword" style="cursor: pointer;"></i>
                            </div>
                        </div>
                    </div>

                    <form id="registroForm" method="post" action="registro.php">
                        <!-- Contenido del formulario... -->
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary">Registrar</button>
                        </div>
                    </form>

                </form>

        </div>
    </div>

    <!-- Modal para Mensaje -->
    <div class="modal fade" id="mensajeModal" tabindex="-1" aria-labelledby="mensajeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="mensajeModalLabel">Estado del Registro</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <!-- Mensaje dinámico -->
            <p id="mensajeModalTexto"></p>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
        </div>
    </div>
  
    <script>
        function mostrarModal(mensaje, exito = true) {
        const modalTexto = document.getElementById('mensajeModalTexto');
        modalTexto.textContent = mensaje;
    
        if (exito) {
            modalTexto.style.color = 'green';
        } else {
            modalTexto.style.color = 'red';
        }
    
        const modal = new bootstrap.Modal(document.getElementById('mensajeModal'));
        modal.show();
    }
    </script>

    <!-- Footer -->
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