<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reportes - Hacienda Real</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <?php
    // Cabecera global del proyecto
    include("../compartido/componentes/cabecera/index.php");
    cabecera("Reportes");
  ?>

  <section class="dashboard">
    <div class="container">
      <h2 class="section-title">Dashboard Analítico</h2>

      <!-- KPIs -->
      <div class="kpis" style="display:grid; grid-template-columns: repeat(3, 1fr); gap:12px; margin-bottom:12px;">
        <div class="card">Total ventas del mes: <strong><span id="kpi-total">—</span></strong></div>
        <div class="card">Ticket promedio: <strong><span id="kpi-ticket">—</span></strong></div>
        <div class="card">Stock bajo: <strong><span id="kpi-stock">—</span></strong></div>
      </div>

      <!-- Filtros -->
      <div class="filtros card" style="margin:12px 0; padding:10px; display:flex; align-items:center; gap:10px;">
        <label for="anio"><strong>Año:</strong></label>
        <select id="anio">
          <option value="2025" selected>2025</option>
          <option value="2024">2024</option>
        </select>

        <label for="mes" style="margin-left:8px;"><strong>Mes:</strong></label>
        <select id="mes">
          <option value="1">Enero</option>
          <option value="2">Febrero</option>
          <option value="3">Marzo</option>
          <option value="4">Abril</option>
          <option value="5">Mayo</option>
          <option value="6">Junio</option>
          <option value="7">Julio</option>
          <option value="8">Agosto</option>
          <option value="9">Septiembre</option>
          <option value="10" selected>Octubre</option>
          <option value="11">Noviembre</option>
          <option value="12">Diciembre</option>
        </select>
      </div>

      <!-- Gráficas -->
      <div class="charts" style="display:grid; gap:16px;">
        <div class="card">
          <h3 class="section-subtitle">Ventas por día (mes seleccionado)</h3>
          <canvas id="chartVentasMes" height="120"></canvas>
        </div>

        <div class="card">
          <h3 class="section-subtitle">Top 5 productos más vendidos</h3>
          <canvas id="chartTopProductos" height="150"></canvas>
        </div>

        <div class="card">
          <h3 class="section-subtitle">Comparativa de ventas por sucursal</h3>
          <canvas id="chartSucursales" height="140"></canvas>
        </div>
        
        <div class="card">
          <h3 class="section-subtitle">Inventario crítico (Top 10 por faltante)</h3>
          <canvas id="chartInventarioCritico" height="140"></canvas>
        </div>

      </div>
    </div>
  </section>

  <!-- Librerías -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="dashboard.js"></script>
</body>
</html>
