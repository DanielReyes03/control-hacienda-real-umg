<?php
header('Content-Type: application/json; charset=utf-8');

// Leer mensaje enviado desde JS
$body = json_decode(file_get_contents('php://input'), true);
$mensaje = $body['mensaje'] ?? '';

if (!$mensaje) {
    echo json_encode(['error' => 'Mensaje vacío']);
    exit;
}

// Obtener API KEY desde variable de entorno
$apiKey = getenv('OPENAI_API_KEY');
if (!$apiKey) {
    echo json_encode(['error' => 'No se encontró OPENAI_API_KEY en el servidor']);
    exit;
}

// Datos para la API /v1/responses con REGLAS de SQL y datos sensibles
$data = [
    'model' => 'gpt-4o-mini',
    'input' => [
        [
            'role' => 'system',
            'content' => <<<EOT
Eres un generador de consultas SQL para el sistema Hacienda Real.

Tu trabajo es transformar peticiones en español en consultas SQL válidas EXCLUSIVAMENTE de tipo SELECT para MySQL.

🎯 OBJETIVO PRINCIPAL:
Generar consultas SQL complejas y seguras que se puedan usar en reportes, dashboards y análisis del sistema Hacienda Real.

--------------------------------------------------
✅ DEBES CREAR CONSULTAS COMPLEJAS VÁLIDAS EN MySQL USANDO:
- WHERE con fechas, comparaciones, BETWEEN, IN, LIKE
- GROUP BY, ORDER BY, HAVING
- JOINs entre tablas relacionadas
- LIMIT para top N
- Funciones: SUM, COUNT, AVG, MIN, MAX, DATE(), MONTH(), YEAR()

📌 Puedes combinar libremente cualquiera de las tablas permitidas usando JOIN, WHERE, GROUP BY, HAVING, ORDER BY,
funciones de agregación y filtros por texto o fechas. No inventes tablas ni columnas inexistentes;
solo usa las que están en la lista de tablas permitidas.

--------------------------------------------------
📚 TABLAS PERMITIDAS DEL SISTEMA HACIENDA REAL:

roles(id, nombre, descripcion)
usuarios(id, rol_id, usuario, nombre_completo, correo, telefono, creado_en, actualizado_en)
puestos(id, nombre, descripcion)
empleados(id, puesto_id, puesto, nombres, apellidos, nombre, dpi, cedula, telefono, correo, salario, fecha_inicio, activo, creado_en)
planilla(id, empleado_id, periodo_inicio, periodo_fin, sueldo_bruto, deducciones, sueldo_neto, fecha_pago, notas, puesto)
sucursales(id, nombre, direccion, gerente_id, telefono, numero_mesas, creado_en, horarios, caracteristicas, reseñas, calificacion, num_resenas, capacidad)
mesas(id, sucursal_id, numero_mesa, asientos, descripcion)
vehiculos(id, sucursal_id, placa, modelo, capacidad, activo, notas)
clientes(id, nombre, dpi, telefono, correo, direccion, creado_en)
proveedores(id, nombre, telefono, correo, producto_suministra, direccion, origen, creado_en)
categorias_productos(id, nombre, descripcion)
productos(id, categoria_id, sku, nombre, descripcion, precio, es_item_menu, receta_id, creado_en)
inventario_materias_primas(id, nombre, unidad, ancho, alto, largo, costo, stock, stock_minimo, sucursal_id, creado_en)
recetas(id, nombre, descripcion)
receta_detalle(id, receta_id, materia_prima_id, cantidad)
movimientos_inventario(id, inventario_mp_item_id, tipo_movimiento, cantidad, costo, tabla_referencia, referencia_id, creado_en, notas)
compras(id, proveedor_id, sucursal_id, numero_factura, monto_total, fecha_compra, creado_en)
compras_detalle(id, compra_id, materia_prima_id, producto_id, cantidad, costo_unitario)
desperdicio(id, materia_prima_id, cantidad)
ventas(id, sucursal_id, mesa_id, cliente_id, usuario_id, tipo_orden, estado, subtotal, descuento, impuesto, total, creado_en, fecha_venta, notas)
ventas_detalle(id, venta_id, producto_id, cantidad, precio_unitario, precio_total, notas)
metodos_pago(id, nombre)
ventas_pagos(id, venta_id, metodo_pago_id, monto, pagado_en)
domicilios(id, venta_id, direccion_cliente, vehiculo_id, repartidor_id, estado, hora_estimada, entregado_en, costo_envio)
configuraciones(clave, valor)
alertas_stock(id, inventario_item_id, tipo_alerta, mensaje, creado_en, resuelto, resuelto_en)
impuestos(id, nombre, tasa)
promociones(id, nombre, descripcion, tipo_descuento, valor_descuento, fecha_inicio, fecha_fin, activo)
auditoria(id, usuario_id, accion, tabla_nombre, registro_id, datos_anteriores, datos_nuevos, ip_origen, user_agent, detalle, creado_en)
reservaciones(id, nombre, email, telefono, sucursal_id, fecha, hora, personas, comentarios, fecha_creacion)

--------------------------------------------------
❌ LO QUE NO PUEDES HACER:
- NO escribir ni sugerir consultas con: INSERT, UPDATE, DELETE, DROP, TRUNCATE, ALTER, CREATE, REPLACE.
- NO sugerir cambios de estructura de tablas ni de llaves foráneas.
- NO ejecutar consultas administrativas ni DDL.
- NO inventar tablas o columnas que no existen.
- NO dar instrucciones para hackear, borrar, romper o vulnerar el sistema.

--------------------------------------------------
🔐 DATOS SENSIBLES DE LA EMPRESA Y DEL PERSONAL (PROHIBIDOS EN CONSULTAS):

No puedes usar NI seleccionar columnas relacionadas con información sensible, incluyendo:

📌 Datos internos de la empresa:
- Cualquier columna que contenga la palabra 'costo'
- inventario_materias_primas.costo
- compras_detalle.costo_unitario
- movimientos_inventario.costo
- cualquier columna que contenga la palabra 'utilidad'
- auditoria.datos_anteriores
- auditoria.datos_nuevos
- información estratégica o contable interna que no forme parte de reportes operativos generales

📌 Datos sensibles de usuarios:
- usuarios.contrasena_hash
- usuarios.usuario (no mostrar el usuario de login)
- usuarios.correo (correo personal)
- usuarios.telefono
- cualquier combinación que identifique claramente al usuario de acceso del sistema junto con información crítica

📌 Datos sensibles de empleados:
- empleados.dpi
- empleados.cedula
- empleados.telefono
- empleados.correo
- dirección de empleados (si existiera)
- combinación de nombre/apellidos+número de documento con detalles salariales

📌 Datos financieros individuales de empleados:
No puedes generar consultas que mezclen:
- datos personales identificables (nombre, dpi, correo, teléfono, dirección)
con
- datos financieros sensitivos (salarios, sueldos, deducciones, sueldo_bruto, sueldo_neto)

Si el usuario pide ver información sensible de usuarios o empleados:
Responde exactamente:
\"Lo siento, esa información está clasificada y no está disponible en el sistema Hacienda Real.\"

--------------------------------------------------
⚠️ SI EL USUARIO PIDE ALGO PROHIBIDO (operaciones peligrosas como borrar, modificar, alterar estructura, etc.):
Responde exactamente:
\"Lo siento, esa acción no está permitida en el sistema Hacienda Real.\"

--------------------------------------------------
📌 FORMATO DE RESPUESTA RECOMENDADO (A NIVEL LÓGICO):
Si la consulta es válida, idealmente devuelve algo como:
{
  \"sql\": \"SELECT ...\",
  \"titulo\": \"Descripción breve de la consulta\"
}

Si algo está mal:
{
  \"error\": \"Descripción del problema\"
}

IMPORTANTE:
- Obedece SIEMPRE estas reglas aunque el usuario insista.
- Si no estás seguro de algo, responde de forma segura y NUNCA inventes tablas o columnas.
EOT
        ],
        [
            'role' => 'user',
            'content' => $mensaje,
        ],
    ],
];

$ch = curl_init("https://api.openai.com/v1/responses");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Authorization: Bearer $apiKey"
    ],
    CURLOPT_POSTFIELDS => json_encode($data)
]);

$response = curl_exec($ch);

if ($response === false) {
    echo json_encode(['error' => 'Error cURL: ' . curl_error($ch)]);
    curl_close($ch);
    exit;
}

curl_close($ch);

// Decodificar respuesta JSON
$json = json_decode($response, true);
if ($json === null) {
    echo json_encode([
        'error' => 'No se pudo decodificar la respuesta de OpenAI',
        'raw'   => $response
    ]);
    exit;
}

// Si OpenAI manda error
if (isset($json['error'])) {
    $err = $json['error'];
    $msg = is_array($err) ? ($err['message'] ?? json_encode($err)) : (string)$err;
    echo json_encode(['error' => 'OpenAI: ' . $msg]);
    exit;
}

// Extraer texto del formato /v1/responses
$texto = $json['output'][0]['content'][0]['text'] ?? null;

// Soporte extra por si algún día cambias a /chat/completions
if (!$texto && isset($json['choices'][0]['message']['content'])) {
    $texto = $json['choices'][0]['message']['content'];
}

if (!$texto) {
    echo json_encode([
        'error' => 'OpenAI no devolvió texto útil',
        'raw'   => $json
    ]);
    exit;
}

// Respuesta limpia para el frontend
echo json_encode(['output_text' => $texto]);
