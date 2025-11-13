<?php
session_start();
include("../../db/conexion.php");
$conn = conectar();

if (empty($_SESSION['carrito'])) {
    die("
    <div class='container text-center mt-5'>
        <h3>🛒 No hay productos en tu carrito.</h3>
        <a href='index.php' class='btn btn-primary mt-3'>Volver al menú</a>
    </div>");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $direccion = $conn->real_escape_string($_POST['direccion']);
    $tipo_orden = $_POST['tipo_orden'];

    // --- Verificar stock ---
    $errores_stock = [];
    foreach ($_SESSION['carrito'] as $item) {
        $producto_id = $item['id'];
        $cantidad_vendida = $item['cantidad'];

        $receta = $conn->query("SELECT receta_id FROM productos WHERE id=$producto_id")->fetch_assoc();
        if ($receta && $receta['receta_id']) {
            $receta_id = $receta['receta_id'];
            $ingredientes = $conn->query("
                SELECT r.materia_prima_id, r.cantidad, mp.nombre, mp.stock
                FROM receta_detalle r
                JOIN inventario_materias_primas mp ON mp.id = r.materia_prima_id
                WHERE receta_id=$receta_id
            ");
            while ($ing = $ingredientes->fetch_assoc()) {
                $cant_necesaria = $ing['cantidad'] * $cantidad_vendida;
                if ($ing['stock'] < $cant_necesaria) {
                    $errores_stock[] = "❌ No hay suficiente stock de <b>{$ing['nombre']}</b> para preparar <b>{$item['nombre']}</b>.";
                }
            }
        }
    }

    if (!empty($errores_stock)) {
        echo "<div class='container mt-5'><div class='alert alert-danger'><h4>⚠️ No se puede completar el pedido</h4><ul>";
        foreach ($errores_stock as $err) echo "<li>$err</li>";
        echo "</ul><a href='carrito.php' class='btn btn-secondary mt-3'>Volver al carrito</a></div></div>";
        desconectar($conn);
        exit;
    }

    // --- Registrar cliente ---
    $conn->query("INSERT INTO clientes (nombre, telefono, direccion, creado_en)
                  VALUES ('$nombre', '$telefono', '$direccion', NOW())");
    $cliente_id = $conn->insert_id;

    // --- Registrar venta ---
    $total = 0;
    foreach ($_SESSION['carrito'] as $item) $total += $item['precio'] * $item['cantidad'];
    $conn->query("INSERT INTO ventas (cliente_id, tipo_orden, subtotal, total, estado, creado_en, fecha_venta)
                  VALUES ($cliente_id, '$tipo_orden', $total, $total, 'abierta', NOW(), CURDATE())");
    $venta_id = $conn->insert_id;

    // --- Registrar detalles ---
    foreach ($_SESSION['carrito'] as $item) {
        $producto_id = $item['id'];
        $cantidad = $item['cantidad'];
        $precio = $item['precio'];
        $subtotal = $precio * $cantidad;

        $conn->query("INSERT INTO ventas_detalle (venta_id, producto_id, cantidad, precio_unitario, precio_total)
                      VALUES ($venta_id, $producto_id, $cantidad, $precio, $subtotal)");

        $receta = $conn->query("SELECT receta_id FROM productos WHERE id=$producto_id")->fetch_assoc();
        if ($receta && $receta['receta_id']) {
            $receta_id = $receta['receta_id'];
            $ingredientes = $conn->query("SELECT materia_prima_id, cantidad FROM receta_detalle WHERE receta_id=$receta_id");
            while ($ing = $ingredientes->fetch_assoc()) {
                $mp_id = $ing['materia_prima_id'];
                $cant_usada = $ing['cantidad'] * $cantidad;
                $conn->query("UPDATE inventario_materias_primas SET stock = stock - $cant_usada WHERE id=$mp_id");
                $conn->query("INSERT INTO movimientos_inventario 
                    (inventario_mp_item_id, tipo_movimiento, cantidad, tabla_referencia, referencia_id, creado_en, notas)
                    VALUES ($mp_id, 'salida', $cant_usada, 'ventas', $venta_id, NOW(), 'Descuento por venta')");
            }
        }
    }

    // --- Si es domicilio ---
    if ($tipo_orden === 'domicilio') {
        $conn->query("INSERT INTO domicilios (venta_id, direccion_cliente, estado, hora_estimada)
                      VALUES ($venta_id, '$direccion', 'pendiente', DATE_ADD(NOW(), INTERVAL 30 MINUTE))");
    }

    unset($_SESSION['carrito']);
    desconectar($conn);

    echo "<div class='container text-center mt-5'>
            <div class='alert alert-success p-4 shadow-sm rounded'>
                <h2>✅ ¡Pedido registrado con éxito!</h2>
                <p>Tu número de pedido es <strong>#{$venta_id}</strong></p>
                <a href='index.php' class='btn btn-success mt-3'>Volver al menú 🍽️</a>
            </div>
          </div>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Finalizar Pedido | La Hacienda Real</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #fff8f1;
      min-height: 100vh;
    }
    .checkout-card {
      background: white;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      padding: 30px;
      max-width: 600px;
      margin: 60px auto;
    }
    h2 {
      color: #ff7f50;
      font-weight: 700;
      margin-bottom: 25px;
      text-align: center;
    }
    label {
      font-weight: 600;
      color: #444;
    }
    .btn-confirmar {
      background-color: #28a745;
      color: white;
      font-weight: 600;
      font-size: 1.1rem;
      transition: 0.3s;
    }
    .btn-confirmar:hover {
      background-color: #218838;
    }
    .form-select, .form-control {
      border-radius: 8px;
    }
    .direccion-div {
      display: none;
      animation: fadeIn 0.3s ease-in;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-5px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <div class="checkout-card">
    <h2>🧾 Finalizar Pedido</h2>
    <form method="POST">
      <div class="mb-3">
        <label><i class="bi bi-person"></i> Nombre del Cliente:</label>
        <input type="text" name="nombre" class="form-control" required>
      </div>
      <div class="mb-3">
        <label><i class="bi bi-telephone"></i> Teléfono:</label>
        <input type="text" name="telefono" class="form-control" required>
      </div>
      <div class="mb-3">
        <label><i class="bi bi-bag-check"></i> Tipo de Orden:</label>
        <select name="tipo_orden" id="tipo_orden" class="form-select" required>
          <option value="en_sitio">En restaurante</option>
          <option value="para_llevar">Para llevar</option>
          <option value="domicilio">A domicilio</option>
        </select>
      </div>
      <div class="mb-3 direccion-div" id="direccion_div">
        <label><i class="bi bi-geo-alt"></i> Dirección (solo si es domicilio):</label>
        <textarea name="direccion" class="form-control" placeholder="Ej. 5ta calle zona 1, Cobán, Alta Verapaz"></textarea>
      </div>
      <button class="btn btn-confirmar w-100">Confirmar Pedido ✅</button>
    </form>
  </div>

  <script>
    document.getElementById('tipo_orden').addEventListener('change', e=>{
      document.getElementById('direccion_div').style.display = 
        e.target.value === 'domicilio' ? 'block' : 'none';
    });
  </script>
</body>
</html>
