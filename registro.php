<?php
include 'includes/conexion.php';
session_start();
include 'header.php';
?>

<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow">
        <div class="card-header bg-success text-white text-center">
          <h3 class="mb-0">Crear cuenta</h3>
        </div>
        <div class="card-body">
          <form method="post">
            <div class="mb-3">
              <label for="nombre" class="form-label">Nombre completo</label>
              <input type="text" name="nombre" id="nombre" class="form-control" required>
            </div>

            <div class="mb-3">
              <label for="telefono" class="form-label">Número de teléfono</label>
              <input type="text" name="telefono" id="telefono" class="form-control" required pattern="[0-9+ ]{7,20}" title="Ingresa un número de teléfono válido">
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">Correo electrónico</label>
              <input type="email" name="email" id="email" class="form-control" required>
            </div>

            <div class="mb-3">
              <label for="password" class="form-label">Contraseña</label>
              <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <button type="submit" name="registro" class="btn btn-success w-100">Registrarse</button>

            <div class="text-center mt-3">
              ¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a>
            </div>
          </form>

          <?php
          if (isset($_POST['registro'])) {
            $nombre = trim($_POST['nombre']);
            $telefono = trim($_POST['telefono']);
            $email = trim($_POST['email']);
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

            $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->rowCount() > 0) {
              echo "<div class='alert alert-danger mt-3 text-center'>❌ Ese correo ya está registrado.</div>";
            } else {
              $stmt = $conn->prepare("INSERT INTO usuarios (nombre, telefono, email, password, rol) VALUES (?, ?, ?, ?, 'cliente')");
              $stmt->execute([$nombre, $telefono, $email, $password]);
              echo "<div class='alert alert-success mt-3 text-center'>✅ ¡Registro exitoso! <a href='login.php'>Inicia sesión aquí</a>.</div>";
            }
          }
          ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>
