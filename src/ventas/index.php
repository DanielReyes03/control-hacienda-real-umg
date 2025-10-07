<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Hacienda Real · Módulo de Ventas</title>

  <!-- Fuentes -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Estilos -->
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="app">
    <!-- ===== Sidebar ===== -->
    <aside>
      <div class="brand">
        <div class="brand-logo">HR</div>
        <div>
          <h1>Hacienda Real</h1>
          <span class="tag">Restaurante · Zona 10</span>
        </div>
      </div>

      <nav class="nav">
        <a href="#" class="active">📊 Panel de Ventas <span class="pill">Hoy</span></a>
        <a href="#">🧾 Órdenes</a>
        <a href="#">🍽️ Menú</a>
        <a href="#">👥 Clientes</a>
        <a href="#">💳 Pagos</a>
        <a href="#">📦 Inventario</a>
        <a href="#">⚙️ Configuración</a>
      </nav>

      <div class="aside-footer">
        <strong>Sugerencia:</strong> Usa <em>Nueva Venta</em> para abrir una orden rápida. Puedes filtrar por método de pago o canal (Salón / Para llevar / Delivery).
      </div>
    </aside>

    <!-- ===== Main ===== -->
    <main>
      <div class="topbar">
        <div class="search">
          <svg class="icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21 21l-3.8-3.8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><circle cx="10.5" cy="10.5" r="6.5" stroke="currentColor" stroke-width="1.6"/></svg>
          <input type="text" placeholder="Buscar en ventas, órdenes o clientes…"/>
        </div>
        <div class="actions">
          <button class="btn">Exportar</button>
          <button class="btn primary">Imprimir corte</button>
          <button class="btn cta">+ Nueva Venta</button>
          <div class="avatar"></div>
        </div>
      </div>

      <div class="content">
        <!-- KPIs -->
        <section class="kpis">
          <div class="card">
            <h3>Total vendido (hoy)</h3>
            <div class="metric"><div class="value">Q 12,450.00</div><div class="trend up">▲ 8.2%</div></div>
          </div>
          <div class="card">
            <h3>Tickets</h3>
            <div class="metric"><div class="value">184</div><div class="trend up">▲ 3.1%</div></div>
          </div>
          <div class="card">
            <h3>Promedio por ticket</h3>
            <div class="metric"><div class="value">Q 67.66</div><div class="trend">—</div></div>
          </div>
          <div class="card">
            <h3>Propinas</h3>
            <div class="metric"><div class="value">Q 1,120.00</div><div class="trend down">▼ 1.4%</div></div>
          </div>
        </section>

        <!-- Filtros -->
        <section class="filters">
          <div class="chip">📅 <input type="date"/></div>
          <div class="chip">Canal
            <select>
              <option>Todos</option>
              <option>Salón</option>
              <option>Para llevar</option>
              <option>Delivery</option>
            </select>
          </div>
          <div class="chip">Estado
            <select>
              <option>Todos</option>
              <option>Pagado</option>
              <option>Pendiente</option>
              <option>Cancelado</option>
            </select>
          </div>
          <div class="chip">Método
            <select>
              <option>Todos</option>
              <option>Efectivo</option>
              <option>Tarjeta</option>
              <option>Transferencia</option>
            </select>
          </div>
          <span class="chip">📍 Sucursal: Zona 10</span>
        </section>

        <!-- Tabs -->
        <input type="radio" name="tabs" id="tab1" checked>
        <input type="radio" name="tabs" id="tab2">
        <input type="radio" name="tabs" id="tab3">
        <div class="tabs">
          <div class="tab-head">
            <label for="tab1">Punto de Venta</label>
            <label for="tab2">Ventas del Día</label>
            <label for="tab3">Reportes</label>
          </div>

          <!-- ===== POS ===== -->
          <div class="tab-content" id="content1">
            <div class="pos">
              <div class="catalogo">
                <!-- Producto -->
                <article class="producto">
                  <div class="img"></div>
                  <div class="info">
                    <div class="p-name">Parrillada Hacienda (2 pax)</div>
                    <div class="p-meta">
                      <span class="badge">Q 219</span>
                      <span class="badge">🍖 Asados</span>
                    </div>
                  </div>
                </article>
                <article class="producto">
                  <div class="img"></div>
                  <div class="info">
                    <div class="p-name">Lomo de res 12oz</div>
                    <div class="p-meta">
                      <span class="badge">Q 165</span>
                      <span class="badge">🥩 Corte</span>
                    </div>
                  </div>
                </article>
                <article class="producto">
                  <div class="img"></div>
                  <div class="info">
                    <div class="p-name">Ensalada fresca</div>
                    <div class="p-meta">
                      <span class="badge">Q 52</span>
                      <span class="badge">🥗 Ensaladas</span>
                    </div>
                  </div>
                </article>
                <article class="producto">
                  <div class="img"></div>
                  <div class="info">
                    <div class="p-name">Sopa de tortilla</div>
                    <div class="p-meta">
                      <span class="badge">Q 38</span>
                      <span class="badge">🍲 Sopas</span>
                    </div>
                  </div>
                </article>
                <article class="producto">
                  <div class="img"></div>
                  <div class="info">
                    <div class="p-name">Limonada mineral</div>
                    <div class="p-meta">
                      <span class="badge">Q 22</span>
                      <span class="badge">🥤 Bebidas</span>
                    </div>
                  </div>
                </article>
                <article class="producto">
                  <div class="img"></div>
                  <div class="info">
                    <div class="p-name">Flan de la casa</div>
                    <div class="p-meta">
                      <span class="badge">Q 28</span>
                      <span class="badge">🍮 Postres</span>
                    </div>
                  </div>
                </article>
              </div>

              <!-- Carrito -->
              <aside class="carrito">
                <div class="cart">
                  <h4>🧾 Orden #1245 · Mesa 6</h4>
                  <div class="cart-list">
                    <div class="item">
                      <div>
                        <div class="name">Parrillada Hacienda</div>
                        <div class="qty">2 × Q 219</div>
                      </div>
                      <div>Q 438</div>
                    </div>
                    <div class="item">
                      <div>
                        <div class="name">Limonada mineral</div>
                        <div class="qty">3 × Q 22</div>
                      </div>
                      <div>Q 66</div>
                    </div>
                    <div class="item">
                      <div>
                        <div class="name">Flan de la casa</div>
                        <div class="qty">2 × Q 28</div>
                      </div>
                      <div>Q 56</div>
                    </div>
                  </div>
                  <div class="cart-foot">
                    <div class="row"><span>Subtotal</span><span>Q 560.00</span></div>
                    <div class="row"><span>Propina 10%</span><span>Q 56.00</span></div>
                    <div class="row total"><span>Total</span><span>Q 616.00</span></div>
                    <div class="pay-grid">
                      <button class="btn">Guardar</button>
                      <button class="btn">Dividir cuenta</button>
                      <button class="btn primary">Pago tarjeta</button>
                      <button class="btn cta">Pagar en efectivo</button>
                    </div>
                  </div>
                </div>
              </aside>
            </div>
          </div>

          <!-- ===== Ventas del día ===== -->
          <div class="tab-content" id="content2">
            <table class="table">
              <thead>
                <tr>
                  <th># Ticket</th>
                  <th>Hora</th>
                  <th>Cliente</th>
                  <th>Canal</th>
                  <th>Método</th>
                  <th>Total</th>
                  <th>Estado</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>0001245</td>
                  <td>12:34</td>
                  <td>Walk-in</td>
                  <td>Salón</td>
                  <td>Tarjeta</td>
                  <td>Q 384.00</td>
                  <td><span class="status paid">Pagado</span></td>
                </tr>
                <tr>
                  <td>0001246</td>
                  <td>12:58</td>
                  <td>Uber Eats</td>
                  <td>Delivery</td>
                  <td>Efectivo</td>
                  <td>Q 186.00</td>
                  <td><span class="status pending">Pendiente</span></td>
                </tr>
                <tr>
                  <td>0001247</td>
                  <td>13:15</td>
                  <td>Reserva Mesa 8</td>
                  <td>Salón</td>
                  <td>Tarjeta</td>
                  <td>Q 912.00</td>
                  <td><span class="status paid">Pagado</span></td>
                </tr>
                <tr>
                  <td>0001248</td>
                  <td>13:31</td>
                  <td>Para llevar</td>
                  <td>Take out</td>
                  <td>Transferencia</td>
                  <td>Q 142.00</td>
                  <td><span class="status cancel">Cancelado</span></td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- ===== Reportes ===== -->
          <div class="tab-content" id="content3">
            <div class="grid-2">
              <div class="card">
                <h3>Ventas por método de pago</h3>
                <div class="placeholder">Gráfico de barras (placeholder)</div>
              </div>
              <div class="card">
                <h3>Ventas por canal</h3>
                <div class="placeholder">Gráfico de pastel (placeholder)</div>
              </div>
              <div class="card">
                <h3>Top productos</h3>
                <div class="placeholder">Ranking (placeholder)</div>
              </div>
              <div class="card">
                <h3>Horas pico</h3>
                <div class="placeholder">Heatmap temporal (placeholder)</div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </main>
  </div>
</body>
</html>
