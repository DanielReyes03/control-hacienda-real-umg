let chartVentasMes; // para actualizar el mismo gráfico sin recrearlo

async function cargarVentasMes(anio, mes) {
  const res = await fetch(`api/ventas_mes.php?anio=${anio}&mes=${mes}`);
  const data = await res.json();

  const labels = data.map(r => `Día ${r.dia}`);
  const valores = data.map(r => Number(r.total));

  const ctx = document.getElementById('chartVentasMes').getContext('2d');

  if (chartVentasMes) {
    chartVentasMes.data.labels = labels;
    chartVentasMes.data.datasets[0].data = valores;
    chartVentasMes.data.datasets[0].label = `Ventas (Q) - ${anio}/${mes}`;
    chartVentasMes.update();
  } else {
    chartVentasMes = new Chart(ctx, {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          label: `Ventas (Q) - ${anio}/${mes}`,
          data: valores,
          backgroundColor: '#ffb347'
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: true } },
        scales: {
          y: { beginAtZero: true }
        }
      }
    });
  }
}

let chartTopProductos; // referencia global del gráfico

async function cargarTopProductos(anio, mes) {
  const res = await fetch(`api/top_productos.php?anio=${anio}&mes=${mes}`);
  const data = await res.json();

  const labels = data.map(r => r.producto);
  const valores = data.map(r => Number(r.total_vendido));

  const ctx = document.getElementById('chartTopProductos').getContext('2d');

  if (chartTopProductos) {
    chartTopProductos.data.labels = labels;
    chartTopProductos.data.datasets[0].data = valores;
    chartTopProductos.update();
  } else {
    chartTopProductos = new Chart(ctx, {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          label: 'Cantidad vendida',
          data: valores,
          backgroundColor: ['#e76f51', '#f4a261', '#e9c46a', '#2a9d8f', '#264653']
        }]
      },
      options: {
        indexAxis: 'y', // barras horizontales
        responsive: true,
        plugins: {
          legend: { display: false },
          title: { display: false }
        },
        scales: {
          x: { beginAtZero: true },
          y: { ticks: { color: '#111' } }
        }
      }
    });
  }
}

let chartSucursales; // referencia global

async function cargarVentasSucursal(anio, mes) {
  const res = await fetch(`api/ventas_sucursal.php?anio=${anio}&mes=${mes}`);
  const data = await res.json();

  const labels  = data.map(r => r.sucursal);
  const valores = data.map(r => Number(r.total));

  const ctx = document.getElementById('chartSucursales').getContext('2d');

  if (chartSucursales) {
    chartSucursales.data.labels = labels;
    chartSucursales.data.datasets[0].data = valores;
    chartSucursales.update();
  } else {
    chartSucursales = new Chart(ctx, {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          label: 'Ventas (Q) por sucursal',
          data: valores
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: (ctx) => ` Q${Number(ctx.parsed.y ?? ctx.parsed.x).toFixed(2)}`
            }
          }
        },
        scales: {
          y: { beginAtZero: true }
        }
      }
    });
  }
}

let chartInventarioCritico; // referencia global

async function cargarInventarioCritico(/* sucursalId opcional */) {
  // Si en el futuro agregas filtro por sucursal, pásalo aquí:
  // const res = await fetch(`api/inventario_bajo.php?sucursal_id=${sucursalId}&limit=10`);
  const res = await fetch('api/inventario_bajo.php?limit=10');
  const data = await res.json();

  // Actualiza KPI "stock bajo" con el conteo
  const elKpi = document.getElementById('kpi-stock');
  if (elKpi) elKpi.textContent = String(data.length);

  const labels = data.map(r => `${r.nombre} (${r.sucursal})`);
  const valores = data.map(r => Number(r.faltante)); // cuánto te falta para llegar al mínimo

  const ctx = document.getElementById('chartInventarioCritico').getContext('2d');

  if (chartInventarioCritico) {
    chartInventarioCritico.data.labels = labels;
    chartInventarioCritico.data.datasets[0].data = valores;
    chartInventarioCritico.update();
  } else {
    chartInventarioCritico = new Chart(ctx, {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          label: 'Faltante respecto al mínimo (unidades)',
          data: valores
        }]
      },
      options: {
        indexAxis: 'y', // barras horizontales
        responsive: true,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: (ctx) => ` Falta: ${Number(ctx.parsed.x ?? ctx.parsed.y).toFixed(2)}`
            }
          }
        },
        scales: {
          x: { beginAtZero: true },
          y: { ticks: { color: '#111' } }
        }
      }
    });
  }
}


(function init() {
  const anioSel = document.getElementById('anio');
  const mesSel  = document.getElementById('mes');

  // Carga inicial de todos los gráficos
  cargarVentasMes(anioSel.value, mesSel.value);
  cargarTopProductos(anioSel.value, mesSel.value);
  cargarVentasSucursal(anioSel.value, mesSel.value);
  cargarInventarioCritico(); // ⬅️ Inventario no depende de mes/año

  // Actualiza gráficos dependientes de filtros
  const actualizar = () => {
    cargarVentasMes(anioSel.value, mesSel.value);
    cargarTopProductos(anioSel.value, mesSel.value);
    cargarVentasSucursal(anioSel.value, mesSel.value);
    // cargarInventarioCritico(); // normalmente no cambia con mes/año
  };

  anioSel.addEventListener('change', actualizar);
  mesSel.addEventListener('change', actualizar);
})();
