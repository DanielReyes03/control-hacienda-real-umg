
CREATE TABLE `roles` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nombre` varchar(50) UNIQUE NOT NULL,
  `descripcion` varchar(255)
);

CREATE TABLE `usuarios` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `rol_id` int NOT NULL,
  `usuario` varchar(100) UNIQUE NOT NULL,
  `contrasena_hash` varchar(255) NOT NULL,
  `nombre_completo` varchar(255),
  `correo` varchar(150),
  `telefono` varchar(50),
  `creado_en` datetime,
  `actualizado_en` datetime
);

CREATE TABLE `puestos` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255)
);

CREATE TABLE empleados (
  id INT PRIMARY KEY AUTO_INCREMENT,
  puesto_id INT,
  puesto VARCHAR(100),
  nombres VARCHAR(100) NOT NULL,
  apellidos VARCHAR(100) NOT NULL,
  nombre VARCHAR(200),           
  dpi VARCHAR(25),
  cedula VARCHAR(25),
  telefono VARCHAR(50),
  correo VARCHAR(150),
  salario DECIMAL(12,2) DEFAULT 0,
  fecha_inicio DATE,
  notas TEXT,
  activo BOOLEAN DEFAULT TRUE,
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE `planilla` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `empleado_id` int NOT NULL,
  `periodo_inicio` date NOT NULL,
  `periodo_fin` date NOT NULL,
  `sueldo_bruto` decimal(12,2) NOT NULL,
  `deducciones` decimal(12,2) DEFAULT 0,
  `sueldo_neto` decimal(12,2) NOT NULL,
  `fecha_pago` date,
  `notas` text,
  `puesto` VARCHAR(100)
);

CREATE TABLE `sucursales` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `direccion` varchar(255),
  `gerente_id` int,
  `telefono` varchar(50),
  `numero_mesas` int DEFAULT 0,
  `creado_en` datetime
);
ALTER TABLE `sucursales` 
ADD COLUMN `horarios` VARCHAR(255),
ADD COLUMN `caracteristicas` TEXT,
ADD COLUMN `calificacion` DECIMAL(3,2),
ADD COLUMN `num_resenas` INT DEFAULT 0,
ADD COLUMN reseñas TEXT AFTER caracteristicas,
ADD COLUMN `capacidad` INT DEFAULT 0;



CREATE TABLE `mesas` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `sucursal_id` int NOT NULL,
  `numero_mesa` varchar(50) NOT NULL,
  `asientos` int DEFAULT 4,
  `descripcion` varchar(255)
);

CREATE TABLE `vehiculos` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `sucursal_id` int,
  `placa` varchar(20) UNIQUE,
  `modelo` varchar(100),
  `capacidad` varchar(100),
  `activo` boolean DEFAULT true,
  `notas` text
);

CREATE TABLE `clientes` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nombre` varchar(200) NOT NULL,
  `dpi` varchar(30),
  `telefono` varchar(50),
  `correo` varchar(150),
  `direccion` varchar(255),
  `creado_en` datetime
);

CREATE TABLE `proveedores` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nombre` varchar(200) NOT NULL,
  `telefono` varchar(50),
  `correo` varchar(150),
  `producto_suministra` varchar(255),
  `direccion` varchar(255),
  `origen` varchar(50),
  `creado_en` datetime
);

CREATE TABLE `categorias_productos` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255)
);

CREATE TABLE `productos` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `categoria_id` int,
  `sku` varchar(100) UNIQUE,
  `nombre` varchar(200) NOT NULL,
  `descripcion` text,
  `precio` decimal(12,2) DEFAULT 0,
  `es_item_menu` boolean DEFAULT true,
  `receta_id` int NOT NULL,
  `creado_en` datetime
);

CREATE TABLE `inventario_materias_primas` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nombre` varchar(200) NOT NULL,
  `unidad` varchar(50),
  `ancho` decimal(12,2) DEFAULT 0,
  `alto` decimal(12,2) DEFAULT 0,
  `largo` decimal(12,2) DEFAULT 0,
  `costo` decimal(12,2) DEFAULT 0,
  `stock` decimal(12,4) DEFAULT 0,
  `stock_minimo` decimal(12,4) DEFAULT 0,
  `sucursal_id` int NOT NULL,
  `creado_en` datetime
);

CREATE TABLE `recetas` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text
);

CREATE TABLE `receta_detalle` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `receta_id` int NOT NULL,
  `materia_prima_id` int NOT NULL,
  `cantidad` decimal(12,4) NOT NULL
);

CREATE TABLE `movimientos_inventario` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `inventario_mp_item_id` int NOT NULL,
  `tipo_movimiento` varchar(20) COMMENT 'enum: ''entrada'',''salida'',''ajuste'',''compra'',''venta''',
  `cantidad` decimal(12,4) NOT NULL,
  `costo` decimal(12,2) DEFAULT 0,
  `tabla_referencia` varchar(100),
  `referencia_id` int,
  `creado_en` datetime,
  `notas` text
);

CREATE TABLE `compras` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `proveedor_id` int,
  `sucursal_id` int,
  `numero_factura` varchar(100),
  `monto_total` decimal(12,2),
  `fecha_compra` date,
  `creado_en` datetime
);

CREATE TABLE `compras_detalle` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `compra_id` int NOT NULL,
  `materia_prima_id` int,
  `producto_id` int,
  `cantidad` decimal(12,4) NOT NULL,
  `costo_unitario` decimal(12,2) DEFAULT 0
);

CREATE TABLE `desperdicio` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `materia_prima_id` int,
  `cantidad` decimal(12,4) NOT NULL
);

CREATE TABLE `ventas` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `sucursal_id` int,
  `mesa_id` int,
  `cliente_id` int,
  `usuario_id` int,
  `tipo_orden` varchar(20) DEFAULT 'en_sitio' COMMENT 'enum: ''en_sitio'',''para_llevar'',''domicilio''',
  `estado` varchar(20) DEFAULT 'cerrada' COMMENT 'enum: ''abierta'',''cerrada'',''cancelada''',
  `subtotal` decimal(12,2) DEFAULT 0,
  `descuento` decimal(12,2) DEFAULT 0,
  `impuesto` decimal(12,2) DEFAULT 0,
  `total` decimal(12,2) DEFAULT 0,
  `creado_en` datetime,
  `fecha_venta` date,
  `notas` text
);

CREATE TABLE `ventas_detalle` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `venta_id` int NOT NULL,
  `producto_id` int NOT NULL,
  `cantidad` decimal(12,4) NOT NULL,
  `precio_unitario` decimal(12,2) NOT NULL,
  `precio_total` decimal(12,2) NOT NULL,
  `notas` text
);

CREATE TABLE `metodos_pago` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL
);

CREATE TABLE `ventas_pagos` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `venta_id` int NOT NULL,
  `metodo_pago_id` int,
  `monto` decimal(12,2) NOT NULL,
  `pagado_en` datetime
);

CREATE TABLE `domicilios` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `venta_id` int NOT NULL,
  `direccion_cliente` varchar(255),
  `vehiculo_id` int,
  `repartidor_id` int,
  `estado` varchar(20) COMMENT 'enum: ''pendiente'',''en_camino'',''entregado'',''cancelado''',
  `hora_estimada` datetime,
  `entregado_en` datetime,
  `costo_envio` decimal(12,2) DEFAULT 0
);

CREATE TABLE `configuraciones` (
  `clave` varchar(150) PRIMARY KEY,
  `valor` text
);

CREATE TABLE `alertas_stock` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `inventario_item_id` int NOT NULL,
  `tipo_alerta` varchar(20) COMMENT 'enum: ''stock_bajo'',''sin_ventas''',
  `mensaje` varchar(255),
  `creado_en` datetime,
  `resuelto` boolean DEFAULT false,
  `resuelto_en` datetime
);

CREATE TABLE `impuestos` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nombre` varchar(100),
  `tasa` decimal(5,2)
);

CREATE TABLE `promociones` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nombre` varchar(150),
  `descripcion` text,
  `tipo_descuento` varchar(20) COMMENT 'enum: ''porcentaje'',''fijo''',
  `valor_descuento` decimal(12,2),
  `fecha_inicio` date,
  `fecha_fin` date,
  `activo` boolean DEFAULT true
);

CREATE TABLE `auditoria` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `usuario_id` int,
  `accion` varchar(50) COMMENT 'enum: ''INSERT'',''UPDATE'',''DELETE'',''LOGIN'',''LOGOUT'',''OTRO''',
  `tabla_nombre` varchar(100) NOT NULL,
  `registro_id` int COMMENT 'ID del registro afectado',
  `datos_anteriores` json COMMENT 'Valores antes del cambio',
  `datos_nuevos` json COMMENT 'Valores después del cambio',
  `ip_origen` varchar(50) COMMENT 'IP desde donde se ejecutó la acción',
  `user_agent` varchar(255) COMMENT 'Dispositivo/Navegador usado',
  `detalle` text,
  `creado_en` datetime DEFAULT CURRENT_TIMESTAMP
);



CREATE TABLE IF NOT EXISTS reservaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    telefono VARCHAR(50) NOT NULL,
    sucursal_id INT NOT NULL,  -- Referencia al ID de la tabla 'sucursales'
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    personas INT NOT NULL DEFAULT 1,
    comentarios TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sucursal_id) REFERENCES sucursales(id)
);


ALTER TABLE `usuarios` ADD CONSTRAINT `fk_usuarios_rol_id` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `empleados` ADD CONSTRAINT `fk_empleados_puesto_id` FOREIGN KEY (`puesto_id`) REFERENCES `puestos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `planilla` ADD CONSTRAINT `fk_planilla_empleado_id` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `sucursales` ADD CONSTRAINT `fk_sucursales_gerente_id` FOREIGN KEY (`gerente_id`) REFERENCES `empleados` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `mesas` ADD CONSTRAINT `fk_mesas_sucursal_id` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `vehiculos` ADD CONSTRAINT `fk_vehiculos_sucursal_id` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `productos` ADD CONSTRAINT `fk_productos_categoria_id` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_productos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `productos` ADD CONSTRAINT `fk_productos_receta_id` FOREIGN KEY (`receta_id`) REFERENCES `recetas` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `inventario_materias_primas` ADD CONSTRAINT `fk_inventario_sucursal_id` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `receta_detalle` ADD CONSTRAINT `fk_receta_detalle_receta_id` FOREIGN KEY (`receta_id`) REFERENCES `recetas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `receta_detalle` ADD CONSTRAINT `fk_receta_detalle_materia_prima_id` FOREIGN KEY (`materia_prima_id`) REFERENCES `inventario_materias_primas` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `movimientos_inventario` ADD CONSTRAINT `fk_movimientos_inventario_item_id` FOREIGN KEY (`inventario_mp_item_id`) REFERENCES `inventario_materias_primas` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `compras` ADD CONSTRAINT `fk_compras_proveedor_id` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `compras` ADD CONSTRAINT `fk_compras_sucursal_id` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `compras_detalle` ADD CONSTRAINT `fk_compras_detalle_compra_id` FOREIGN KEY (`compra_id`) REFERENCES `compras` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `compras_detalle` ADD CONSTRAINT `fk_compras_detalle_materia_prima_id` FOREIGN KEY (`materia_prima_id`) REFERENCES `inventario_materias_primas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `compras_detalle` ADD CONSTRAINT `fk_compras_detalle_producto_id` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `desperdicio` ADD CONSTRAINT `fk_desperdicio_materia_prima_id` FOREIGN KEY (`materia_prima_id`) REFERENCES `inventario_materias_primas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `ventas` ADD CONSTRAINT `fk_ventas_sucursal_id` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `ventas` ADD CONSTRAINT `fk_ventas_mesa_id` FOREIGN KEY (`mesa_id`) REFERENCES `mesas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `ventas` ADD CONSTRAINT `fk_ventas_cliente_id` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `ventas` ADD CONSTRAINT `fk_ventas_usuario_id` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `ventas_detalle` ADD CONSTRAINT `fk_ventas_detalle_venta_id` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `ventas_detalle` ADD CONSTRAINT `fk_ventas_detalle_producto_id` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `ventas_pagos` ADD CONSTRAINT `fk_ventas_pagos_venta_id` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `ventas_pagos` ADD CONSTRAINT `fk_ventas_pagos_metodo_id` FOREIGN KEY (`metodo_pago_id`) REFERENCES `metodos_pago` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `domicilios` ADD CONSTRAINT `fk_domicilios_venta_id` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `domicilios` ADD CONSTRAINT `fk_domicilios_vehiculo_id` FOREIGN KEY (`vehiculo_id`) REFERENCES `vehiculos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `domicilios` ADD CONSTRAINT `fk_domicilios_repartidor_id` FOREIGN KEY (`repartidor_id`) REFERENCES `empleados` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `alertas_stock` ADD CONSTRAINT `fk_alertas_stock_item_id` FOREIGN KEY (`inventario_item_id`) REFERENCES `inventario_materias_primas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `auditoria` ADD CONSTRAINT `fk_auditoria_usuario_id` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;


INSERT INTO usuarios (rol_id, usuario, contrasena_hash, nombre_completo, correo, telefono, creado_en)
VALUES (
  (SELECT id FROM roles WHERE nombre = 'Administrador'),
  'admin_root',
  '$2y$10$7kH5FzQso0ZWV21fYiykj.A68h7iA9kIKrKi2B.KOYAmClUVcex02',  -- Contraseña: 123456
  'Administrador General del Sistema',
  'admin@lahaciendareal.com',
  '000-0000',
  NOW()
);



INSERT INTO roles (nombre, descripcion) VALUES
('Administrador', 'Tiene acceso completo a todas las secciones del sistema'),
('Gerente', 'Acceso a reportes, planilla y ventas de su sucursal'),
('Empleado', 'Acceso limitado a módulos de ventas y clientes'),
('Repartidor', 'Acceso solo a domicilios y entregas'),
('Cliente','Acceso solo al servicio a domicilio');