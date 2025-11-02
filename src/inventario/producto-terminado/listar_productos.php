<?php
error_reporting(E_ALL);
include_once("../../db/conexion.php");
header('Content-Type: application/json');

$db = conectar();

$sql = "
    SELECT 
        p.id,
        p.sku,
        p.nombre,
        p.descripcion,
        p.precio,
        p.es_item_menu,
        p.creado_en,
        c.id AS categoria_id,
        c.nombre AS categoria,
        r.id AS receta_id,
        r.nombre AS receta
    FROM productos p
    LEFT JOIN categorias_productos c ON p.categoria_id = c.id
    LEFT JOIN recetas r ON p.receta_id = r.id
    ORDER BY p.nombre ASC
";

$stmt = $db->prepare($sql);

if (!$stmt) {
    echo json_encode([]);
    exit;
}

$stmt->execute();
$result = $stmt->get_result();

$productos = [];

if ($result) {
    while($row = $result->fetch_assoc()){
        // Convertir booleano a verdadero/falso para JS
        $row["es_item_menu"] = (bool)$row["es_item_menu"];
        $productos[] = $row;
    }
}

echo json_encode($productos);

$stmt->close();
$db->close();
?>
