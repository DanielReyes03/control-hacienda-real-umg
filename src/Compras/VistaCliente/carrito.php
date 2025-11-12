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
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', sans-serif; background-color: #fff7f0; }
    .card-cart { border-radius: 15px; box-shadow: 0 8px 20px rgba(0,0,0,0.1); padding: 20px; margin-bottom: 20px; background-color: #fff; }
    .card-cart h4 { font-weight: 600; color: #ff7f50; }
    .table thead th { background-color: #ffecd9; color: #ff7f50; font-weight: 600; }
    .table tbody td { vertical-align: middle; }
    .btn-accion { min-width: 40px; }
    .total-container { text-align: right; font-size: 1.5rem; font-weight: 700; color: #28a745; margin-top: 20px; }
    .btn-finalizar { background-color: #28a745; color: #fff; font-weight: 600; }
    .btn-finalizar:hover { background-color: #218838; }
    .btn-seguir { background-color: #6c757d; color: #fff; font-weight: 600; }
    .btn-seguir:hover { background-color: #5a6268; }
    .input-cantidad { width: 70px; display: inline-block; }
    .empty-cart { text-align: center; margin-top: 50px; font-size: 1.2rem; color: #555; }
    .acciones { display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px; }
  </style>
</head>
<body>
<div class="container mt-5">
  <div class="card-cart">
    <h4>🛍️ Tu Carrito</h4>
    <?php if (empty($_SESSION['carrito'])): ?>
      <p class="empty-cart">No hay productos en tu carrito.</p>
      <div class="acciones">
        <a href="index.php" class="btn btn-seguir">Volver al menú</a>
      </div>
    <?php else: ?>
      <form method="POST">
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th>Producto</th>
              <th>Precio</th>
              <th>Cantidad</th>
              <th>Total</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($_SESSION['carrito'] as $i): ?>
            <tr>
              <td><?= htmlspecialchars($i['nombre']) ?></td>
              <td>Q<?= number_format($i['precio'], 2) ?></td>
              <td>
                <form method="POST" style="display:inline;">
                  <input type="hidden" name="id" value="<?= $i['id'] ?>">
                  <input type="hidden" name="accion" value="actualizar">
                  <input type="number" name="cantidad" value="<?= $i['cantidad'] ?>" min="1" class="form-control form-control-sm input-cantidad">
                  <button class="btn btn-outline-secondary btn-sm btn-accion mt-1">↻</button>
                </form>
              </td>
              <td>Q<?= number_format($i['precio'] * $i['cantidad'], 2) ?></td>
              <td>
                <form method="POST" style="display:inline;">
                  <input type="hidden" name="id" value="<?= $i['id'] ?>">
                  <input type="hidden" name="accion" value="eliminar">
                  <button class="btn btn-danger btn-sm btn-accion">🗑️</button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </form>

      <div class="total-container">
        Total: Q<?= number_format($total, 2) ?>
      </div>

      <div class="acciones">
        <a href="checkout.php" class="btn btn-finalizar">Finalizar Pedido ✅</a>
        <a href="index.php" class="btn btn-seguir">Seguir comprando 🍽️</a>
      </div>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
