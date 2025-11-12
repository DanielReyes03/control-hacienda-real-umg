<?php
include("../../db/conexion.php");

$conn = conectar();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cerrar_id'])) {
  $id = intval($_POST['cerrar_id']);
  $conn->query("UPDATE ventas SET estado='cerrada' WHERE id=$id");
}

$ventas = $conn->query("
SELECT v.id, c.nombre AS cliente, v.total, v.estado, v.fecha_venta
FROM ventas v
LEFT JOIN clientes c ON v.cliente_id = c.id
ORDER BY 
  CASE WHEN v.estado = 'abierta' THEN 0 ELSE 1 END,
  v.creado_en DESC
");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel Empleado</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <h1>👨‍🍳 Panel del Empleado</h1>
  <table class="table table-striped mt-4">
    <thead><tr><th>#</th><th>Cliente</th><th>Total</th><th>Estado</th><th>Fecha</th><th>Acción</th></tr></thead>
    <tbody>
      <?php while ($v = $ventas->fetch_assoc()): ?>
      <tr>
        <td><?= $v['id'] ?></td>
        <td><?= htmlspecialchars($v['cliente']) ?></td>
        <td>Q<?= number_format($v['total'], 2) ?></td>
        <td><?= ucfirst($v['estado']) ?></td>
        <td><?= $v['fecha_venta'] ?></td>
        <td>
          <?php if ($v['estado'] == 'abierta'): ?>
          <form method="POST">
            <input type="hidden" name="cerrar_id" value="<?= $v['id'] ?>">
            <button class="btn btn-sm btn-success">Cerrar</button>
          </form>
          <?php else: ?>
          <span class="text-muted">Cerrada</span>
          <?php endif; ?>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
  <a href="domicilios.php" class="btn btn-primary mt-2">Ver Domicilios 🚚</a>
</div>
</body>
</html>
<?php desconectar($conn); ?>
