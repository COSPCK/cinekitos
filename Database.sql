CREATE DATABASE IF NOT EXISTS CineDB;
USE CineDB;

-- Tabla usuarios
CREATE TABLE IF NOT EXISTS Usuarios (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    nombre_usuario VARCHAR(255) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla cines
CREATE TABLE IF NOT EXISTS Cines (
    id_cine INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(255) NOT NULL,
    direccion VARCHAR(255) NOT NULL
);

-- Tabla películas
CREATE TABLE IF NOT EXISTS Peliculas (
    id_pelicula INT PRIMARY KEY AUTO_INCREMENT,
    imagen VARCHAR(255),
    titulo VARCHAR(255) NOT NULL,
    descripcion VARCHAR(255),
    duracion_minutos INT,
    genero VARCHAR(100)
);

-- Tabla salas
CREATE TABLE IF NOT EXISTS Salas (
    id_sala INT PRIMARY KEY AUTO_INCREMENT,
    id_cine INT NOT NULL,
    numero_sala VARCHAR(50) NOT NULL,
    capacidad INT,
    FOREIGN KEY (id_cine) REFERENCES Cines(id_cine)
);

-- Tabla asientos
CREATE TABLE IF NOT EXISTS Asientos (
    id_asiento INT PRIMARY KEY AUTO_INCREMENT,
    id_sala INT NOT NULL,
    fila VARCHAR(10),
    numero VARCHAR(10),
    FOREIGN KEY (id_sala) REFERENCES Salas(id_sala)
);

-- Tabla  funciones
CREATE TABLE IF NOT EXISTS Funciones (
    id_funcion INT PRIMARY KEY AUTO_INCREMENT,
    id_pelicula INT NOT NULL,
    id_sala INT NOT NULL,
    fecha_hora DATETIME NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (id_pelicula) REFERENCES Peliculas(id_pelicula),
    FOREIGN KEY (id_sala) REFERENCES Salas(id_sala)
);

-- Compra de boletos
CREATE TABLE IF NOT EXISTS CompraBoletos (
    id_compra_boleto INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    id_funcion INT NOT NULL,
    id_asiento INT,
    folio_boleto VARCHAR(255) UNIQUE NOT NULL,
    tipo_pago ENUM('Tarjeta', 'Efectivo') NOT NULL,
    validacion_tarjeta VARCHAR(255),
    folio_pago_efectivo VARCHAR(255),
    fecha_compra TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario),
    FOREIGN KEY (id_funcion) REFERENCES Funciones(id_funcion),
    FOREIGN KEY (id_asiento) REFERENCES Asientos(id_asiento)
);

-- Tabla alimentos
CREATE TABLE IF NOT EXISTS ProductosAlimentos (
    id_producto_alimento INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL
);

-- Compra de alimentos
CREATE TABLE IF NOT EXISTS CompraAlimentos (
    id_compra_alimento INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    resumen_compra TEXT,
    precio_total DECIMAL(10, 2) NOT NULL,
    fecha_hora DATETIME NOT NULL,
    folio_alimento VARCHAR(255) UNIQUE NOT NULL,
    tipo_pago ENUM('Tarjeta', 'Efectivo') NOT NULL,
    validacion_tarjeta VARCHAR(255),
    folio_pago_efectivo VARCHAR(255),
    fecha_compra TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario)
);

-- Tabla de detalle para la compra de alimentos
CREATE TABLE IF NOT EXISTS DetalleCompraAlimentos (
    id_detalle_compra INT PRIMARY KEY AUTO_INCREMENT,
    id_compra_alimento INT NOT NULL,
    id_producto_alimento INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (id_compra_alimento) REFERENCES CompraAlimentos(id_compra_alimento),
    FOREIGN KEY (id_producto_alimento) REFERENCES ProductosAlimentos(id_producto_alimento)
);



-- Usuarios
INSERT INTO Usuarios (nombre_usuario, contrasena, email) VALUES
('juanperez', 'password123', 'juan.perez@example.com'),
('mariagomez', 'securepass', 'maria.gomez@example.com'),
('carlossal', 'mypassword', 'carlos.sal@example.com');

-- Cines
INSERT INTO Cines (nombre, direccion) VALUES
('Cinepolis Centro', 'Av. Siempre Viva 123, Centro'),
('Cinemex Plaza', 'Calle Falsa 456, Colonia del Sol'),
('Cine Arte', 'Paseo de la Reforma 789, Juárez');

-- Peliculas
INSERT INTO Peliculas (imagen,titulo,descripcion, duracion_minutos, genero) VALUES
('../imagen.jpeg','Cómo Entrenar A Tu Dragón (Live Action)', 'Una aventura épica donde un joven vikingo se hace amigo de un dragón, adaptando la querida historia animada a acción real. Llena de amistad y vuelos espectaculares.','125', 'Aventura/Fantasía'),
('../imagen1.jpeg','Bailarina','Un intenso spin-off del universo John Wick. Una asesina busca venganza por la muerte de su familia, desatando espectaculares coreografías de combate y acción implacable.','125', 'Acción'),
('../imagen2.jpeg','Misión: Imposible – La Sentencia Final',' Ethan Hunt y su equipo se embarcan en su misión más peligrosa hasta la fecha. Una carrera contra el tiempo con acrobacias asombrosas y giros inesperados.','169', 'Acción/Espionaje'),
('../imagen3.jpeg','Mickey 17', 'Un "prescindible" clon es enviado a colonizar un mundo helado. Si muere, es reemplazado por otro clon con sus recuerdos. Una historia fascinante de identidad y supervivencia.', '137', 'Ciencia Ficción/Drama'),
('../imagen4.jpeg','F1: La Película', 'Una inmersión emocionante en el mundo de la Fórmula 1, siguiendo a un piloto veterano que regresa para competir con una joven promesa. Velocidad, drama y pura adrenalina en la pista.', '120', 'Acción/Drama'),
('../imagen5.jpeg','Exterminio: La evolución', 'La aterradora continuación de la saga de zombis, donde un virus mortal ha evolucionado. La humanidad lucha por sobrevivir en un mundo postapocalíptico lleno de horror y desesperación.', '110', 'Terror'),
('../imagen6.jpeg','Lilo y Stitch', 'El reestreno de un clásico de Disney sobre una niña hawaiana que adopta una peculiar "mascota" extraterrestre. Una historia conmovedora sobre la familia y encontrar tu lugar.', '108', 'Animación/Aventura'),
('../imagen7.jpeg','Capitán América: Un Nuevo Mundo', 'Sam Wilson asume el manto del Capitán América en una nueva y emocionante aventura. Enfrenta amenazas globales y redefine lo que significa ser un héroe en el mundo moderno.', '118', 'Acción/Superhéroes'),
('../imagen8.jpeg','Destino Final: Lazos De Sangre', 'La Muerte regresa con nuevos y macabros planes. Un grupo de personas debe escapar de su destino ineludible, enfrentando trampas mortales en esta escalofriante nueva entrega.', '110', 'Terror'),
('../imagen9.jpeg','Memorias De Un Caracol', 'Una conmovedora historia animada sobre un caracol que busca su propósito en un vasto jardín. Una reflexión sobre la vida, la perseverancia y la belleza de lo pequeño.', '94', 'Animación/Drama');

-- Salas
INSERT INTO Salas (id_cine, numero_sala, capacidad) VALUES
(1, 'Sala 1', 150),
(1, 'Sala 2', 100),
(2, 'Sala A', 200),
(2, 'Sala B', 120),
(3, 'Sala Principal', 80);

-- Asientos
INSERT INTO Asientos (id_sala, fila, numero) VALUES
(1, 'A', '1'), (1, 'A', '2'),
(1, 'B', '5'),
(2, 'C', '10'), (2, 'C', '11'),
(3, 'D', '1'), (3, 'D', '20');

-- Funciones
INSERT INTO Funciones (id_pelicula, id_sala, fecha_hora, precio) VALUES
(1, 1, '2025-06-15 16:00:00', 90.00),
(1, 2, '2025-06-15 19:30:00', 90.00),
(2, 3, '2025-06-16 18:00:00', 95.00),
(3, 1, '2025-06-16 14:00:00', 70.00),
(4, 2, '2025-06-17 22:00:00', 100.00);

-- CompraBoletos
INSERT INTO CompraBoletos (id_usuario, id_funcion, id_asiento, folio_boleto, tipo_pago, validacion_tarjeta, folio_pago_efectivo) VALUES
(1, 1, (SELECT id_asiento FROM Asientos WHERE id_sala = 1 AND fila = 'A' AND numero = '1'), 'BOL-0001', 'Tarjeta', 'TRX789ABC', NULL),
(2, 2, (SELECT id_asiento FROM Asientos WHERE id_sala = 2 AND fila = 'C' AND numero = '10'), 'BOL-0002', 'Efectivo', NULL, 'PAG-EFEC-001'),
(1, 3, (SELECT id_asiento FROM Asientos WHERE id_sala = 3 AND fila = 'D' AND numero = '20'), 'BOL-0003', 'Tarjeta', 'TRXXYZ456', NULL),
(3, 4, (SELECT id_asiento FROM Asientos WHERE id_sala = 1 AND fila = 'B' AND numero = '5'), 'BOL-0004', 'Tarjeta', 'TRXABCDEF', NULL);


-- ProductosAlimentos
INSERT INTO ProductosAlimentos (nombre, descripcion, precio) VALUES
('Palomitas Grandes', 'Palomitas de maíz sabor mantequilla', 80.00),
('Refresco Grande', 'Refresco de cola de 1 litro', 50.00),
('Nachos con Queso', 'Totopos con queso fundido y jalapeños', 100.00),
('Hot Dog', 'Salchicha de pavo con pan y aderezos', 60.00);

-- CompraAlimentos
INSERT INTO CompraAlimentos (id_usuario, resumen_compra, precio_total, fecha_hora, folio_alimento, tipo_pago, validacion_tarjeta, folio_pago_efectivo) VALUES
(1, '1 Palomitas Grandes, 2 Refrescos Grandes', 180.00, '2025-06-15 15:45:00', 'ALM-0001', 'Tarjeta', 'ALMTRX-111', NULL),
(2, '1 Nachos con Queso, 1 Refresco Grande', 150.00, '2025-06-15 19:15:00', 'ALM-0002', 'Efectivo', NULL, 'ALM-EFEC-001'),
(3, '2 Hot Dogs, 2 Refrescos Grandes', 220.00, '2025-06-16 17:30:00', 'ALM-0003', 'Tarjeta', 'ALMTRX-222', NULL);

-- DetalleCompraAlimentos (asumiendo que los ids de CompraAlimentos y ProductosAlimentos existen)
INSERT INTO DetalleCompraAlimentos (id_compra_alimento, id_producto_alimento, cantidad, precio_unitario) VALUES
(1, 1, 1, 80.00),
(1, 2, 2, 50.00),
(2, 3, 1, 100.00),
(2, 2, 1, 50.00),
(3, 4, 2, 60.00),
(3, 2, 2, 50.00);