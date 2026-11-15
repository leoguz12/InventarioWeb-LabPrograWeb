USE inventario_db;

-- Metemos los dos roles del sistema (1 = Admin, 2 = Cliente)
INSERT INTO Roles (id_rol, nombre_rol) VALUES 
(1, 'Administrador'),
(2, 'Cliente');

-- Configuracion inicial de alerta de stock bajo (por defecto 10)
INSERT INTO Configuracion_Sistema (id_config, limite_stock_critico) VALUES 
(1, 10);

-- Usuario admin de prueba (correo: admin@inventario.com / pass: admin123)
-- Y un usuario cliente de prueba (correo: cliente@inventario.com / pass: cliente123)
INSERT INTO Usuarios (nombre, correo, password, id_rol) VALUES 
('Edgar Guzman', 'admin@inventario.com', 'admin123', 1),
('Usuario Mostrador', 'cliente@inventario.com', 'cliente123', 2);

-- Categorias de ejemplo para que no este vacio al iniciar
INSERT INTO Categorias (nombre) VALUES 
('Papelería'),
('Electrónica');

-- Productos de ejemplo
INSERT INTO Productos (nombre, precio, stock, id_categoria) VALUES 
('Lápiz Adhesivo', 15.00, 50, 1),
('Cuaderno Profesional', 45.00, 120, 1),
('Mouse USB', 120.00, 8, 2);