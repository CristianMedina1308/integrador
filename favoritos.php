<?php include 'header.php'; ?>

<main class="container py-5">
  <h1 class="text-center mb-5">💖 Mis Favoritos</h1>
  <div id="contenedor-favoritos" class="row g-4 justify-content-center">
    <!-- Productos favoritos se insertan con JavaScript -->
  </div>
</main>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const favoritos = JSON.parse(localStorage.getItem("favoritos")) || [];
  const contenedor = document.getElementById("contenedor-favoritos");

  if (favoritos.length === 0) {
    contenedor.innerHTML = `
      <div class="text-center text-muted">
        <i class="bi bi-heart-slash display-4"></i>
        <p class="mt-2">No tienes productos en tu lista de deseos.</p>
      </div>`;
    return;
  }

  fetch("api_favoritos.php?ids=" + favoritos.join(","))
    .then(res => res.json())
    .then(productos => {
      if (!productos.length) {
        contenedor.innerHTML = `
          <div class="text-center text-muted">
            <p>No se encontraron productos en la base de datos.</p>
          </div>`;
        return;
      }

      productos.forEach(p => {
        const col = document.createElement("div");
        col.className = "col-12 col-sm-6 col-md-4 col-lg-3";
        col.innerHTML = `
          <div class="card h-100 shadow-sm">
            <img src="assets/img/productos/${p.imagen}" class="card-img-top" alt="${p.nombre}">
            <div class="card-body text-center d-flex flex-column">
              <h5 class="card-title">${p.nombre}</h5>
              <p class="text-danger fw-bold mb-3">$${Number(p.precio).toLocaleString()}</p>
              <div class="mt-auto d-flex flex-column gap-2">
                <button class="btn btn-outline-primary"
                        onclick="agregarCarrito('${p.nombre}', ${p.precio}, ${p.id})">
                  <i class="bi bi-cart-plus"></i> Agregar al carrito
                </button>
                <button class="btn btn-outline-danger"
                        onclick="toggleFavorito(${p.id}); this.closest('.col-12').remove();">
                  <i class="bi bi-x-circle"></i> Quitar
                </button>
              </div>
            </div>
          </div>`;
        contenedor.appendChild(col);
      });
    })
    .catch(() => {
      contenedor.innerHTML = `
        <div class="text-center text-danger">
          <p>Ocurrió un error al cargar tus favoritos.</p>
        </div>`;
    });
});

function agregarCarrito(nombre, precio, id) {
  let carrito = JSON.parse(localStorage.getItem("carrito")) || [];
  const existe = carrito.find(p => p.id === id);
  if (existe) {
    existe.cantidad++;
  } else {
    carrito.push({ id, nombre, precio, cantidad: 1 });
  }
  localStorage.setItem("carrito", JSON.stringify(carrito));
  alert("✅ Producto agregado al carrito");
}

function toggleFavorito(id) {
  let favs = JSON.parse(localStorage.getItem("favoritos")) || [];
  const idx = favs.indexOf(id);
  if (idx !== -1) {
    favs.splice(idx, 1);
    localStorage.setItem("favoritos", JSON.stringify(favs));
    alert("❌ Producto eliminado de favoritos");
  }
}
</script>

<?php include 'footer.php'; ?>
