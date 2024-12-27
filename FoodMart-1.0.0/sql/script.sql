-- Tabla principal de usuarios
CREATE TABLE usuario (
    id_usuario SERIAL PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL,
    apellido_paterno VARCHAR(80) NOT NULL,
    apellido_materno VARCHAR(80),
    correo VARCHAR(200) UNIQUE NOT NULL,
    telefono VARCHAR(20) UNIQUE,
    contraseña VARCHAR(200) NOT NULL,
    fecha_registro DATE NOT NULL DEFAULT CURRENT_DATE
);

-- Tabla específica para vendedores
CREATE TABLE vendedor (
    id_vendedor SERIAL PRIMARY KEY,
    id_usuario INT NOT NULL UNIQUE,
    informacion_contacto TEXT,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
);

-- Tabla específica para compradores
CREATE TABLE comprador (
    id_comprador SERIAL PRIMARY KEY,
    id_usuario INT NOT NULL UNIQUE,
    direccion_envio TEXT,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
);

-- Tabla de productos (asociada con vendedores)
CREATE TABLE producto (
    id_producto SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(500) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL CHECK (stock >= 0),
    categoria VARCHAR(50),
    id_vendedor INT NOT NULL,
    FOREIGN KEY (id_vendedor) REFERENCES vendedor(id_vendedor)
);

-- Tabla de pedidos (relacionando compradores y vendedores)
CREATE TABLE pedido (
    id_pedido SERIAL PRIMARY KEY,
    fecha DATE NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    id_comprador INT NOT NULL,
    id_vendedor INT NOT NULL,
    FOREIGN KEY (id_comprador) REFERENCES comprador(id_comprador),
    FOREIGN KEY (id_vendedor) REFERENCES vendedor(id_vendedor)
);

-- Tabla para reseñas (relacionando compradores y productos)
CREATE TABLE resena (
    id_resena SERIAL PRIMARY KEY,
    calificacion INT NOT NULL CHECK (calificacion BETWEEN 1 AND 5),
    comentario VARCHAR(500),
    fecha DATE NOT NULL,
    id_comprador INT NOT NULL,
    id_producto INT NOT NULL,
    FOREIGN KEY (id_comprador) REFERENCES comprador(id_comprador),
    FOREIGN KEY (id_producto) REFERENCES producto(id_producto)
);

-- Tabla de métodos de pago (asociada con compradores)
CREATE TABLE metodo_pago (
    id_metodo_pago SERIAL PRIMARY KEY,
    tipo_pago VARCHAR(50) NOT NULL,
    id_comprador INT NOT NULL,
    FOREIGN KEY (id_comprador) REFERENCES comprador(id_comprador)
);

-- Tabla de detalle de pedidos (relacionando pedidos y productos)
CREATE TABLE detalle_pedido (
    id_detalle SERIAL PRIMARY KEY,
    cantidad INT NOT NULL CHECK (cantidad > 0),
    id_pedido INT NOT NULL,
    id_producto INT NOT NULL,
    FOREIGN KEY (id_pedido) REFERENCES pedido(id_pedido),
    FOREIGN KEY (id_producto) REFERENCES producto(id_producto)
);

-- Tabla de notificaciones (relacionando usuarios y productos)
CREATE TABLE notificacion (
    id_notificacion SERIAL PRIMARY KEY,
    mensaje VARCHAR(500) NOT NULL,
    fecha DATE NOT NULL,
    id_usuario INT NOT NULL,
    id_producto INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
    FOREIGN KEY (id_producto) REFERENCES producto(id_producto)
);
