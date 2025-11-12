<?php
session_start();

// Eliminar o actualizar productos
if (isset($_POST['accion'])) {
    if ($_POST['accion'] === 'eliminar') {
        $id = $_POST['id'];
        foreach ($_SESSION['carrito'] as $i => $item) {
            if ($item['id'] == $id) unset($_SESSION['carrito'][$i]);
        }
        $_SESSION['carrito'] = array_values($_SESSION['carrito']);
    }

    if ($_POST['accion'] === 'actualizar') {
        $id = $_POST['id'];
        $cantidad = max(1, intval($_POST['cantidad']));
        foreach ($_SESSION['carrito'] as &$item) {
            if ($item['id'] == $id) $item['cantidad'] = $cantidad;
        }
    }
}

$total = 0;
foreach ($_SESSION['carrito'] as $i) $total += $i['precio'] * $i['cantidad'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Carrito - La Hacienda Real</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <h2>🛍️ Tu Carrito</h2>
  <?php if (empty($_SESSION['carrito'])): ?>
    <p>No hay productos en tu carrito.</p>
    <a href="index.php" class="btn btn-primary">Volver al menú</a>
  <?php else: ?>
    <form method="POST">
      <table class="table table-bordered">
        <thead><tr><th>Producto</th><th>Precio</th><th>Cantidad</th><th>Total</th><th>Acción</th></tr></thead>
        <tbody>
          <?php foreach ($_SESSION['carrito'] as $i): ?>
          <tr>
            <td><?= htmlspecialchars($i['nombre']) ?></td>
            <td>Q<?= number_format($i['precio'], 2) ?></td>
            <td>
              <form method="POST" style="display:inline;">
                <input type="hidden" name="id" value="<?= $i['id'] ?>">
                <input type="hidden" name="accion" value="actualizar">
                <input type="number" name="cantidad" value="<?= $i['cantidad'] ?>" min="1" class="form-control form-control-sm" style="width:80px;display:inline-block;">
                <button class="btn btn-sm btn-outline-secondary">↻</button>
              </form>
            </td>
            <td>Q<?= number_format($i['precio'] * $i['cantidad'], 2) ?></td>
            <td>
              <form method="POST" style="display:inline;">
                <input type="hidden" name="id" value="<?= $i['id'] ?>">
                <input type="hidden" name="accion" value="eliminar">
                <button class="btn btn-sm btn-danger">🗑️</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </form>
    <h4>Total: Q<?= number_format($total, 2) ?></h4>
    <a href="checkout.php" class="btn btn-success">Finalizar Pedido ✅</a>
    <a href="index.php" class="btn btn-secondary">Seguir comprando 🍽️</a>
  <?php endif; ?>
</div>
</body>
</html>
