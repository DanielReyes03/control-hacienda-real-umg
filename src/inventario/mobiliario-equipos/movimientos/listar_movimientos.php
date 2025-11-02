<?php
error_reporting(E_ALL);
header('Content-Type: application/json');
include_once("../../../db/conexion.php");

$db = conectar();

$sql = "
SELECT 
    ma.id,
    a.codigo_interno,
    a.nombre AS activo,
    ma.tipo_movimiento,
    s_origen.nombre AS origen,
    s_destino.nombre AS destino,
    ma.fecha_movimiento,
    ma.observaciones
FROM movimientos_activos ma
INNER JOIN activos a ON ma.activo_id = a.id
LEFT JOIN sucursales s_origen ON ma.origen_id = s_origen.id
LEFT JOIN sucursales s_destino ON ma.destino_id = s_destino.id
ORDER BY ma.fecha_movimiento DESC
";

$stmt = $db->prepare($sql);
if (!$stmt) {
    echo json_encode(["error" => "Error preparando consulta: " . $db->error]);
    exit;
}

$stmt->execute();
$result = $stmt->get_result();

$movimientos = [];
while ($row = $result->fetch_assoc()) {
    $movimientos[] = [
        "id" => $row["id"],
        "activo" => $row["codigo_interno"] . " - " . $row["activo"],
        "tipo_movimiento" => ucfirst($row["tipo_movimiento"]),
        "origen" => $row["origen"] ?? "—",
        "destino" => $row["destino"] ?? "—",
        "fecha_movimiento" => date("Y-m-d H:i", strtotime($row["fecha_movimiento"])),
        "observaciones" => $row["observaciones"] ?? ""
    ];
}

echo json_encode($movimientos);

$stmt->close();
$db->close();
?>
