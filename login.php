<?php
include 'includes/conexion.php';
session_start();
include 'header.php';
?>

<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow">
        <div class="card-header bg-primary text-white text-center">
          <h3 class="mb-0">Iniciar Sesión</h3>
        </div>
        <div class="card-body">
          <form method="post" autocomplete="off">
            <div class="mb-3">
              <label for="email" class="form-label">Correo electrónico</label>
              <input type="email" name="email" id="email" class="form-control" required>
            </div>

            <div class="mb-3">
              <label for="password" class="form-label">Contraseña</label>
              <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <button type="submit" name="login" class="btn btn-primary w-100">Ingresar</button>

            <div class="text-center mt-3">
              ¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a> <br>
              <a href="recuperar.php" class="d-block mt-2 text-decoration-underline text-primary">¿Olvidaste tu contraseña?</a>
            </div>
          </form>

          <?php
          if (isset($_POST['login'])) {
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
              $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
              $stmt->execute([$email]);
              $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

              if ($usuario && password_verify($password, $usuario['password'])) {
                $_SESSION['usuario'] = $usuario;
                $redirect = ($usuario['rol'] === 'admin') ? 'admin/index.php' : 'index.php';
                echo "<script>window.location.href='$redirect';</script>";
                exit;
              } else {
                echo "<div class='alert alert-danger mt-3 text-center'>❌ Correo o contraseña incorrectos.</div>";
              }
            } else {
              echo "<div class='alert alert-warning mt-3 text-center'>❗ Ingresa un correo electrónico válido.</div>";
            }
          }
          ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>
