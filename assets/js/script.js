// ===========================
// CARRITO
// ===========================

function agregarCarrito(nombre, precio, id) {
  if (!id || isNaN(id)) {
    alert("Error: El producto no tiene un ID válido.");
    return;
  }

  let carrito = JSON.parse(localStorage.getItem("carrito")) || [];
  let producto = carrito.find(p => p.id === id);

  if (producto) {
    producto.cantidad += 1;
  } else {
    carrito.push({ id, nombre, precio, cantidad: 1 });
  }

  localStorage.setItem("carrito", JSON.stringify(carrito));
  actualizarContadorCarrito();
  mostrarMiniCarrito();
  alert("✅ Producto agregado al carrito");
}

function actualizarContadorCarrito() {
  const carrito = JSON.parse(localStorage.getItem("carrito")) || [];
  const total = carrito.reduce((acc, item) => acc + (item.cantidad ?? 1), 0);
  const contador = document.getElementById("contador-carrito");
  if (contador) {
    contador.innerText = total;
    contador.style.display = total > 0 ? "inline-block" : "none";
  }
}

function toggleMiniCarrito() {
  const mini = document.getElementById("mini-carrito");
  if (mini.style.display === "none" || mini.style.display === "") {
    mini.style.display = "block";
  } else {
    mini.style.display = "none";
  }
}

function mostrarMiniCarrito() {
  const carrito = JSON.parse(localStorage.getItem("carrito")) || [];
  const lista = document.getElementById("lista-mini-carrito");
  if (!lista) return;
  lista.innerHTML = "";

  if (carrito.length === 0) {
    lista.innerHTML = "<li>Tu carrito está vacío.</li>";
    return;
  }

  carrito.forEach(item => {
    const li = document.createElement("li");
    li.innerHTML = `
      <span>${item.nombre} x${item.cantidad ?? 1}</span>
      <strong class="float-end">$${(item.precio * item.cantidad).toLocaleString()}</strong>
    `;
    lista.appendChild(li);
  });
}

// ===========================
// FAVORITOS
// ===========================

function toggleFavorito(id) {
  let favoritos = JSON.parse(localStorage.getItem("favoritos")) || [];
  id = Number(id);
  const idx = favoritos.indexOf(id);

  if (idx !== -1) {
    favoritos.splice(idx, 1);
    alert("❌ Eliminado de favoritos");
  } else {
    favoritos.push(id);
    alert("💖 Agregado a favoritos");
  }

  localStorage.setItem("favoritos", JSON.stringify(favoritos));
  updateFavoriteIcons();
}

function updateFavoriteIcons() {
  const favoritos = JSON.parse(localStorage.getItem("favoritos")) || [];
  document.querySelectorAll("[data-fav-id]").forEach(btn => {
    const id = Number(btn.getAttribute("data-fav-id"));
    const icon = btn.querySelector("i");
    if (!icon) return;

    if (favoritos.includes(id)) {
      icon.classList.remove("bi-heart");
      icon.classList.add("bi-heart-fill", "text-danger");
    } else {
      icon.classList.remove("bi-heart-fill", "text-danger");
      icon.classList.add("bi-heart");
    }
  });
}

// ===========================
// INICIALIZACIÓN
// ===========================

document.addEventListener("DOMContentLoaded", () => {
  mostrarMiniCarrito();
  actualizarContadorCarrito();
  updateFavoriteIcons();

  document.addEventListener("click", function (e) {
    const mini = document.getElementById("mini-carrito");
    const icono = document.querySelector(".bi-bag-fill");
    if (mini && !mini.contains(e.target) && icono && !icono.contains(e.target)) {
      mini.style.display = "none";
    }
  });
});
