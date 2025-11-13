<?php
require_once "../../login/check_adminEmple.php";
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
  v.fecha_venta DESC
");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel Empleado - Hacienda Real</title>
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
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      padding: 30px;
      margin-top: 60px;
    }
    h1 {
      color: #333;
      font-weight: 700;
      text-align: center;
      margin-bottom: 20px;
    }
    table th {
      background-color: #343a40;
      color: #fff;
      text-align: center;
    }
    table td {
      text-align: center;
      vertical-align: middle;
    }
    .btn-success {
      background-color: #28a745;
      border: none;
      transition: background-color 0.3s ease;
    }
    .btn-success:hover {
      background-color: #218838;
    }
    .btn-primary {
      background-color: #e63946;
      border: none;
    }
    .btn-primary:hover {
      background-color: #d62828;
    }
    .footer-btn {
      display: flex;
      justify-content: center;
      gap: 15px;
      margin-top: 30px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>👨‍🍳 Panel del Empleado</h1>
    <p class="text-muted text-center mb-4">Administra y cierra las ventas activas</p>

    <table class="table table-hover table-bordered align-middle">
      <thead class="table-dark">
        <tr>
          <th>#</th>
          <th>Cliente</th>
          <th>Total</th>
          <th>Estado</th>
          <th>Fecha</th>
          <th>Acción</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($v = $ventas->fetch_assoc()): ?>
        <tr>
          <td><?= $v['id'] ?></td>
          <td><?= htmlspecialchars($v['cliente']) ?></td>
          <td><strong>Q<?= number_format($v['total'], 2) ?></strong></td>
          <td>
            <?php if ($v['estado'] == 'abierta'): ?>
              <span class="badge bg-success">Abierta</span>
            <?php else: ?>
              <span class="badge bg-secondary">Cerrada</span>
            <?php endif; ?>
          </td>
          <td><?= $v['fecha_venta'] ?></td>
          <td>
            <?php if ($v['estado'] == 'abierta'): ?>
              <button 
                class="btn btn-sm btn-success cerrar-btn"
                data-id="<?= $v['id'] ?>">
                Cerrar venta
              </button>
            <?php else: ?>
              <span class="text-muted">—</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

    <div class="footer-btn">
      <a href="domicilios.php" class="btn btn-primary btn-lg">🚚 Ver Domicilios</a>
      <a href="../../inicio/index.php" class="btn btn-secondary btn-lg">⬅️ Regresar al Dashboard</a>
    </div>
  </div>

  <form id="cerrarForm" method="POST" style="display:none;">
    <input type="hidden" name="cerrar_id" id="cerrar_id">
  </form>

  <script>
    document.querySelectorAll(".cerrar-btn").forEach(btn => {
      btn.addEventListener("click", function() {
        const ventaId = this.dataset.id;

        Swal.fire({
          title: '¿Cerrar esta venta?',
          text: "Esta acción no se puede deshacer.",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#28a745',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Sí, cerrar',
          cancelButtonText: 'Cancelar'
        }).then((result) => {
          if (result.isConfirmed) {
            document.getElementById('cerrar_id').value = ventaId;
            document.getElementById('cerrarForm').submit();
          }
        });
      });
    });

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cerrar_id'])): ?>
    Swal.fire({
      icon: 'success',
      title: 'Venta cerrada correctamente',
      showConfirmButton: false,
      timer: 1500
    });
    <?php endif; ?>
  </script>
</body>
</html>
<?php desconectar($conn); ?>
