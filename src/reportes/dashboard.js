
// ==== VENTAS POR MES ====
let chartVentasMes;

async function cargarVentasMes(anio, mes) {
  try {
    const canvas = document.getElementById('chartVentasMes');
    if (!canvas) { console.error('No existe #chartVentasMes'); return; }

    const res = await fetch(`api/ventas_mes.php?anio=${anio}&mes=${mes}`);
    const data = await res.json();

    const labels  = data.map(r => `Día ${r.dia}`);
    const valores = data.map(r => Number(r.total));

    const ctx = canvas.getContext('2d');

    if (chartVentasMes) {
      chartVentasMes.data.labels = labels;
      chartVentasMes.data.datasets[0].data = valores;
      chartVentasMes.update();
    } else {
      chartVentasMes = new Chart(ctx, {
        type: 'bar',
        data: {
          labels,
          datasets: [{
            label: `Ventas (Q) - ${anio}/${String(mes).padStart(2,'0')}`,
            data: valores
          }]
        },
        options: {
          responsive: true,
          scales: { y: { beginAtZero: true } }
        }
      });
    }
  } catch (err) {
    console.error('Error cargarVentasMes:', err);
  }
}

// ==== KPIs (NO toca ningún <canvas>) ====
async function cargarKPIs(anio, mes) {
  try {
    const res = await fetch(`api/kpis.php?anio=${anio}&mes=${mes}`);
    const data = await res.json();

    const kpiTotal   = document.getElementById('kpi-total');
    const kpiTicket  = document.getElementById('kpi-ticket');

    if (kpiTotal)  kpiTotal.textContent  = `Q${(data.total_mes ?? 0).toLocaleString()}`;
    if (kpiTicket) kpiTicket.textContent = `Q${Number(data.ticket_promedio ?? 0).toFixed(2)}`;
  } catch (err) {
    console.error('Error cargarKPIs:', err);
  }
}

// ==== INIT SEGURO ====
// Nada depende de nada; si una falla, la otra sigue.
document.addEventListener('DOMContentLoaded', () => {
  const anioSel = document.getElementById('anio');
  const mesSel  = document.getElementById('mes');

  const anio = anioSel ? anioSel.value : new Date().getFullYear();
  const mes  = mesSel  ? mesSel.value  : (new Date().getMonth() + 1);

  // Lanza en paralelo, pero si una falla, no tumba la otra
  cargarVentasMes(anio, mes);
  cargarKPIs(anio, mes);

  // Si tienes selects, vuelve a cargar cuando cambien
  const actualizar = () => {
    const a = anioSel.value;
    const m = mesSel.value;
    cargarVentasMes(a, m);
    cargarKPIs(a, m);
  };
  if (anioSel && mesSel) {
    anioSel.addEventListener('change', actualizar);
    mesSel.addEventListener('change', actualizar);
  }
});

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

async function cargarKPIs() {
  try {
    const res = await fetch('api/kpis.php');
    const data = await res.json();

    // Mostrar en el frontend
    document.getElementById('kpi-total').textContent = `Q${data.total_mes.toLocaleString()}`;
    document.getElementById('kpi-ticket').textContent = `Q${data.ticket_promedio.toFixed(2)}`;
  } catch (err) {
    console.error('Error al cargar KPIs:', err);
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

// document.addEventListener("DOMContentLoaded", async () => {
//   try {
//     await Promise.all([
//       cargarKPIs(),
//       cargarVentasMes()
//     ]);
//   } catch (err) {
//     console.error("Error al cargar el dashboard:", err);
//   }
// });
