<?php
include("../../db/conexion.php");
require_once "../../login/check_adminEmple.php";
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
  <title>Gestión de Domicilios - Hacienda Real</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      background-color: #f6f8fa;
      font-family: 'Poppins', sans-serif;
    }
    .container {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      padding: 30px;
      margin-top: 60px;
    }
    h1 {
      text-align: center;
      font-weight: 700;
      color: #222;
      margin-bottom: 25px;
    }
    table th {
      background-color: #343a40;
      color: white;
      text-align: center;
    }
    table td {
      text-align: center;
      vertical-align: middle;
    }
    .btn-primary {
      background-color: #e63946;
      border: none;
    }
    .btn-primary:hover {
      background-color: #d62828;
    }
    .btn-secondary {
      background-color: #6c757d;
    }
    .footer-btn {
      display: flex;
      justify-content: center;
      margin-top: 30px;
    }
  </style>
</head>
<body>

  <div class="container">
    <h1>🚚 Gestión de Domicilios</h1>
    <p class="text-muted text-center mb-4">Controla el estado de los pedidos a domicilio</p>

    <table class="table table-hover table-bordered align-middle">
      <thead>
        <tr>
          <th>#</th>
          <th>Cliente</th>
          <th>Dirección</th>
          <th>Estado</th>
          <th>Hora Estimada</th>
          <th>Entregado En</th>
          <th>Acción</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($d = $domicilios->fetch_assoc()): ?>
        <tr>
          <td><?= $d['id'] ?></td>
          <td><?= htmlspecialchars($d['cliente']) ?></td>
          <td><?= htmlspecialchars($d['direccion_cliente']) ?></td>
          <td>
            <span class="badge bg-<?= $d['estado']=='entregado'?'success':($d['estado']=='en_camino'?'warning':'secondary') ?>">
              <?= ucfirst($d['estado']) ?>
            </span>
          </td>
          <td><?= $d['hora_estimada'] ?></td>
          <td><?= $d['entregado_en'] ?: '-' ?></td>
          <td>
            <form method="POST" class="estado-form d-flex justify-content-center align-items-center gap-2">
              <input type="hidden" name="domicilio_id" value="<?= $d['id'] ?>">
              <select name="estado" class="form-select form-select-sm">
                <option value="pendiente" <?= $d['estado']=='pendiente'?'selected':'' ?>>Pendiente</option>
                <option value="en_camino" <?= $d['estado']=='en_camino'?'selected':'' ?>>En camino</option>
                <option value="entregado" <?= $d['estado']=='entregado'?'selected':'' ?>>Entregado</option>
              </select>
              <button class="btn btn-sm btn-primary actualizar-btn" type="submit">Actualizar</button>
            </form>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

    <div class="footer-btn">
      <a href="index.php" class="btn btn-secondary btn-lg">⬅ Volver al Panel</a>
    </div>
  </div>

  <script>
    document.querySelectorAll('.estado-form').forEach(form => {
      form.addEventListener('submit', function(e) {
        e.preventDefault();

        const estado = this.querySelector('select[name="estado"]').value;

        Swal.fire({
          title: '¿Actualizar estado?',
          text: `El domicilio se marcará como "${estado}".`,
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#28a745',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Sí, actualizar',
          cancelButtonText: 'Cancelar'
        }).then(result => {
          if (result.isConfirmed) {
            this.submit();
          }
        });
      });
    });

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    Swal.fire({
      icon: 'success',
      title: 'Estado actualizado correctamente',
      showConfirmButton: false,
      timer: 1500
    });
    <?php endif; ?>
  </script>

</body>
</html>
<?php desconectar($conn); ?>
