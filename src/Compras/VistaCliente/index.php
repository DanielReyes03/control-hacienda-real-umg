<?php
include("../../db/conexion.php");
require_once "../../login/check_adminclient.php";

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
        $_SESSION['carrito'][] = [
            'id' => $id,
            'nombre' => $nombre,
            'precio' => $precio,
            'cantidad' => $cantidad
        ];
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
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body { 
      font-family: 'Poppins', sans-serif; 
      background-color: #fff7f0; 
    }
    .card { 
      border-radius: 15px; 
      border: none; 
      box-shadow: 0 8px 20px rgba(0,0,0,0.1); 
      transition: transform 0.2s; 
    }
    .card:hover { 
      transform: translateY(-5px); 
      box-shadow: 0 12px 25px rgba(0,0,0,0.15); 
    }
    .card-title { 
      font-weight: 600; 
      font-size: 1.25rem; 
    }
    .card-text { 
      color: #555; 
    }
    .btn-agregar { 
      background-color: #ff7f50; 
      color: #fff; 
      font-weight: 600; 
    }
    .btn-agregar:hover { 
      background-color: #ff6333; 
    }
    .carrito-fijo { 
      position: fixed; 
      top: 20px; 
      right: 20px; 
      z-index: 1000; 
    }
    .btn-regresar {
      position: fixed;
      top: 20px;
      left: 20px;
      z-index: 1000;
      background-color: #6c757d;
      color: white;
      border: none;
      padding: 10px 18px;
      border-radius: 8px;
      font-weight: 600;
      transition: 0.3s;
      text-decoration: none;
    }
    .btn-regresar:hover {
      background-color: #5a6268;
    }
    .input-cantidad { 
      width: 60px; 
    }
    .category-badge { 
      background-color: #ffe5d0; 
      color: #ff6333; 
      font-weight: 600; 
      border-radius: 12px; 
      padding: 2px 8px; 
      font-size: 0.8rem; 
    }
    .header-title { 
      font-size: 2.5rem; 
      font-weight: 700; 
      color: #ff7f50; 
    }
    .header-subtitle { 
      font-size: 1.1rem; 
      color: #555; 
    }
  </style>
</head>
<body>

  <!-- Botón de regreso al Dashboard -->
  <a href="../../inicio/index.php" class="btn-regresar">⬅ Volver al Dashboard</a>

  <div class="container mt-5">
    <!-- Carrito fijo -->
    <div class="carrito-fijo">
      <a href="carrito.php" class="btn btn-primary btn-lg">
        🛒 Carrito <span id="carritoCount">(<?= count($_SESSION['carrito']) ?>)</span>
      </a>
    </div>

    <!-- Header -->
    <div class="text-center mb-5">
      <h1 class="header-title">La Hacienda Real</h1>
      <p class="header-subtitle">Explora nuestro menú y agrega tus platillos favoritos al carrito</p>
    </div>

    <!-- Formulario de búsqueda -->
    <form method="GET" class="mb-4 d-flex justify-content-center gap-2">
      <input type="text" name="buscar" class="form-control w-50" placeholder="Buscar producto o categoría..." value="<?= htmlspecialchars($filtro) ?>">
      <button class="btn btn-secondary">Buscar 🔍</button>
    </form>

    <!-- Productos en formato de Cards -->
    <div class="row">
      <?php while ($p = $result->fetch_assoc()): ?>
      <div class="col-md-4 mb-4">
        <div class="card h-100">
          <div class="card-body d-flex flex-column">
            <span class="category-badge mb-2"><?= htmlspecialchars($p['categoria']) ?></span>
            <h5 class="card-title"><?= htmlspecialchars($p['nombre']) ?></h5>
            <p class="card-text flex-grow-1"><?= htmlspecialchars($p['descripcion']) ?></p>
            <div class="d-flex justify-content-between align-items-center mt-3">
              <strong class="text-primary fs-5">Q<?= number_format($p['precio'], 2) ?></strong>
              <div class="input-group input-group-sm">
                <input type="number" id="cantidad<?= $p['id'] ?>" class="form-control input-cantidad" min="1" value="1">
                <button class="btn btn-agregar agregar" 
                  data-id="<?= $p['id'] ?>" 
                  data-nombre="<?= htmlspecialchars($p['nombre']) ?>" 
                  data-precio="<?= $p['precio'] ?>">Agregar</button>
              </div>
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
      let cantidad = parseInt($("#cantidad" + id).val()) || 1;

      $.post("index.php", {id, nombre, precio, cantidad}, function(res){
        let r = JSON.parse(res);
        $("#carritoCount").text("(" + r.total_items + ")");
        Swal.fire({
          icon: 'success',
          title: '¡Agregado!',
          text: nombre + ' se agregó al carrito.',
          timer: 1200,
          showConfirmButton: false
        });
      });
    });
  });
  </script>
</body>
</html>
<?php desconectar($conn); ?>
