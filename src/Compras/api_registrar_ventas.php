<?php
header('Content-Type: application/json');

// ✅ Importar la conexión
require_once "../db/conexion.php";

try {
    // Crear conexión
    $conexion = conectar();

    // Leer el JSON enviado desde el frontend
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);

    if (!$data) {
        throw new Exception("Datos JSON inválidos o vacíos");
    }

    $cliente = $data["cliente"] ?? "Cliente anónimo";
    $direccion = $data["direccion"] ?? "-";
    $entrega = $data["entrega"] ?? "en_sitio";
    $pago = $data["pago"] ?? "efectivo";
    $items = $data["items"] ?? [];
    $total = floatval($data["total"] ?? 0);

    if (empty($items)) {
        throw new Exception("No se recibieron productos en la venta");
    }

    // ✅ Insertar venta principal
    $sqlVenta = "INSERT INTO ventas (sucursal_id, mesa_id, cliente_id, usuario_id, tipo_orden, estado, subtotal, descuento, impuesto, total, creado_en, fecha_venta, notas)
                 VALUES (1, NULL, NULL, 1, ?, 'cerrada', ?, 0, 0, ?, NOW(), CURDATE(), ?)";
    $stmt = $conexion->prepare($sqlVenta);
    if (!$stmt) {
        throw new Exception("Error preparando la consulta de venta: " . $conexion->error);
    }

    $notas = "Pago: $pago - Dirección: $direccion - Cliente: $cliente";
    $stmt->bind_param("sdds", $entrega, $total, $total, $notas);
    $stmt->execute();

    $ventaId = $conexion->insert_id;

    // ✅ Insertar detalle de cada producto
    $sqlDetalle = "INSERT INTO ventas_detalle (venta_id, producto_id, cantidad, precio_unitario, precio_total)
                   VALUES (?, ?, ?, ?, ?)";
    $stmtDetalle = $conexion->prepare($sqlDetalle);
    if (!$stmtDetalle) {
        throw new Exception("Error preparando el detalle: " . $conexion->error);
    }

    foreach ($items as $item) {
        $productoId = isset($item["id"]) ? intval($item["id"]) : 0;
        $cantidad = floatval($item["quantity"]);
        $precio = floatval($item["price"]);
        $precioTotal = $cantidad * $precio;

        $stmtDetalle->bind_param("iiddd", $ventaId, $productoId, $cantidad, $precio, $precioTotal);
        $stmtDetalle->execute();
    }

    // ✅ Cerrar conexión
    desconectar($conexion);

    echo json_encode([
        "success" => true,
        "message" => "Venta registrada correctamente",
        "id_venta" => $ventaId
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>
