<?php
error_reporting(E_ALL);
include_once("../../../db/conexion.php");
header('Content-Type: application/json');

$db = conectar();

// Primero obtenemos las recetas
$sqlRecetas = "
    SELECT 
        r.id,
        r.nombre,
        r.descripcion
    FROM recetas r
    ORDER BY r.nombre ASC
";

$stmtRec = $db->prepare($sqlRecetas);

if (!$stmtRec) {
    echo json_encode([]);
    exit;
}

$stmtRec->execute();
$resultRec = $stmtRec->get_result();

$recetas = [];

if ($resultRec) {
    while($r = $resultRec->fetch_assoc()) {
        // Buscar el detalle de cada receta
        $sqlDetalle = "
            SELECT 
                d.id,
                d.receta_id,
                d.materia_prima_id,
                mp.nombre AS materia_prima,
                d.cantidad
            FROM receta_detalle d
            INNER JOIN inventario_materias_primas mp ON d.materia_prima_id = mp.id
            WHERE d.receta_id = ?
        ";

        $stmtDet = $db->prepare($sqlDetalle);
        $stmtDet->bind_param("i", $r["id"]);
        $stmtDet->execute();
        $resultDet = $stmtDet->get_result();

        $detalle = [];
        while($d = $resultDet->fetch_assoc()) {
            $detalle[] = $d;
        }

        $r["detalle"] = $detalle;
        $recetas[] = $r;

        $stmtDet->close();
    }
}

echo json_encode($recetas);

$stmtRec->close();
$db->close();
?>
