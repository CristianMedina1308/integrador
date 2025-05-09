<?php include 'header.php'; ?>

<main class="container py-5">
  <h1 class="text-center mb-5">📬 Contáctanos</h1>
  <div class="row g-5">

    <!-- FORMULARIO -->
    <div class="col-md-6">
      <div class="card shadow">
        <div class="card-body">
          <h5 class="card-title mb-4">Envíanos un mensaje</h5>
          <form method="post">
            <div class="mb-3">
              <label for="nombre" class="form-label">Nombre completo</label>
              <input type="text" name="nombre" id="nombre" class="form-control" required>
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">Correo electrónico</label>
              <input type="email" name="email" id="email" class="form-control" required>
            </div>

            <div class="mb-3">
              <label for="mensaje" class="form-label">Mensaje</label>
              <textarea name="mensaje" id="mensaje" rows="5" class="form-control" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary w-100">Enviar mensaje</button>
          </form>

          <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
            <div class="alert alert-success mt-4">
              ✅ Gracias por tu mensaje, <strong><?= htmlspecialchars($_POST['nombre']) ?></strong>.
              Te responderemos pronto a <strong><?= htmlspecialchars($_POST['email']) ?></strong>.
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- INFORMACIÓN / REDES / MAPA -->
    <div class="col-md-6">
      <div class="card shadow mb-4">
        <div class="card-body">
          <h5 class="card-title">Información de contacto</h5>
          <p><strong>Email:</strong> contacto@mitienda.com</p>
          <p><strong>WhatsApp:</strong> +57 302 334 1713</p>
          <p><strong>Ubicación:</strong> Bogotá, Colombia</p>
        </div>
      </div>

      <div class="card shadow mb-4">
        <div class="card-body">
          <h5 class="card-title mb-3">Síguenos</h5>
          <div class="d-flex gap-3 fs-4">
            <a href="https://www.instagram.com/" target="_blank" class="text-danger"><i class="fab fa-instagram"></i></a>
            <a href="https://wa.me/573023341713" target="_blank" class="text-success"><i class="fab fa-whatsapp"></i></a>
            <a href="https://www.facebook.com/" target="_blank" class="text-primary"><i class="fab fa-facebook"></i></a>
          </div>
        </div>
      </div>

      <div class="ratio ratio-16x9 rounded shadow">
        <iframe
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3976.767305509528!2d-74.081754!3d4.609710000000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e3f99c6f4c4b82f%3A0x1c915ed2c84fd96!2sBogot%C3%A1%2C%20Colombia!5e0!3m2!1ses!2sco!4v1687909608000"
          width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
      </div>
    </div>
  </div>
</main>

<?php include 'footer.php'; ?>

<!-- Font Awesome -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
