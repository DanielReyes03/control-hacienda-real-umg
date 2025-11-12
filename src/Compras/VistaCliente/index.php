<?php
session_start();
include("../../db/conexion.php");

$conn = conectar();

// Inicializar carrito
if (!isset($_SESSION['carrito'])) $_SESSION['carrito'] = [];

// Agregar producto vía AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $cantidad = intval($_POST['cantidad']);

    $existe = false;
    foreach ($_SESSION['carrito'] as &$item) {
        if ($item['id'] == $id) {
            $item['cantidad'] += $cantidad;
            $existe = true;
            break;
        }
    }
    if (!$existe) {
        $_SESSION['carrito'][] = ['id' => $id, 'nombre' => $nombre, 'precio' => $precio, 'cantidad' => $cantidad];
    }

    echo json_encode(['ok' => true, 'total_items' => count($_SESSION['carrito'])]);
    exit;
}

// Búsqueda
$filtro = isset($_GET['buscar']) ? $conn->real_escape_string($_GET['buscar']) : '';
$query = "
SELECT p.id, p.nombre, p.descripcion, p.precio, c.nombre AS categoria
FROM productos p
LEFT JOIN categorias_productos c ON p.categoria_id = c.id
WHERE p.es_item_menu = 1
AND (p.nombre LIKE '%$filtro%' OR c.nombre LIKE '%$filtro%')
";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Menú - La Hacienda Real</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1>🍗 Menú La Hacienda Real</h1>
    <a href="carrito.php" class="btn btn-primary">
      Ver carrito 🛒 <span id="carritoCount">(<?= count($_SESSION['carrito']) ?>)</span>
    </a>
  </div>

  <form method="GET" class="mb-4 d-flex gap-2">
    <input type="text" name="buscar" class="form-control" placeholder="Buscar producto o categoría..." value="<?= htmlspecialchars($filtro) ?>">
    <button class="btn btn-secondary">Buscar 🔍</button>
  </form>

  <div class="row">
    <?php while ($p = $result->fetch_assoc()): ?>
    <div class="col-md-4 mb-4">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <h5><?= htmlspecialchars($p['nombre']) ?></h5>
          <p><?= htmlspecialchars($p['descripcion']) ?></p>
          <p><strong>Q<?= number_format($p['precio'], 2) ?></strong></p>
          <div class="input-group">
            <input type="number" id="cantidad<?= $p['id'] ?>" class="form-control" min="1" value="1">
            <button class="btn btn-success agregar" 
              data-id="<?= $p['id'] ?>" 
              data-nombre="<?= htmlspecialchars($p['nombre']) ?>" 
              data-precio="<?= $p['precio'] ?>">Agregar 🛒</button>
          </div>
        </div>
      </div>
    </div>
    <?php endwhile; ?>
  </div>
</div>

<script>
$(document).ready(function(){
  $(".agregar").click(function(e){
    e.preventDefault();
    let id = $(this).data("id");
    let nombre = $(this).data("nombre");
    let precio = $(this).data("precio");
    let cantidad = parseInt($("#cantidad" + id).val());
    $.post("index.php", {id, nombre, precio, cantidad}, function(res){
      let r = JSON.parse(res);
      $("#carritoCount").text("(" + r.total_items + ")");
    });
  });
});
</script>
</body>
</html>
<?php desconectar($conn); ?>
