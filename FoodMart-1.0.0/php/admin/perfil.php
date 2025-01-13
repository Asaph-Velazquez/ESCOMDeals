<?php session_start(); // Inicia la sesión para verificar si el usuario ha iniciado sesión ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Perfil - ESCOMDeals</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Perfil del usuario en ESCOMDeals.">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" href="../..//css/perfil.css">
    <link rel="stylesheet" href="../../css/footer.css">
    <link rel="stylesheet" href="../../css/los_chidos/sesion.css">

    <script>
        // Cargar datos en el formulario desde las variables de sesión de PHP
        window.onload = function() {
            // Las variables de sesión se manejan directamente en el HTML
        };

        // Habilitar edición del formulario
        function enableEdit() {
            const inputs = document.querySelectorAll(".form-control");
            inputs.forEach(input => input.removeAttribute("disabled"));
            document.getElementById("save-button").classList.remove("d-none");
            document.getElementById('upload-button').style.display = 'inline'; // Mostrar el botón "Cambiar Foto"
        }

        // Guardar cambios del perfil
        function saveProfile() {
            const inputs = document.querySelectorAll(".form-control");
            inputs.forEach(input => input.setAttribute("disabled", "true"));
            document.getElementById("save-button").classList.add("d-none");
            document.getElementById('upload-button').style.display = 'none'; // Ocultar el botón "Cambiar Foto"
            alert("¡Perfil actualizado con éxito!");
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Habilitar edición del formulario
            document.getElementById('edit-button').addEventListener('click', function() {
                // Habilitar campos para edición
                document.getElementById('usuario').disabled = false;
                // Habilitar otros campos similares
                document.getElementById('photo-upload').style.display = 'block';
                document.getElementById('edit-button').classList.add('d-none');
                document.getElementById('save-button').classList.remove('d-none');
                document.getElementById('upload-button').style.display = 'inline'; // Mostrar el botón "Cambiar Foto"
            });

            document.getElementById('upload-button').addEventListener('click', function() {
                document.getElementById('photo-upload').click();
            });
        });

        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profile-image').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        }
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

    <!-- Perfil Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-4">Mi Perfil</h2>
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card p-4 shadow-sm">
                        <form id="profile-form" method="POST" action="modificarPerfil.php" enctype="multipart/form-data">
                            <section class="profile-photo">
                                <div class="photo-container">
                                    <img id="profile-image" src="php/<?php echo $_SESSION['foto_perfil']; ?>" alt="Foto de perfil" class="rounded-circle" style="object-fit: cover;">
                                    <br>
                                    <input type="file" accept=".PNG, .JPG, .JPEG" name="foto_perfil" id="photo-upload" accept="image/*" style="display: none;" onchange="previewImage(event)">
                                    <button type="button" id="upload-button" style="display: none;" onclick="document.getElementById('photo-upload').click()">Cambiar Foto</button>
                                </div>
                            </section>
                            <div class="mb-3">
                                <label for="Usuario" class="form-label">Usuario</label>
                                <input type="text" name="usuario" id="usuario" class="form-control" value="<?php echo $_SESSION['alias']; ?>" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="name" class="form-label">Nombre</label>
                                <input type="text" name="name" id="name" class="form-control" value="<?php echo $_SESSION['nombre']; ?>" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="apellido_paterno" class="form-label">Apellido Paterno</label>
                                <input type="text" name="apellido_paterno" id="apellido_paterno" class="form-control" value="<?php echo $_SESSION['apellido_paterno']; ?>" disabled>
                            </div>                            
                            <div class="mb-3">
                                <label for="apellido_materno" class="form-label">Apellido Materno</label>
                                <input type="text" name="apellido_materno" id="apellido_materno" class="form-control" value="<?php echo $_SESSION['apellido_materno']; ?>" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email" name="email" id="email" class="form-control" value="<?php echo $_SESSION['correo']; ?>" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Teléfono</label>
                                <input type="text" name="phone" id="phone" class="form-control" value="<?php echo $_SESSION['telefono']; ?>" disabled>
                            </div>  
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-primary" onclick="enableEdit()" style="color: black;">Editar</button>
                                <button type="submit" class="btn btn-success d-none" id="save-button">Guardar Cambios</button>
                            </div>
                        </form>                        
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container-fluid">
            <p>&copy; 2024 ESCOMDeals - Todos los derechos reservados.</p>
            <a href="terminos.html" class="text-white">Términos y Condiciones</a> |
            <a href="privacidad.html" class="text-white">Política de Privacidad</a>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
