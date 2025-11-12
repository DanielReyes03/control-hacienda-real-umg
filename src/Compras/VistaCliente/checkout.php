<?php
session_start();
include("../../db/conexion.php");
$conn = conectar();

if (empty($_SESSION['carrito'])) {
    die("<p>No hay productos en tu carrito.</p><a href='index.php'>Volver al menú</a>");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $direccion = $conn->real_escape_string($_POST['direccion']);
    $tipo_orden = $_POST['tipo_orden'];

    // --- 1️⃣ Verificar stock antes de registrar venta ---
    $errores_stock = [];

    foreach ($_SESSION['carrito'] as $item) {
        $producto_id = $item['id'];
        $cantidad_vendida = $item['cantidad'];

        // Obtener receta del producto
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
                    $errores_stock[] = "❌ No hay suficiente stock de <b>{$ing['nombre']}</b> para preparar <b>{$item['nombre']}</b> (stock actual: {$ing['stock']}, necesario: {$cant_necesaria})";
                }
            }
        }
    }

    // Si hay errores, mostrar y detener el proceso
    if (!empty($errores_stock)) {
        echo "<div class='container mt-4'><h3>No se puede completar el pedido</h3><ul class='list-group'>";
        foreach ($errores_stock as $err) {
            echo "<li class='list-group-item list-group-item-danger'>$err</li>";
        }
        echo "</ul><a href='carrito.php' class='btn btn-primary mt-3'>Volver al carrito</a></div>";
        desconectar($conn);
        exit;
    }

    // --- 2️⃣ Registrar cliente ---
    $conn->query("INSERT INTO clientes (nombre, telefono, direccion, creado_en)
                  VALUES ('$nombre', '$telefono', '$direccion', NOW())");
    $cliente_id = $conn->insert_id;

    // --- 3️⃣ Registrar venta (estado abierta) ---
    $total = 0;
    foreach ($_SESSION['carrito'] as $item) $total += $item['precio'] * $item['cantidad'];

    $conn->query("INSERT INTO ventas (cliente_id, tipo_orden, subtotal, total, estado, creado_en, fecha_venta)
                  VALUES ($cliente_id, '$tipo_orden', $total, $total, 'abierta', NOW(), CURDATE())");
    $venta_id = $conn->insert_id;

    // --- 4️⃣ Registrar detalles y descontar inventario ---
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

    // --- 5️⃣ Si es domicilio, registrar entrega ---
    if ($tipo_orden === 'domicilio') {
        $conn->query("INSERT INTO domicilios (venta_id, direccion_cliente, estado, hora_estimada)
                      VALUES ($venta_id, '$direccion', 'pendiente', DATE_ADD(NOW(), INTERVAL 30 MINUTE))");
    }

    unset($_SESSION['carrito']);
    desconectar($conn);

    echo "<div style='text-align:center;margin-top:40px;'>
          <h2>✅ Pedido registrado</h2>
          <p>Tu número de pedido es <strong>#$venta_id</strong></p>
          <a href='index.php' class='btn btn-primary mt-3'>Volver al menú</a>
          </div>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Finalizar Pedido</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <h2>🧾 Completa tu Pedido</h2>
  <form method="POST">
    <div class="mb-3">
      <label>Nombre del Cliente:</label>
      <input type="text" name="nombre" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Teléfono:</label>
      <input type="text" name="telefono" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Tipo de Orden:</label>
      <select name="tipo_orden" id="tipo_orden" class="form-select" required>
        <option value="en_sitio">En restaurante</option>
        <option value="para_llevar">Para llevar</option>
        <option value="domicilio">A domicilio</option>
      </select>
    </div>
    <div class="mb-3" id="direccion_div" style="display:none;">
      <label>Dirección (solo si es domicilio):</label>
      <textarea name="direccion" class="form-control"></textarea>
    </div>
    <button class="btn btn-success w-100">Confirmar Pedido ✅</button>
  </form>
</div>
<script>
document.getElementById('tipo_orden').addEventListener('change', e=>{
  document.getElementById('direccion_div').style.display = e.target.value === 'domicilio' ? 'block' : 'none';
});
</script>
</body>
</html>
