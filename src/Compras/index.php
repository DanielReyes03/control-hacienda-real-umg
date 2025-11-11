<?php
require_once "../login/check_adminGer.php"; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Menú y Ventas</title>
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-gray-50 text-gray-800">

  <header class="bg-yellow-600 text-white p-4 text-center font-bold text-2xl">
    Sistema de Ventas - Pollo Campero
  </header>

  <main class="p-4 max-w-6xl mx-auto">
    <h2 class="text-xl font-semibold mb-4">Menú de Productos</h2>

    <!-- LISTADO DE PRODUCTOS -->
    <div id="productList" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

      <div class="border rounded-lg shadow p-4 bg-white">
        <h3 class="text-lg font-semibold">Combo Campero</h3>
        <p>Pollo, papas y bebida</p>
        <p class="font-bold text-green-600">Q40.00</p>
        <button onclick="addToCart('Combo Campero', 40)" class="mt-2 bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded">Agregar</button>
      </div>

      <div class="border rounded-lg shadow p-4 bg-white">
        <h3 class="text-lg font-semibold">Pollo Individual</h3>
        <p>1 pieza + acompañamiento</p>
        <p class="font-bold text-green-600">Q25.00</p>
        <button onclick="addToCart('Pollo Individual', 25)" class="mt-2 bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded">Agregar</button>
      </div>

      <div class="border rounded-lg shadow p-4 bg-white">
        <h3 class="text-lg font-semibold">Familiar</h3>
        <p>8 piezas + papas grandes</p>
        <p class="font-bold text-green-600">Q120.00</p>
        <button onclick="addToCart('Familiar', 120)" class="mt-2 bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded">Agregar</button>
      </div>
    </div>

    <!-- CARRITO -->
    <section class="mt-8">
      <h2 class="text-lg font-semibold">Carrito</h2>
      <div id="cartList" class="bg-white rounded shadow p-4 min-h-[100px]"></div>
      <p class="text-right mt-2 font-bold text-xl">Total: Q<span id="cartTotal">0.00</span></p>
      <button onclick="openModal()" class="mt-4 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Finalizar Pedido</button>
    </section>
  </main>

  <!-- MODAL DE PEDIDO -->
  <div id="modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
    <div class="bg-white p-6 rounded shadow-lg w-96">
      <h3 class="text-xl font-semibold mb-4">Datos del Pedido</h3>
      <form onsubmit="submitOrder(event)">
        <label class="block mb-2">Nombre del Cliente</label>
        <input id="nombre" type="text" class="border rounded w-full p-2 mb-3" required>

        <label class="block mb-2">Dirección (opcional)</label>
        <input id="direccion" type="text" class="border rounded w-full p-2 mb-3">

        <label class="block mb-2">Tipo de Entrega:</label>
        <label><input type="radio" name="entrega" value="local" required> Local</label>
        <label class="ml-4"><input type="radio" name="entrega" value="domicilio"> Domicilio</label>

        <label class="block mt-3 mb-2">Método de Pago:</label>
        <label><input type="radio" name="pago" value="efectivo" required> Efectivo</label>
        <label class="ml-4"><input type="radio" name="pago" value="tarjeta"> Tarjeta</label>

        <div class="mt-3">
          <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded w-full">Generar Factura</button>
        </div>
        <div class="mt-2">
          <button type="button" onclick="closeModal()" class="text-gray-500 w-full">Cancelar</button>
        </div>
      </form>
    </div>
  </div>

  <!-- RECIBO -->
  <div id="receipt" class="hidden fixed inset-0 bg-white p-8 overflow-y-auto">
    <h2 class="text-2xl font-bold mb-4 text-center">Factura</h2>
    <div id="receiptContent"></div>
    <div class="text-center mt-4">
      <button onclick="window.print()" class="bg-green-600 text-white px-4 py-2 rounded">Imprimir</button>
      <button onclick="closeReceipt()" class="ml-2 text-gray-500">Cerrar</button>
    </div>
  </div>

  <script>
    let cart = [];
    let orders = JSON.parse(localStorage.getItem('hacienda_orders') || '[]');

    function addToCart(name, price) {
      const existing = cart.find(p => p.name === name);
      if (existing) {
        existing.quantity++;
      } else {
        cart.push({ name, price, quantity: 1 });
      }
      updateCart();
    }

    function updateCart() {
      const cartList = document.getElementById('cartList');
      const cartTotal = document.getElementById('cartTotal');
      cartList.innerHTML = '';
      let total = 0;

      cart.forEach((item) => {
        total += item.price * item.quantity;
        const div = document.createElement('div');
        div.classList.add('flex', 'justify-between', 'border-b', 'py-2');
        div.innerHTML = `
          <span>${item.name} (x${item.quantity})</span>
          <span>Q${(item.price * item.quantity).toFixed(2)}</span>
        `;
        cartList.appendChild(div);
      });

      cartTotal.textContent = total.toFixed(2);
    }

    function openModal() {
      document.getElementById('modal').classList.remove('hidden');
    }

    function closeModal() {
      document.getElementById('modal').classList.add('hidden');
    }

    async function submitOrder(event) {
      event.preventDefault();

      const nombre = document.getElementById('nombre').value.trim();
      const direccion = document.getElementById('direccion').value.trim();
      const entrega = document.querySelector('input[name="entrega"]:checked')?.value;
      const pago = document.querySelector('input[name="pago"]:checked')?.value;

      if (!nombre || !entrega || !pago || cart.length === 0) {
        alert('Por favor completa todos los campos y agrega al menos un producto.');
        return;
      }

      const order = {
        cliente: nombre,
        direccion: entrega === 'domicilio' ? direccion : '-',
        entrega,
        pago,
        items: JSON.parse(JSON.stringify(cart)),
        total: cart.reduce((sum, i) => sum + i.price * i.quantity, 0)
      };
            // Guardar en base de datos (API)
  fetch("./api_guardar_venta.php", {

    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      cliente: currentOrder.cliente,
      direccion: currentOrder.direccion,
      entrega: currentOrder.entrega,
      pago: currentOrder.pago,
      total: currentOrder.total,
      items: currentOrder.items
    })
  })
  .then(r => r.json())
  .then(data => {
      console.log("Venta guardada:", data);
  })
  .catch(err => console.error("Error enviando venta:", err));


      try {
        // ✅ Ruta corregida (mismo nivel que index.php)
        const res = await fetch('./api_registrar_ventas.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(order)
        });

        // Si el PHP devuelve error HTML, esto lanza excepción
        const text = await res.text();
        let data;
        try {
          data = JSON.parse(text);
        } catch {
          throw new Error("El servidor no devolvió JSON válido:\n" + text);
        }

        if (!data.success) throw new Error(data.error || 'Error al registrar la venta');

        order.id = data.id_venta;
        order.date = new Date().toLocaleString('es-GT');
        orders.unshift(order);
        localStorage.setItem('hacienda_orders', JSON.stringify(orders));

        cart = [];
        updateCart();
        closeModal();
        showReceipt(order);
      } catch (error) {
        alert('⚠️ No se pudo guardar la venta: ' + error.message);
      }
    }


    function showReceipt(order) {
      const receipt = document.getElementById('receipt');
      const content = document.getElementById('receiptContent');
      content.innerHTML = `
        <p><strong>Cliente:</strong> ${order.cliente}</p>
        <p><strong>Entrega:</strong> ${order.entrega}</p>
        <p><strong>Pago:</strong> ${order.pago}</p>
        <hr class="my-2">
        <ul>${order.items.map(i => `<li>${i.name} x${i.quantity} - Q${(i.price * i.quantity).toFixed(2)}</li>`).join('')}</ul>
        <hr class="my-2">
        <p class="text-right font-bold text-lg">Total: Q${order.total.toFixed(2)}</p>
      `;
      receipt.classList.remove('hidden');
    }

    function closeReceipt() {
      document.getElementById('receipt').classList.add('hidden');
    }
  </script>
</body>
</html>
