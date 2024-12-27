-- Crear tabla principal de usuarios
CREATE TABLE usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL,
    apellido_paterno VARCHAR(80) NOT NULL,
    apellido_materno VARCHAR(80),
    correo VARCHAR(200) UNIQUE NOT NULL,
    telefono VARCHAR(20) UNIQUE,
    contraseña VARCHAR(200) NOT NULL,
    fecha_registro DATE NOT NULL DEFAULT CURRENT_DATE
);

-- Crear tabla específica para vendedores
CREATE TABLE vendedor (
    id_vendedor INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL UNIQUE,
    informacion_contacto TEXT,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Crear tabla específica para compradores
CREATE TABLE comprador (
    id_comprador INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL UNIQUE,
    direccion_envio TEXT,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Crear tabla de productos (asociada con vendedores)
CREATE TABLE producto (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(500) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL CHECK (stock >= 0),
    categoria VARCHAR(50),
    id_vendedor INT NOT NULL,
    FOREIGN KEY (id_vendedor) REFERENCES vendedor(id_vendedor) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Crear tabla de pedidos (relacionando compradores y vendedores)
CREATE TABLE pedido (
    id_pedido INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    id_comprador INT NOT NULL,
    id_vendedor INT NOT NULL,
    FOREIGN KEY (id_comprador) REFERENCES comprador(id_comprador) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_vendedor) REFERENCES vendedor(id_vendedor) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Crear tabla para reseñas (relacionando compradores y productos)
CREATE TABLE resena (
    id_resena INT AUTO_INCREMENT PRIMARY KEY,
    calificacion INT NOT NULL CHECK (calificacion BETWEEN 1 AND 5),
    comentario VARCHAR(500),
    fecha DATE NOT NULL,
    id_comprador INT NOT NULL,
    id_producto INT NOT NULL,
    FOREIGN KEY (id_comprador) REFERENCES comprador(id_comprador) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES producto(id_producto) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Crear tabla de métodos de pago (asociada con compradores)
CREATE TABLE metodo_pago (
    id_metodo_pago INT AUTO_INCREMENT PRIMARY KEY,
    tipo_pago VARCHAR(50) NOT NULL,
    id_comprador INT NOT NULL,
    FOREIGN KEY (id_comprador) REFERENCES comprador(id_comprador) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Crear tabla de detalle de pedidos (relacionando pedidos y productos)
CREATE TABLE detalle_pedido (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    cantidad INT NOT NULL CHECK (cantidad > 0),
    id_pedido INT NOT NULL,
    id_producto INT NOT NULL,
    FOREIGN KEY (id_pedido) REFERENCES pedido(id_pedido) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES producto(id_producto) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Crear tabla de notificaciones (relacionando usuarios y productos)
CREATE TABLE notificacion (
    id_notificacion INT AUTO_INCREMENT PRIMARY KEY,
    mensaje VARCHAR(500) NOT NULL,
    fecha DATE NOT NULL,
    id_usuario INT NOT NULL,
    id_producto INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES producto(id_producto) ON DELETE CASCADE ON UPDATE CASCADE
);