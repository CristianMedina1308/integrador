<?php include 'header.php'; ?>

<div class="container py-5">
  <h1 class="text-center mb-4">🛍️ Tu Carrito de Compras</h1>

  <div class="card shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered align-middle text-center" id="tabla-carrito">
          <thead class="table-light">
            <tr>
              <th>Producto</th>
              <th>Precio</th>
              <th>Cantidad</th>
              <th>Subtotal</th>
              <th>Eliminar</th>
            </tr>
          </thead>
          <tbody>
            <!-- Se genera con JavaScript -->
          </tbody>
        </table>
      </div>

      <div class="d-flex justify-content-between align-items-center mt-4">
        <h4 class="mb-0">Total a pagar: $<span id="total-carrito">0</span></h4>
        <a href="checkout.php" class="btn btn-primary">Finalizar compra</a>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const carrito = JSON.parse(localStorage.getItem("carrito")) || [];
  const tabla = document.querySelector("#tabla-carrito tbody");
  const totalSpan = document.getElementById("total-carrito");

  let total = 0;
  tabla.innerHTML = "";

  if (carrito.length === 0) {
    tabla.innerHTML = '<tr><td colspan="5" class="text-center py-4">Tu carrito está vacío.</td></tr>';
  } else {
    carrito.forEach((item, index) => {
      const cantidad = item.cantidad ?? 1;
      const subtotal = item.precio * cantidad;
      total += subtotal;

      const fila = document.createElement("tr");
      fila.innerHTML = `
        <td>${item.nombre}</td>
        <td>$${item.precio.toLocaleString()}</td>
        <td>${cantidad}</td>
        <td>$${subtotal.toLocaleString()}</td>
        <td>
          <button class="btn btn-danger btn-sm" onclick="eliminarDelCarrito(${index})">
            <i class="fas fa-trash-alt"></i>
          </button>
        </td>
      `;
      tabla.appendChild(fila);
    });
  }

  totalSpan.textContent = total.toLocaleString();
  actualizarContadorCarrito();
});

function eliminarDelCarrito(index) {
  let carrito = JSON.parse(localStorage.getItem("carrito")) || [];
  carrito.splice(index, 1);
  localStorage.setItem("carrito", JSON.stringify(carrito));
  location.reload();
}

function actualizarContadorCarrito() {
  const carrito = JSON.parse(localStorage.getItem("carrito")) || [];
  document.getElementById("contador-carrito").textContent = carrito.length;
}
</script>

<?php include 'footer.php'; ?>
