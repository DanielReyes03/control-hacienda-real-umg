<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas - Hacienda Real Guatemala</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="../compartido/componentes/cabecera/cabecera.css">
</head>
<body>
<?php 
    include("../compartido/componentes/cabecera/index.php");
    cabecera("Ventas"); 
?>
<section class="sales">
    <div class="container">
        <h2>Registro de Ventas</h2>
        <p>Gestiona las ventas del restaurante en tiempo real.</p>

        <form action="procesar_venta.php" method="POST" class="sale-form">
            <div class="form-group">
                <label for="cliente">Cliente:</label>
                <input type="text" id="cliente" name="cliente" placeholder="Nombre del cliente" required>
            </div>

            <div class="form-group">
                <label for="producto">Producto:</label>
                <select id="producto" name="producto" required>
                    <option value="">Seleccione un producto</option>
                    <option value="Parrillada Mixta">Parrillada Mixta</option>
                    <option value="Lomo de Res">Lomo de Res</option>
                    <option value="Costillas BBQ">Costillas BBQ</option>
                    <option value="Bebida Natural">Bebida Natural</option>
                    <option value="Postre de la Casa">Postre de la Casa</option>
                </select>
            </div>

            <div class="form-group">
                <label for="cantidad">Cantidad:</label>
                <input type="number" id="cantidad" name="cantidad" min="1" required>
            </div>

            <div class="form-group">
                <label for="metodo_pago">Método de Pago:</label>
                <select id="metodo_pago" name="metodo_pago" required>
                    <option value="">Seleccione método</option>
                    <option value="Efectivo">Efectivo</option>
                    <option value="Tarjeta">Tarjeta</option>
                    <option value="Transferencia">Transferencia</option>
                </select>
            </div>

            <div class="form-group">
                <label for="total">Total (Q):</label>
                <input type="text" id="total" name="total" placeholder="0.00" readonly>
            </div>

            <button type="submit" class="btn">Registrar Venta</button>
        </form>

        <h3>Historial de Ventas</h3>
        <table class="sales-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Cliente</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Método de Pago</th>
                    <th>Total (Q)</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    // Ejemplo de datos simulados (puedes reemplazar por una consulta SQL)
                    $ventas = [
                        ["id" => 1, "cliente" => "Carlos Pérez", "producto" => "Lomo de Res", "cantidad" => 2, "metodo" => "Tarjeta", "total" => 250, "fecha" => "2025-10-09"],
                        ["id" => 2, "cliente" => "Ana Gómez", "producto" => "Parrillada Mixta", "cantidad" => 1, "metodo" => "Efectivo", "total" => 180, "fecha" => "2025-10-08"],
                        ["id" => 3, "cliente" => "Luis García", "producto" => "Postre de la Casa", "cantidad" => 3, "metodo" => "Transferencia", "total" => 90, "fecha" => "2025-10-07"]
                    ];
                    foreach ($ventas as $venta) {
                        echo "<tr>
                                <td>{$venta['id']}</td>
                                <td>{$venta['cliente']}</td>
                                <td>{$venta['producto']}</td>
                                <td>{$venta['cantidad']}</td>
                                <td>{$venta['metodo']}</td>
                                <td>Q{$venta['total']}</td>
                                <td>{$venta['fecha']}</td>
                              </tr>";
                    }
                ?>
            </tbody>
        </table>
    </div>
</section>

<footer class="footer">
    <div class="container">
        <p>&copy; 2025 Hacienda Real Guatemala. Todos los derechos reservados.</p>
        <p>Visita <a href="https://haciendareal.net/" target="_blank">haciendareal.net</a> para más información.</p>
    </div>
</footer>

<script>
    // Simulación de cálculo total
    const producto = document.getElementById('producto');
    const cantidad = document.getElementById('cantidad');
    const total = document.getElementById('total');

    const precios = {
        "Parrillada Mixta": 180,
        "Lomo de Res": 125,
        "Costillas BBQ": 150,
        "Bebida Natural": 20,
        "Postre de la Casa": 30
    };

    function calcularTotal() {
        const p = producto.value; // nombre del producto
        const c = parseInt(cantidad.value) || 0; // cantidad del producto
        total.value = p && c ? (precios[p] * c).toFixed(2) : "0.00";
    }

    producto.addEventListener('change', calcularTotal);
    cantidad.addEventListener('input', calcularTotal);
</script>

</body>
</html>
