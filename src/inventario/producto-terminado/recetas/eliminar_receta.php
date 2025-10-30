<?php
error_reporting(E_ALL);
include_once("../../../db/conexion.php");

if (!isset($_GET["id"])) {
    echo "Faltan parámetros";
    exit;
}

$id = intval($_GET["id"]);
if ($id <= 0) {
    echo "ID inválido";
    exit;
}

$db = conectar();
$db->begin_transaction();

try {
    // Verificar si la receta está asociada a algún producto
    $check = $db->prepare("SELECT id FROM productos WHERE receta_id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        echo "No se puede eliminar: hay productos que usan esta receta";
        $check->close();
        $db->close();
        exit;
    }
    $check->close();

    $db->query("DELETE FROM receta_detalle WHERE receta_id = " . $id);

    $stmt = $db->prepare("DELETE FROM recetas WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    $db->commit();
    echo "Ok";
} catch (Exception $e) {
    $db->rollback();
    echo "Error al eliminar: " . $e->getMessage();
}

$db->close();
?>
