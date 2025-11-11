<?php
header("Content-Type: application/json");
require_once "../../conexion.php";

$conn = conectar();

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["error" => "No data"]);
    exit;
}

$cliente      = $data["cliente"];
$direccion    = $data["direccion"];
$entrega      = $data["entrega"];   // llevar / consumir / domicilio
$pago         = $data["pago"];      // efectivo / tarjeta
$total        = $data["total"];
$items        = $data["items"];     // productos

// Convertir tipo de entrega al tipo usado en la tabla ventas
$tipo_orden = ($entrega === "llevar") ? "para_llevar" :
              (($entrega === "domicilio") ? "domicilio" : "en_sitio");

$conn->begin_transaction();

try {

    // ========================================
    // 1) INSERTAR VENTA
    // ========================================
    $stmt = $conn->prepare("
        INSERT INTO ventas (tipo_orden, subtotal, total, creado_en, fecha_venta)
        VALUES (?, ?, ?, NOW(), CURDATE())
    ");
    $stmt->bind_param("sdd", $tipo_orden, $total, $total);
    $stmt->execute();
    $venta_id = $stmt->insert_id;


    // ========================================
    // 2) INSERTAR DETALLE DE VENTA
    // ========================================
    $stmtDetalle = $conn->prepare("
        INSERT INTO ventas_detalle (venta_id, producto_id, cantidad, precio_unitario, precio_total)
        VALUES (?, ?, ?, ?, ?)
    ");

    foreach ($items as $item) {

        // Buscar producto_id por nombre
        $stmt2 = $conn->prepare("SELECT id FROM productos WHERE nombre = ? LIMIT 1");
        $stmt2->bind_param("s", $item["name"]);
        $stmt2->execute();
        $stmt2->bind_result($producto_id);
        $stmt2->fetch();
        $stmt2->close();

        if (!$producto_id) {
            throw new Exception("Producto NO existe en DB: ".$item["name"]);
        }

        $cantidad     = $item["quantity"];
        $precio       = $item["price"];
        $precio_total = $cantidad * $precio;

        $stmtDetalle->bind_param("iiidd", $venta_id, $producto_id, $cantidad, $precio, $precio_total);
        $stmtDetalle->execute();
    }

    $conn->commit();

    echo json_encode([
        "success" => true,
        "venta_id" => $venta_id
    ]);

} catch (Exception $e) {

    $conn->rollback();
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}

desconectar($conn);

?>
