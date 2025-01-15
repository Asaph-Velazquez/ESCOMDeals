<?php session_start(); // Inicia la sesión para verificar si el usuario ha iniciado sesión ?>
<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "escomdeals";  // Ajusta el nombre de la base de datos si es necesario

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Capturar el término de búsqueda desde la URL
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Consulta para buscar por nombre de producto o alias del vendedor
$sql = "SELECT p.*, u.alias 
        FROM producto p
        JOIN usuario u ON p.id_vendedor = u.id_usuario
        WHERE (p.nombre LIKE '%$search%' OR u.alias LIKE '%$search%') 
        AND p.stock > 0";

// Ejecutar la consulta y obtener los resultados
$result = $conn->query($sql);

// Procesar los productos encontrados
$productos = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $productos[] = $row;
    }
} else {
    echo "<p class='text-center'>No se encontraron resultados para \"$search\".</p>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>ESCOMDeals - Compra y Venta en ESCOM</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ESCOMDeals: plataforma para compra y venta entre estudiantes de ESCOM">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../../accesorios.html">
    <link rel="stylesheet" type="text/css" href="../../style.css">
    <link rel="stylesheet" href="../../css/main.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/footer.css">
    <link rel="stylesheet" href="../../css/los_chidos/sesion.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

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
                    <div class="search-bar bg-light p-2 my-2 rounded-4">
                        <form id="search-form" action="busqueda.php" method="get">
                            <input name="search" type="text" class="form-control border-0" placeholder="Buscar productos en ESCOMDeals" />
                        </form>
                    </div>
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

    <div class="container my-5">
        <h2 class="text-center">Resultados de Búsqueda</h2>
        <div class="row mt-4">
            <?php if (!empty($productos)): ?>
                <?php foreach ($productos as $producto): ?>
                <div class="col-md-4 col-sm-6 mb-4">
                    <div class="card product-card position-relative">
                        <!-- Imagen del producto -->
                        <img src="../imgsVentas/<?php echo htmlspecialchars($producto['imagen_producto'])?>" class="card-img-top" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                        
                        <!-- Información al pasar el ratón -->
                        <div class="product-info-overlay d-flex flex-column align-items-center justify-content-center position-absolute w-100 h-100">
                                    <h5 class="card-title text-white"><?php echo htmlspecialchars($producto['nombre']); ?></h5>
                                    <p class="card-text text-white">$<?php echo htmlspecialchars(number_format($producto['precio'], 2)); ?></p>
                                    <p class="card-text text-white small"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                                    <div class="d-flex gap-2 mt-2">
                                       
                                        <button 
                                            type="button" 
                                            class="btn btn-danger" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#confirmDeleteModal<?php echo $producto['id_producto']; ?>"
                                            data-id="<?php echo $producto['id_producto']; ?>"
                                            data-nombre="<?php echo htmlspecialchars($producto['nombre']); ?>">
                                            Eliminar
                                        </button>                        
                                    </div>    
                                </div>
                    </div>
                </div>
                <!-- Modal de confirmación -->
                <div class="modal fade" id="confirmDeleteModal<?php echo $producto['id_producto']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Confirmar Eliminación</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        ¿Estás seguro de que deseas eliminar el producto <span id="productoNombre"><?php echo htmlspecialchars($producto['nombre']); ?></span>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <form id="deleteForm" action="eliminar_producto.php" method="POST">
                                            <input type="hidden" name="id_producto" id="productoId" value="<?php echo $producto['id_producto']; ?>">
                                            <button type="submit" class="btn btn-danger">Eliminar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">No se encontraron productos que coincidan con tu búsqueda.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <footer>
        <div class="container-fluid">
            <p>&copy; 2024 ESCOMDeals - Todos los derechos reservados.</p>
            <a href="terminos.html" class="text-white">Términos y Condiciones</a> |
            <a href="privacidad.html" class="text-white">Política de Privacidad</a>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../js/main.js"></script>
</body>
</html>
