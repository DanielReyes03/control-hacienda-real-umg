// ==========================
// REFERENCIAS GLOBALES
// ==========================
let chartVentasMes;
let chartSucursales;

// ==========================
// VENTAS POR MES
// ==========================
async function cargarVentasMes(anio, mes) {
  try {
    const canvas = document.getElementById('chartVentasMes');
    if (!canvas) {
      console.error('No existe #chartVentasMes');
      return;
    }

    const res = await fetch(`api/ventas_mes.php?anio=${anio}&mes=${mes}`);
    if (!res.ok) {
      console.error('Error HTTP ventas_mes.php:', res.status);
      return;
    }

    const data = await res.json();

    const labels  = data.map(r => `Día ${r.dia}`);
    const valores = data.map(r => Number(r.total));

    const ctx = canvas.getContext('2d');

    if (chartVentasMes) {
      chartVentasMes.data.labels = labels;
      chartVentasMes.data.datasets[0].data = valores;
      chartVentasMes.data.datasets[0].label = `Ventas (Q) - ${anio}/${String(mes).padStart(2, '0')}`;
      chartVentasMes.update();
    } else {
      chartVentasMes = new Chart(ctx, {
        type: 'bar',
        data: {
          labels,
          datasets: [{
            label: `Ventas (Q) - ${anio}/${String(mes).padStart(2, '0')}`,
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

// ==========================
// KPIs (usa kpsi.php con anio/mes)
// ==========================
async function cargarKPIs(anio, mes) {
  try {
    const res = await fetch(`api/kpsi.php?anio=${anio}&mes=${mes}`);
    if (!res.ok) {
      console.error('Error HTTP kpsi.php:', res.status);
      return;
    }

    const data = await res.json();

    const kpiTotal  = document.getElementById('kpi-total');
    const kpiTicket = document.getElementById('kpi-ticket');

    const totalMes   = Number(data.total_mes ?? 0);
    const ticketProm = Number(data.ticket_promedio ?? 0);

    if (kpiTotal) {
      kpiTotal.textContent = `Q${totalMes.toLocaleString()}`;
    }
    if (kpiTicket) {
      kpiTicket.textContent = `Q${ticketProm.toFixed(2)}`;
    }
  } catch (err) {
    console.error('Error cargarKPIs:', err);
  }
}

// ==========================
// VENTAS POR SUCURSAL
// ==========================
async function cargarVentasSucursal(anio, mes) {
  try {
    const canvas = document.getElementById('chartSucursales');
    if (!canvas) {
      console.error('No existe #chartSucursales');
      return;
    }

    const res = await fetch(`api/ventas_sucursal.php?anio=${anio}&mes=${mes}`);
    if (!res.ok) {
      console.error('Error HTTP ventas_sucursal.php:', res.status);
      return;
    }

    const data = await res.json();

    const labels  = data.map(r => r.sucursal);
    const valores = data.map(r => Number(r.total));

    const ctx = canvas.getContext('2d');

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
  } catch (err) {
    console.error('Error cargarVentasSucursal:', err);
  }
}

// ==========================
// INIT ÚNICO
// ==========================
document.addEventListener('DOMContentLoaded', () => {
  const anioSel = document.getElementById('anio');
  const mesSel  = document.getElementById('mes');

  // Si no existen los selects, usamos la fecha actual
  let anio = new Date().getFullYear();
  let mes  = new Date().getMonth() + 1;

  if (anioSel && mesSel) {
    anio = Number(anioSel.value);
    mes  = Number(mesSel.value);
  }

  const recargarTodo = (a, m) => {
    const A = Number(a);
    const M = Number(m);
    cargarVentasMes(A, M);
    cargarVentasSucursal(A, M);
    cargarKPIs(A, M); // ⬅️ aquí se actualizan los KPIs según el mes
  };

  // Carga inicial
  recargarTodo(anio, mes);

  // Listeners de filtros
  if (anioSel && mesSel) {
    const onChange = () => {
      const nuevoAnio = anioSel.value;
      const nuevoMes  = mesSel.value;
      recargarTodo(nuevoAnio, nuevoMes);
    };
    anioSel.addEventListener('change', onChange);
    mesSel.addEventListener('change', onChange);
  }
});
