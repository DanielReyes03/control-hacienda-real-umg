let cart = [];

function addToCart(name, price) {
  cart.push({ name, price });
  updateCartCount();
}

function updateCartCount() {
  document.getElementById("cartBtn").innerText = `🛒 Carrito (${cart.length})`;
}

function openCart() {
  const cartItemsContainer = document.getElementById("cartItems");

  // Mostrar cada item con botón "X" para eliminar
  cartItemsContainer.innerHTML = cart
    .map((item, index) => `
      <li>
        ${item.name} - Q${item.price} 
        <button onclick="removeFromCart(${index})" class="remove-btn">X</button>
      </li>
    `)
    .join('');

  const total = cart.reduce((sum, item) => sum + item.price, 0);
  document.getElementById("cartTotal").innerText = `Total: Q${total}`;
  document.getElementById("cartModal").classList.remove("hidden");
}

function closeCart() {
  document.getElementById("cartModal").classList.add("hidden");
}

function checkout() {
  if(cart.length === 0){
    alert("🛒 Tu carrito está vacío.");
    return;
  }
  alert("✅ Pedido confirmado. ¡Gracias por tu compra!");
  cart = [];
  updateCartCount();
  closeCart();
}

// Función para eliminar item del carrito
function removeFromCart(index) {
  cart.splice(index, 1); // Elimina el item del array
  updateCartCount();
  openCart(); // Actualiza la vista del carrito
}

// Eventos
document.getElementById("cartBtn").addEventListener("click", openCart);
document.getElementById("closeCartBtn").addEventListener("click", closeCart);
document.getElementById("checkoutBtn").addEventListener("click", checkout);
