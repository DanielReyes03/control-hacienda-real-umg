<?php
include("../../db/conexion.php");
$conn = conectar();

// Cambiar estado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['domicilio_id'];
    $estado = $_POST['estado'];
    $conn->query("UPDATE domicilios SET estado='$estado', entregado_en = IF('$estado'='entregado', NOW(), NULL) WHERE id=$id");
}

$domicilios = $conn->query("
SELECT d.id, c.nombre AS cliente, d.direccion_cliente, d.estado, d.hora_estimada, d.entregado_en
FROM domicilios d
JOIN ventas v ON v.id = d.venta_id
JOIN clientes c ON v.cliente_id = c.id
ORDER BY d.id DESC
");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Domicilios</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <h1>🚚 Gestión de Domicilios</h1>
  <table class="table table-bordered">
    <thead><tr><th>#</th><th>Cliente</th><th>Dirección</th><th>Estado</th><th>Hora Estimada</th><th>Entregado En</th><th>Acción</th></tr></thead>
    <tbody>
      <?php while ($d = $domicilios->fetch_assoc()): ?>
        <tr>
          <td><?= $d['id'] ?></td>
          <td><?= htmlspecialchars($d['cliente']) ?></td>
          <td><?= htmlspecialchars($d['direccion_cliente']) ?></td>
          <td><span class="badge bg-<?= $d['estado']=='entregado'?'success':($d['estado']=='en_camino'?'warning':'secondary') ?>">
            <?= ucfirst($d['estado']) ?>
          </span></td>
          <td><?= $d['hora_estimada'] ?></td>
          <td><?= $d['entregado_en'] ?: '-' ?></td>
          <td>
            <form method="POST" style="display:flex;gap:5px;">
              <input type="hidden" name="domicilio_id" value="<?= $d['id'] ?>">
              <select name="estado" class="form-select form-select-sm">
                <option value="pendiente" <?= $d['estado']=='pendiente'?'selected':'' ?>>Pendiente</option>
                <option value="en_camino" <?= $d['estado']=='en_camino'?'selected':'' ?>>En camino</option>
                <option value="entregado" <?= $d['estado']=='entregado'?'selected':'' ?>>Entregado</option>
              </select>
              <button class="btn btn-sm btn-primary">Actualizar</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
</body>
</html>
<?php desconectar($conn); ?>
