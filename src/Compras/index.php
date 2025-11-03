<?php
require_once "../login/check_adminclient.php"; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Menú - Hacienda Real Guatemala</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="../compartido/componentes/cabecera/cabecera.css">
  <style>
    @media print {
      body * { visibility: hidden; }
      #printable-receipt, #printable-receipt * { visibility: visible; }
      #printable-receipt { position: absolute; left: 0; top: 0; width: 100%; }
      .no-print { display: none; }
    }
  </style>
</head>

<body class="bg-[#FFF8E7] text-gray-800">

  <!-- Cabecera PHP -->
  <?php 
    include("../compartido/componentes/cabecera/index.php");
    cabecera("Menú"); 
  ?>

  <!-- Navegación -->
  <nav class="fixed top-0 left-0 w-full bg-[#FFD700] text-[#8B0000] shadow z-50">
    <ul class="flex flex-wrap justify-center gap-4 py-2 font-semibold">
      <li><a href="#entradas" class="hover:underline">Entradas</a></li>
      <li><a href="#sopas" class="hover:underline">Sopas y Recados</a></li>
      <li><a href="#carnes" class="hover:underline">Carnes y Parrilladas</a></li>
      <li><a href="#mar" class="hover:underline">Del Mar</a></li>
      <li><a href="#postres" class="hover:underline">Postres</a></li>
      <li><a href="#bebidas" class="hover:underline">Bebidas</a></li>
    </ul>
  </nav>

  <?php
  $imageBasePath = "images/";
  $menu = [
      'entradas' => [
          ['name' => 'Guacamole con Nachos', 'price' => 35, 'description' => 'Aguacate fresco con tomate, cebolla y cilantro, servido con nachos crujientes.', 'image' => 'tortillas-con-guacamole.jpg'],
          ['name' => 'Ceviche de Camarón', 'price' => 50, 'description' => 'Camarones frescos marinados en limón, con cebolla y cilantro.', 'image' => 'ceviche.jpg'],
          ['name' => 'Ensalada Hacienda', 'price' => 40, 'description' => 'Lechuga, tomate, aguacate y queso fresco con aderezo de limón.', 'image' => 'ensalada.jpg']
      ],
      'sopas' => [
          ['name' => 'Pepián Tradicional', 'price' => 65, 'description' => 'Salsa espesa de semillas y chiles con pollo o res,verduras, arroz, aguacate, tortillas o tamalitos.', 'image' => 'pepian.jpg'],
          ['name' => 'Estofado Criollo', 'price' => 60, 'description' => 'Carne de res en salsa de tomate y especias, con papas y zanahorias.', 'image' => 'estofado.jpeg'],
          ['name' => 'Caldo de Gallina', 'price' => 55, 'description' => 'Sopa reconfortante de gallina con verduras y hierbas frescas.', 'image' => 'caldo.jpeg'],
          ['name' => 'Sopa de Tortilla', 'price' => 45, 'description' => 'Caldo de tomate con tiras de tortilla frita, aguacate y queso.', 'image' => 'sopatortilla.jpg']
      ],
      'carnes' => [
          ['name' => 'Puyazo Hacienda (12 oz)', 'price' => 120, 'description' => 'Corte premium de res a la parrilla, con papas y verduras.', 'image' => 'puyazo.jpg'],
          ['name' => 'Lomito (8 oz)', 'price' => 110, 'description' => 'Filete de res tierno, jugoso, con chimichurri.', 'image' => 'lomito.jpg'],
          ['name' => 'Parrillada Mixta', 'price' => 150, 'description' => 'Costilla, chorizo, longaniza y pollo asado para dos personas.', 'image' => 'parrillada.jpeg'],
          ['name' => 'Churrasco Guatemalteco', 'price' => 95, 'description' => 'Bistec de res con cebollas y pimientos, servido con arroz.', 'image' => 'bistec.jpg'],
          ['name' => 'Tacos de Carne Asada', 'price' => 70, 'description' => 'Tres tacos con carne a la parrilla, cebolla y cilantro.', 'image' => 'tacos.jpg']
      ],
      'mar' => [
          ['name' => 'Camarones a la Diabla', 'price' => 130, 'description' => 'Camarones en salsa picante de chile, con arroz y ensalada.', 'image' => 'camarones.jpg'],
          ['name' => 'Filete de Pescado a la Plancha', 'price' => 100, 'description' => 'Pescado fresco con limón, hierbas y verduras al vapor.', 'image' => 'pescado1.jpg']
      ],
      'postres' => [
          ['name' => 'Tres Leches', 'price' => 35, 'description' => 'Pastel esponjoso empapado en tres leches, con crema batida.', 'image' => 'tres-leches.jpg'],
          ['name' => 'Flan de Leche', 'price' => 30, 'description' => 'Flan cremoso con caramelo y nueces.', 'image' => 'flan.jpg']
      ],
      'bebidas' => [
          ['name' => 'Refrescos', 'price' => 15, 'description' => 'Coca-Cola, Sprite, etc.', 'image' => 'regrescos.jpeg'],
          ['name' => 'Jugo Natural de Horchata', 'price' => 20, 'description' => 'Bebida de arroz con canela y vainilla.', 'image' => 'horchata.jpg'],
          ['name' => 'Cerveza Nacional (Gallo)', 'price' => 25, 'description' => 'Botella fría.', 'image' => 'gallo.jpeg'],
          ['name' => 'Café de Antigua', 'price' => 18, 'description' => 'Café guatemalteco molido fresco.', 'image' => 'cafee.jpg']
      ]
  ];

  function renderSection($sectionId, $sectionTitle, $items, $imageBasePath) {
      echo "<section id='$sectionId' class='my-16 scroll-mt-32'>";
      echo "<h2 class='text-3xl font-bold text-[#8B0000] mb-8 text-center border-b-4 border-[#FFD700] inline-block pb-2'>$sectionTitle</h2>";
      echo "<div class='grid md:grid-cols-2 lg:grid-cols-3 gap-6'>";

      foreach ($items as $item) {
          $imagePath = $imageBasePath . $item['image'];
          echo "
          <div class='bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200 hover:shadow-2xl transition-all duration-300 flex flex-col h-full'>
              <div class='h-48 bg-gray-200 border-2 border-dashed rounded-t-xl overflow-hidden'>
                  <img src='".htmlspecialchars($imagePath)."' alt='".htmlspecialchars($item['name'])."' class='w-full h-full object-cover'>
              </div>
              <div class='p-5 flex flex-col justify-between flex-1'>
                  <div>
                      <h3 class='text-xl font-bold text-[#8B0000] mb-2 line-clamp-2'>".htmlspecialchars($item['name'])."</h3>
                      <p class='text-gray-600 text-sm mb-3 line-clamp-3'>".htmlspecialchars($item['description'])."</p>
                      <p class='text-[#FFD700] font-bold text-lg'>Q. ".number_format($item['price'], 0)."</p>
                  </div>
                  <button 
                      onclick=\"addToCart('".addslashes(htmlspecialchars($item['name']))."', ".$item['price'].")\" 
                      class='mt-4 bg-[#8B0000] text-white w-full py-3 rounded-lg hover:bg-[#A52A2A] transition font-semibold text-sm shadow-md hover:shadow-lg'>
                      Ordenar
                  </button>
              </div>
          </div>";
      }
      echo "</div></section>";
  }
  ?>

  <!-- Contenido Principal -->
  <main class="container mx-auto px-6 pt-12 pb-16">
    <?php
      renderSection('entradas', 'Entradas', $menu['entradas'], $imageBasePath);
      renderSection('sopas', 'Sopas y Recados Guatemaltecos', $menu['sopas'], $imageBasePath);
      renderSection('carnes', 'Carnes y Parrilladas', $menu['carnes'], $imageBasePath);
      renderSection('mar', 'Del Mar', $menu['mar'], $imageBasePath);
      renderSection('postres', 'Postres', $menu['postres'], $imageBasePath);
      renderSection('bebidas', 'Bebidas', $menu['bebidas'], $imageBasePath);
    ?>
  </main>

  <!-- Carrito -->
  <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
    <h3 class="font-bold text-[#8B0000] text-lg mb-4">Datos de Orden</h3>
    <ul id="cart-items" class="space-y-3"></ul>
    <div id="total" class="text-right text-xl font-bold text-[#FFD700] mt-4">Total: Q.0</div>
    <button id="checkout-btn" onclick="openModal()" class="hidden mt-4 bg-[#8B0000] text-white w-full py-3 rounded-lg hover:bg-[#A52A2A] transition font-semibold">
      Finalizar Orden
    </button>
  </div>

  <!-- Modal de Checkout -->
  <div id="checkoutModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full mx-4 relative">
      <button class="absolute top-2 right-3 text-gray-500 text-2xl hover:text-gray-700" onclick="closeModal()">x</button>
      <h2 class="text-xl font-bold text-[#8B0000] mb-4">Ingrese sus Datos</h2>
      <form onsubmit="submitOrder(event)" class="space-y-4">
        <input type="text" id="nombre" placeholder="Nombre" required class="border w-full p-3 rounded-lg">
        <input type="text" id="direccion" placeholder="Dirección (no necesaria si consume acá)" class="border w-full p-3 rounded-lg">
        <div class="flex flex-col gap-3 mt-4">
          <label class="flex items-center gap-2"><input type="radio" name="entrega" value="llevar" required> Llevar</label>
          <label class="flex items-center gap-2"><input type="radio" name="entrega" value="consumir"> Consumir acá</label>
          <label class="flex items-center gap-2"><input type="radio" name="entrega" value="domicilio"> A domicilio</label>
        </div>
        <div class="flex gap-6 mt-3">
          <label class="flex items-center gap-2"><input type="radio" name="pago" value="efectivo" required> Efectivo</label>
          <label class="flex items-center gap-2"><input type="radio" name="pago" value="tarjeta"> Tarjeta</label>
        </div>
        <div id="cantidadContainer" class="hidden mt-4">
          <label for="cantidad" class="block text-sm font-medium text-gray-700 mb-1">Cantidad en efectivo (GTQ)</label>
          <input type="number" id="cantidad" placeholder="Ej: 500" min="1" step="1" class="border w-full p-3 rounded-lg">
        </div>
        <div class="flex gap-3 mt-6">
          <button type="submit" class="flex-1 bg-[#8B0000] text-white py-3 rounded-lg hover:bg-[#A52A2A] transition font-semibold shadow-md">
            Enviar Orden
          </button>
          <button type="button" onclick="closeModal()" class="flex-1 bg-gray-200 text-gray-800 py-3 rounded-lg hover:bg-gray-300 transition font-semibold">
            Agregar más
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL DE RECIBO -->
  <div id="receiptModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 max-w-lg w-full mx-4 relative">
      <button class="absolute top-2 right-3 text-gray-500 text-2xl hover:text-gray-700 no-print" onclick="closeReceipt()">x</button>
      <div id="printable-receipt" class="text-left">
        <div class="text-center mb-6">
          <h1 class="text-2xl font-bold text-[#8B0000]">HACIENDA REAL</h1>
          <p class="text-sm">Zona 10, Ciudad de Guatemala</p>
          <p class="text-sm">Tel: 1234-5678 | NIT: 1234567-8</p>
          <p class="text-sm" id="receipt-date"></p>
          <p class="text-sm" id="receipt-id"></p>
        </div>
        <hr class="border-gray-300 mb-4">
        <div class="space-y-1 text-sm">
          <p><strong>Cliente:</strong> <span id="receipt-cliente"></span></p>
          <p><strong>Tipo:</strong> <span id="receipt-tipo"></span></p>
          <p id="receipt-direccion-line" class="hidden"><strong>Dirección:</strong> <span id="receipt-direccion"></span></p>
          <p><strong>Pago:</strong> <span id="receipt-pago"></span></p>
        </div>
        <hr class="border-gray-300 my-4">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b">
              <th class="text-left">Producto</th>
              <th class="text-center">Cant.</th>
              <th class="text-right">Precio</th>
              <th class="text-right">Total</th>
            </tr>
          </thead>
          <tbody id="receipt-items"></tbody>
        </table>
        <hr class="border-gray-300 my-4">
        <div class="text-right font-bold text-lg">
          <p>Total: Q. <span id="receipt-total"></span></p>
        </div>
        <div class="text-center mt-6 text-xs text-gray-600">
          <p>Gracias por su preferencia</p>
          <p>www.haciendareal.net</p>
        </div>
      </div>
      <div class="mt-6 text-center no-print">
        <button onclick="printReceipt()" class="bg-[#8B0000] text-white px-6 py-3 rounded-lg hover:bg-[#A52A2A] transition font-semibold">
          Imprimir Recibo
        </button>
      </div>
    </div>
  </div>

  <!-- BOTÓN VER ÓRDENES -->
  <div class="container mx-auto px-6 mt-6">
    <button onclick="openOrdersModal()" class="w-full bg-[#FFD700] text-[#8B0000] py-3 rounded-lg hover:bg-[#FFA500] transition font-bold shadow-md">
      Ver Compras realizadas
    </button>
  </div>

  <!-- MODAL DE ÓRDENES (RESTAURADO) -->
  <div id="ordersModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 overflow-y-auto">
    <div class="bg-white rounded-lg shadow-xl p-6 max-w-2xl w-full mx-4 my-8 relative max-h-screen overflow-y-auto">
      <button class="absolute top-3 right-4 text-gray-500 text-3xl hover:text-gray-700" onclick="closeOrdersModal()">x</button>
      
      <h2 class="text-2xl font-bold text-[#8B0000] mb-6 text-center">Órdenes Enviadas</h2>

      <!-- Filtros -->
      <div class="flex flex-wrap gap-3 mb-6">
        <select id="filterEntrega" onchange="filterOrders()" class="border p-2 rounded-lg">
          <option value="">Todos los tipos</option>
          <option value="llevar">Llevar</option>
          <option value="consumir">Consumir acá</option>
          <option value="domicilio">A domicilio</option>
        </select>
        <select id="filterPago" onchange="filterOrders()" class="border p-2 rounded-lg">
          <option value="">Todos los pagos</option>
          <option value="efectivo">Efectivo</option>
          <option value="tarjeta">Tarjeta</option>
        </select>
        <button onclick="clearOrders()" class="bg-[#8B0000] text-white px-5 py-3 rounded-lg hover:bg-[#A52A2A] transition text-sm font-medium shadow-md">
          Limpiar Todo
        </button>
      </div>

      <div id="ordersList" class="space-y-4"></div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="bg-[#8B0000] text-white text-center py-4">
    <p>© 2025 Hacienda Real Guatemala. Todos los derechos reservados.</p>
    <p>Visita <a href="https://haciendareal.net/" target="_blank" class="underline text-[#FFD700]">haciendareal.net</a></p>
  </footer>

  <!-- Script del carrito + recibo + órdenes -->
  <script>
    let cart = [];
    let orders = JSON.parse(localStorage.getItem('hacienda_orders')) || [];
    let currentOrder = null;

    function addToCart(name, price) {
      const existing = cart.find(item => item.name === name);
      if (existing) existing.quantity += 1;
      else cart.push({ name, price, quantity: 1 });
      updateCart();
    }

    function removeFromCart(name) {
      const index = cart.findIndex(item => item.name === name);
      if (index !== -1) {
        if (cart[index].quantity > 1) cart[index].quantity -= 1;
        else cart.splice(index, 1);
        updateCart();
      }
    }

    function updateCart() {
      const cartItems = document.getElementById('cart-items');
      const totalElement = document.getElementById('total');
      const checkoutBtn = document.getElementById('checkout-btn');
      cartItems.innerHTML = '';
      let total = 0;

      if (cart.length === 0) {
        cartItems.innerHTML = '<li class="text-center text-gray-500 py-4 italic">No hay productos</li>';
      } else {
        cart.forEach(item => {
          const subtotal = item.price * item.quantity;
          total += subtotal;
          const li = document.createElement('li');
          li.className = 'flex justify-between items-center py-2 border-b border-gray-200 last:border-b-0';
          li.innerHTML = `
            <div class="flex-1"><div class="font-medium text-gray-800">${item.name} - Q.${item.price}</div></div>
            <div class="flex items-center gap-2">
              <button onclick="removeFromCart('${item.name}')" class="w-8 h-8 bg-red-100 text-red-600 rounded-full hover:bg-red-200 text-sm font-bold">−</button>
              <span class="w-8 text-center font-medium">${item.quantity}</span>
              <button onclick="addToCart('${item.name}', ${item.price})" class="w-8 h-8 bg-green-100 text-green-600 rounded-full hover:bg-green-200 text-sm font-bold">+</button>
              <span class="text-right w-20 font-bold text-[#8B0000] ml-3">Q.${subtotal}</span>
            </div>`;
          cartItems.appendChild(li);
        });
      }
      totalElement.innerHTML = `Total: Q.${total}`;
      checkoutBtn.classList.toggle('hidden', cart.length === 0);
    }

    function openModal() { updateCart(); document.getElementById('checkoutModal').classList.remove('hidden'); }
    function closeModal() { document.getElementById('checkoutModal').classList.add('hidden'); }

    function submitOrder(event) {
      event.preventDefault();
      const nombre = document.getElementById('nombre').value.trim();
      const direccion = document.getElementById('direccion').value.trim();
      const entrega = document.querySelector('input[name="entrega"]:checked')?.value;
      const pago = document.querySelector('input[name="pago"]:checked')?.value;
      const cantidad = document.getElementById('cantidad').value;

      if (!nombre || !entrega || !pago || cart.length === 0) return;
      if (entrega === 'domicilio' && !direccion) return;
      if (pago === 'efectivo' && !cantidad) return;

      currentOrder = {
        id: Date.now(),
        date: new Date().toLocaleString('es-GT'),
        cliente: nombre,
        direccion: entrega === 'domicilio' ? direccion : '-',
        entrega,
        pago,
        efectivo: pago === 'efectivo' ? cantidad : null,
        items: JSON.parse(JSON.stringify(cart)),
        total: cart.reduce((sum, i) => sum + i.price * i.quantity, 0)
      };

      orders.unshift(currentOrder);
      localStorage.setItem('hacienda_orders', JSON.stringify(orders));
      cart = [];
      updateCart();
      closeModal();
      showReceipt(currentOrder);
      document.querySelector('form').reset();
    }

    function showReceipt(order) {
      document.getElementById('receipt-date').textContent = order.date;
      document.getElementById('receipt-id').textContent = `#${order.id.toString().slice(-6)}`;
      document.getElementById('receipt-cliente').textContent = order.cliente;
      document.getElementById('receipt-tipo').textContent = {
        'llevar': 'Llevar', 'consumir': 'Consumir acá', 'domicilio': 'A domicilio'
      }[order.entrega];
      document.getElementById('receipt-pago').textContent = order.pago === 'efectivo' ? `Efectivo (Q.${order.efectivo})` : 'Tarjeta';

      const dirLine = document.getElementById('receipt-direccion-line');
      const dirSpan = document.getElementById('receipt-direccion');
      if (order.direccion !== '-') {
        dirSpan.textContent = order.direccion;
        dirLine.classList.remove('hidden');
      } else {
        dirLine.classList.add('hidden');
      }

      const itemsBody = document.getElementById('receipt-items');
      itemsBody.innerHTML = '';
      order.items.forEach(item => {
        const tr = document.createElement('tr');
        tr.className = 'border-b';
        tr.innerHTML = `
          <td class="py-1">${item.name}</td>
          <td class="text-center">${item.quantity}</td>
          <td class="text-right">Q.${item.price}</td>
          <td class="text-right">Q.${item.price * item.quantity}</td>
        `;
        itemsBody.appendChild(tr);
      });

      document.getElementById('receipt-total').textContent = order.total;
      document.getElementById('receiptModal').classList.remove('hidden');
    }

    function closeReceipt() {
      document.getElementById('receiptModal').classList.add('hidden');
    }

    function printReceipt() {
      window.print();
    }

    // === MODAL DE ÓRDENES (CORREGIDO) ===
    function openOrdersModal() {
      renderOrders(); // ← AHORA SÍ SE LLAMA
      document.getElementById('ordersModal').classList.remove('hidden');
    }

    function closeOrdersModal() {
      document.getElementById('ordersModal').classList.add('hidden');
    }

    function renderOrders() {
      const list = document.getElementById('ordersList');
      const filterEntrega = document.getElementById('filterEntrega').value;
      const filterPago = document.getElementById('filterPago').value;

      const filtered = orders.filter(order => {
        if (filterEntrega && order.entrega !== filterEntrega) return false;
        if (filterPago && order.pago !== filterPago) return false;
        return true;
      });

      list.innerHTML = '';
      if (filtered.length === 0) {
        list.innerHTML = '<p class="text-center text-gray-500 py-8 text-lg">No hay órdenes</p>';
        return;
      }

      filtered.forEach(order => {
        const div = document.createElement('div');
        div.className = 'bg-gradient-to-r from-gray-50 to-gray-100 p-5 rounded-xl border border-gray-300 shadow-sm';
        let itemsHtml = '';
        order.items.forEach(item => {
          itemsHtml += `<div class="text-sm text-gray-700">• ${item.name} x${item.quantity} → Q.${item.price * item.quantity}</div>`;
        });

        const tipoEntrega = { 'llevar': 'Llevar', 'consumir': 'Consumir acá', 'domicilio': 'A domicilio' }[order.entrega];
        const tipoPago = order.pago === 'efectivo' ? `Efectivo (Q.${order.efectivo})` : 'Tarjeta';

        div.innerHTML = `
          <div class="flex justify-between items-start mb-3">
            <div>
              <div class="font-bold text-[#8B0000] text-lg">#${order.id.toString().slice(-4)}</div>
              <div class="text-sm text-gray-600">${order.date}</div>
            </div>
            <div class="text-right">
              <div class="font-bold text-xl text-[#8B0000]">Q.${order.total}</div>
            </div>
          </div>
          <div class="space-y-1 text-sm">
            <div><strong>Cliente:</strong> ${order.cliente}</div>
            <div><strong>Tipo:</strong> ${tipoEntrega}</div>
            <div><strong>Pago:</strong> ${tipoPago}</div>
            ${order.direccion !== '-' ? `<div><strong>Dirección:</strong> ${order.direccion}</div>` : ''}
          </div>
          <div class="mt-3">${itemsHtml}</div>
        `;
        list.appendChild(div);
      });
    }

    function filterOrders() { renderOrders(); }
    function clearOrders() {
      if (confirm('¿Eliminar todas las órdenes?')) {
        orders = [];
        localStorage.removeItem('hacienda_orders');
        renderOrders();
      }
    }

    // === CONTROLES DINÁMICOS ===
    document.addEventListener('DOMContentLoaded', function () {
      const entregaRadios = document.querySelectorAll('input[name="entrega"]');
      const pagoRadios = document.querySelectorAll('input[name="pago"]');
      const direccionInput = document.getElementById('direccion');
      const cantidadContainer = document.getElementById('cantidadContainer');
      const cantidadInput = document.getElementById('cantidad');

      function actualizarDireccion() {
        const seleccion = document.querySelector('input[name="entrega"]:checked')?.value;
        if (seleccion === 'domicilio') {
          direccionInput.required = true;
          direccionInput.placeholder = "Dirección (obligatoria para A domicilio)";
          direccionInput.classList.remove('opacity-50');
        } else {
          direccionInput.required = false;
          direccionInput.placeholder = "Dirección (no necesaria)";
          direccionInput.classList.add('opacity-50');
          if (seleccion !== 'domicilio') direccionInput.value = '';
        }
      }

      function actualizarCantidad() {
        const pago = document.querySelector('input[name="pago"]:checked')?.value;
        if (pago === 'efectivo') {
          cantidadContainer.classList.remove('hidden');
          cantidadInput.required = true;
        } else {
          cantidadContainer.classList.add('hidden');
          cantidadInput.required = false;
          cantidadInput.value = '';
        }
      }

      entregaRadios.forEach(r => r.addEventListener('change', actualizarDireccion));
      pagoRadios.forEach(r => r.addEventListener('change', actualizarCantidad));
      actualizarDireccion();
      actualizarCantidad();

      // Preseleccionar A domicilio si viene del dashboard
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.get('tipo') === 'domicilio') {
        const radioDomicilio = document.querySelector('input[name="entrega"][value="domicilio"]');
        if (radioDomicilio) {
          radioDomicilio.checked = true;
          direccionInput.required = true;
          direccionInput.placeholder = "Dirección (obligatoria para A domicilio)";
          direccionInput.classList.remove('opacity-50');
        }
      }
    });

    updateCart();
  </script>
</body>
</html>