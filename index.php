<?php
session_start();
include 'conexion.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $contraseña = trim($_POST['contraseña']);
    $rol = $_POST['rol'];

    if (empty($email)) {
        $mensaje = "El email is obligatorio.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "El email no es válido.";
    } elseif (empty($contraseña)) {
        $mensaje = "La contraseña es obligatoria.";
    } elseif (empty($rol)) {
        $mensaje = "Debe seleccionar un rol.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ? AND rol = ?");
        $stmt->bind_param("ss", $email, $rol);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 1) {
            $usuario = $res->fetch_assoc();
            if (password_verify($contraseña, $usuario['contraseña'])) {
                
         
                // Verificar si la cuenta ha sido dada de baja
                if (isset($usuario['eliminado']) && $usuario['eliminado'] == 1) {
                    $motivo_baja = !empty($usuario['motivo_eliminacion']) ? $usuario['motivo_eliminacion'] : 'Baja administrativa por parte de la academia.';
                    // Recarga la página indicando el bloqueo y pasando el motivo por URL
                    header("Location: index.php?error=cuenta_eliminada&motivo=" . urlencode($motivo_baja));
                    exit;
                }

                $_SESSION['id'] = $usuario['id'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['rol'] = $usuario['rol'];
                $_SESSION['cedula'] = $usuario['cedula'] ?? null;

                // Guardar el mensaje de éxito 
                $_SESSION['login_exito'] = "¡Bienvenido/a de nuevo, " . htmlspecialchars($usuario['nombre']) . "!";

                // Redirecciones de seguridad según el rol (Admin, Profesor o Alumno)
                if ($rol === 'admin') {
                    header("Location: panel_admin.php");
                } elseif ($rol === 'profesor') {
                    header("Location: panel_profesor.php");
                } else {
                    header("Location: panel_alumno.php");
                }
                exit;
            } else {
                $mensaje = "Contraseña incorrecta.";
            }
        } else {
            $mensaje = "Usuario no encontrado con ese rol.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - Biblioteca Danza</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="css/estilos_login.css">
    <style>
        .toggle-password {
            cursor: pointer;
            border-left: none;
            background-color: #fff;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #dee2e6;
        }
    </style>
</head>
<body>

<div class="form-login">
    
    <?php if (isset($_GET['error']) && $_GET['error'] === 'cuenta_eliminada'): ?>
        <div class="alert alert-danger p-3 shadow-sm mb-4 text-start" style="border-radius: 12px; border-left: 5px solid #dc3545;">
            <div class="d-flex align-items-center mb-1 text-danger fw-bold">
                <i class="fa-solid fa-user-slash me-2" style="font-size: 1.1rem;"></i>
                Acceso Restringido
            </div>
            <p class="mb-2 small text-muted">Tu cuenta ya no se encuentra activa en el portal de la academia.</p>
            <div class="p-2 bg-light rounded border border-danger-subtle small text-dark" style="font-style: italic;">
                <strong>Motivo informado:</strong> "<?= htmlspecialchars($_GET['motivo']) ?>"
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['logout_exito'])): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-3" role="alert" style="border-radius: 10px;">
            <i class="fa-solid fa-circle-check me-2"></i> <?= $_SESSION['logout_exito'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['logout_exito']); ?>
    <?php endif; ?>

    <h2 class="text-center">Iniciar Sesión</h2>

    <?php if($mensaje != ''): ?>
        <div class="alert alert-danger" style="border-radius: 10px;"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <form action="" method="post">
        <div class="mb-3">
            <label>Email:</label>
            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label>Contraseña:</label>
            <div class="input-group">
                <input type="password" class="form-control border-end-0" name="contraseña" id="passwordInput" required>
                <span class="input-group-text toggle-password border-start-0" id="togglePassword">
                    <i class="fa-solid fa-eye" id="eyeIcon"></i>
                </span>
            </div>
        </div>

        <div class="mb-3">
            <label>Rol:</label>
            <select name="rol" class="form-select" required>
                <option value="">Selecciona tu rol</option>
                <option value="profesor" <?= (($_POST['rol'] ?? '') === 'profesor') ? 'selected' : '' ?>>Profesor</option>
                <option value="alumno" <?= (($_POST['rol'] ?? '') === 'alumno') ? 'selected' : '' ?>>Alumno</option>
                <option value="admin" <?= (($_POST['rol'] ?? '') === 'admin') ? 'selected' : '' ?>>Administrador</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary w-100">Iniciar sesión</button>
    </form>

    <p class="mt-3 text-center">
        ¿No tienes una cuenta? <a href="registro.php">Registrarse</a>
    </p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const togglePassword = document.querySelector('#togglePassword');
    const passwordInput = document.querySelector('#passwordInput');
    const eyeIcon = document.querySelector('#eyeIcon');

    togglePassword.addEventListener('click', function () {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        eyeIcon.classList.toggle('fa-eye');
        eyeIcon.classList.toggle('fa-eye-slash');
    });
</script>

</body>
</html>




