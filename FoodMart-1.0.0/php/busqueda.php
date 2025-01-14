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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESCOMDeals - Resultados de Búsqueda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h2 class="text-center">Resultados de Búsqueda</h2>
        <div class="row mt-4">
            <?php if (!empty($productos)): ?>
                <?php foreach ($productos as $producto): ?>
                    <div class="col-md-4">
                        <div class="card">
                            <img src="php/imgsVentas/<?php echo htmlspecialchars($producto['imagen_producto']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($producto['nombre']); ?></h5>
                                <p class="card-text">Vendedor: <?php echo htmlspecialchars($producto['alias']); ?></p>
                                <p class="card-text">Precio: $<?php echo htmlspecialchars(number_format($producto['precio'], 2)); ?></p>
                                <a href="detalles.html?id_producto=<?php echo htmlspecialchars($producto['id_producto']); ?>" class="btn btn-primary">Ver Detalles</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">No se encontraron productos que coincidan con tu búsqueda.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
