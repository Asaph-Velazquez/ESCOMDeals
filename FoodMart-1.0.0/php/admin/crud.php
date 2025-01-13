<?php session_start(); // Inicia la sesión para verificar si el usuario ha iniciado sesión 

include '../conexion.php';

// Obtén el ID del usuario de la sesión
$id_usuario_sesion = $_SESSION['id_usuario'];

// Obtener el término de búsqueda de la barra de búsqueda
$searchTerm = isset($_GET['buscar']) ? $_GET['buscar'] : ''; // Si no hay término, buscará todos

// Modificar la consulta para incluir la búsqueda
$sql = "SELECT * FROM usuario WHERE id_usuario != '$id_usuario_sesion' AND (id_usuario LIKE '%$searchTerm%' OR nombre LIKE '%$searchTerm%' OR apellido_paterno LIKE '%$searchTerm%' OR apellido_materno LIKE '%$searchTerm%' OR correo LIKE '%$searchTerm%' OR telefono LIKE '%$searchTerm%')";
$result = $conn->query($sql);

?>
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
                            <li><a href="login.php" class="rounded-circle bg-light p-2 mx-1">Ingresar</a></li>
                        <?php endif; ?>
                    </ul>
                </div>                
            </div>
        </div>
    </header>

    <!-- Perfil Section -->
    <section class="py-5">

    <div class="container mt-5">
    <h2>Tabla de Usuarios</h2>
    
    <!-- Fila para el botón y la barra de búsqueda -->
    <div class="row mb-4 justify-content-center">
        <!-- Botón de añadir usuario -->
        <div class="col-auto">
            <a href="agregar_usuario.php" class="btn btn-primary">Añadir Usuario</a>
        </div>
        
        <!-- Barra de búsqueda -->
        <div class="col-auto">
            <form method="get" action="crud.php">
    
            <input type="text" name="buscar" class="form-control" placeholder="Buscar usuario..." value="<?php echo isset($_GET['buscar']) ? $_GET['buscar'] : ''; ?>">
            </form>
        </div>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID Usuario</th>
                <th>Nombre</th>
                <th>Apellido Paterno</th>
                <th>Apellido Materno</th>
                <th>Correo</th>
                <th>Teléfono</th>
                <th>Fecha Registro</th>
                <th>Alias</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Verifica si hay resultados en la consulta
            if ($result->num_rows > 0) {
                // Salida de cada fila
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>" . $row["id_usuario"] . "</td>
                        <td>" . $row["nombre"] . "</td>
                        <td>" . $row["apellido_paterno"] . "</td>
                        <td>" . $row["apellido_materno"] . "</td>
                        <td>" . $row["correo"] . "</td>
                        <td>" . $row["telefono"] . "</td>
                        <td>" . $row["fecha_registro"] . "</td>
                        <td>" . $row["alias"] . "</td>
                        <td>
                            <a href='editar_usuario.php?id=" . $row["id_usuario"] . "' class='btn btn-warning btn-sm'>Editar</a>
                            <a href='eliminar_usuario.php?id=" . $row["id_usuario"] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"¿Estás seguro de que quieres eliminar este usuario?\")'>Borrar</a>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='11'>No se encontraron usuarios</td></tr>";
            }

            // Cierra la conexión a la base de datos
            $conn->close();
            ?>
        </tbody>
    </table>
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
    <!-- JavaScript para realizar la búsqueda sin recargar la página -->
    <script>
function buscarUsuario() {
    let query = document.getElementById('buscar').value.toLowerCase();
    let rows = document.querySelectorAll('#usuariosTableBody tr');
    
    rows.forEach(row => {
        let cells = row.querySelectorAll('td');
        let found = false;
        
        cells.forEach(cell => {
            if (cell.innerText.toLowerCase().includes(query)) {
                found = true;
            }
        });
        
        if (found) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
