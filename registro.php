<?php
session_start();
include 'conexion.php';

$mensaje = '';
$registro_exitoso = false; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $contraseña = password_hash($_POST['contraseña'], PASSWORD_DEFAULT);
    $rol = $_POST['rol'];

    // 1. Verificar si el email ya existe
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $mensaje = "El email ya está registrado.";
    } else {
        // Control de Administrador Único
        if ($rol === 'admin') {
            $check_admin = $conn->query("SELECT id FROM usuarios WHERE rol = 'admin' LIMIT 1");
            if ($check_admin->num_rows > 0) {
                $mensaje = "Ya existe un administrador registrado en el sistema. No se permiten cuentas duplicadas.";
            }
        }

        // Si no hubo ningún error con el administrador, procedemos a guardar
        if (empty($mensaje)) {
            $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, contraseña, rol) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nombre, $email, $contraseña, $rol);
            
            if ($stmt->execute()) {
                $stmt->close();
                $registro_exitoso = true; 
            } else {
                $mensaje = "Error al registrar usuario.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background-color: #fcfaff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; 
            margin: 0;
            overflow: hidden; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .registro-container {
            background-color: #fff;
            padding: 30px 35px;
            border-radius: 25px;
            box-shadow: 0 10px 30px rgba(111, 45, 168, 0.05);
            width: 100%;
            max-width: 440px;
        }

        .form-control, .form-select {
            border: none;
            background-color: #f8f9fa;
            padding: 10px 15px; 
            border-radius: 12px;
            font-size: 0.95rem;
        }

        .form-control:focus, .form-select:focus {
            background-color: #fff !important;
            box-shadow: 0 0 0 0.25rem rgba(111, 45, 168, 0.1);
            border: 1px solid #6f2da8 !important;
        }

        /* Ajuste específico para que el botón del ojo encaje con el fondo gris */
        .btn-toggle-password {
            background-color: #f8f9fa;
            border: none;
            color: #6c757d;
            border-top-right-radius: 12px !important;
            border-bottom-right-radius: 12px !important;
            padding-right: 15px;
            transition: color 0.2s;
        }
        
        .btn-toggle-password:hover {
            color: #6f2da8;
            background-color: #f8f9fa;
        }

        .input-group .form-control {
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }

        .btn-primary-custom {
            background: #6f2da8;
            border: none;
            border-radius: 15px;
            font-weight: bold;
            padding: 12px;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-primary-custom:hover {
            background: #5a208c;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(111, 45, 168, 0.2);
            color: white;
        }

        .btn-outline-custom {
            border: 1px solid #dee2e6;
            border-radius: 12px;
            font-weight: 500;
            padding: 10px;
            color: #6c757d;
            background: transparent;
            transition: all 0.2s ease;
        }

        .btn-outline-custom:hover {
            background: #f8f9fa;
            color: #495057;
            border-color: #ced4da;
        }

        /* Notificación Flotante */
        .toast-container-custom {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1060;
        }
        
        .toast-minimalista {
            background: #ffffff !important;
            border-left: 5px solid #198754 !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08) !important;
            border-radius: 16px;
            min-width: 320px;
            border-top: none;
            border-right: none;
            border-bottom: none;
        }
    </style>
</head>
<body>

<div class="toast-container-custom">
    <div id="toastExito" class="toast toast-minimalista fade hide" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-body p-3">
            <div class="d-flex align-items-start mb-2">
                <div class="p-2 rounded-circle me-3" style="background: rgba(25, 135, 84, 0.1); color: #198754;">
                    <i class="fa-solid fa-circle-check" style="font-size: 1.4rem;"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-success m-0" style="font-size: 1rem;">¡Registro con éxito!</h6>
                    <p class="text-muted small m-0 mt-1">Tu cuenta fue creada de forma correcta en el sistema.</p>
                </div>
            </div>
            <div class="text-end border-top pt-2 mt-3">
                <a href="index.php" class="btn btn-success btn-sm px-4 py-2" style="border-radius: 10px; font-weight: bold; background: #198754; border: none;">Aceptar</a>
            </div>
        </div>
    </div>
</div>

<div class="registro-container">
    <div class="text-center mb-3">
        <div class="d-inline-block p-2.5 rounded-circle mb-2" style="background: rgba(111, 45, 168, 0.1); color: #6f2da8;">
            <i class="fa-solid fa-user-plus fa-xl"></i>
        </div>
        <h2 style="color: #6f2da8; font-weight: bold; margin: 0; font-size: 1.6rem;">REGISTRARSE</h2>
        <p class="text-muted small mb-0 mt-1">Regístrate para acceder.</p>
    </div>

    <?php if($mensaje != ''): ?>
        <div class="alert alert-danger d-flex align-items-center mb-3" style="border-radius: 12px; border-left: 4px solid #dc3545; font-size: 0.85rem; padding: 10px 15px;">
            <i class="fa-solid fa-triangle-exclamation me-2" style="font-size: 1rem;"></i> 
            <div><?= htmlspecialchars($mensaje) ?></div>
        </div>
    <?php endif; ?>

    <form action="" method="post" class="mb-3">
        <div class="mb-2.5">
            <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="letter-spacing: 0.5px; font-size: 0.75rem;">Nombre y Apellido</label>
            <input type="text" class="form-control" name="nombre" value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" placeholder="Ej: Juan Pérez" required>
        </div>

        <div class="mb-2.5">
            <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="letter-spacing: 0.5px; font-size: 0.75rem;">Correo Electrónico</label>
            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" placeholder="nombre@correo.com" required>
        </div>

        <div class="mb-2.5">
            <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="letter-spacing: 0.5px; font-size: 0.75rem;">Contraseña</label>
            <div class="input-group">
                <input type="password" id="inputContraseña" class="form-control" name="contraseña" placeholder="••••••••" required>
                <button type="button" id="btnToggle" class="btn btn-toggle-password">
                    <i id="iconoOjo" class="fa-solid fa-eye"></i>
                </button>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="letter-spacing: 0.5px; font-size: 0.75rem;">Seleccionar Rol</label>
            <select name="rol" class="form-select" required>
                <option value="">Selecciona tu rol...</option>
                <option value="profesor" <?= (($_POST['rol'] ?? '') === 'profesor') ? 'selected' : '' ?>>Profesor</option>
                <option value="alumno" <?= (($_POST['rol'] ?? '') === 'alumno') ? 'selected' : '' ?>>Alumno</option>
                <option value="admin" <?= (($_POST['rol'] ?? '') === 'admin') ? 'selected' : '' ?>>Administrador</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary-custom w-100 py-2.5 mb-2 shadow-sm" style="font-size: 0.95rem;">
            <i class="fa-solid fa-user-check me-2"></i> Registrarse
        </button>
    </form>

    <a href="index.php" class="btn btn-outline-custom w-100 py-2" style="font-size: 0.9rem;">
        <i class="fa-solid fa-arrow-left me-1"></i> Volver al Login
    </a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Lógica interactiva para ver/ocultar contraseña
    document.getElementById('btnToggle').addEventListener('click', function () {
        var inputPass = document.getElementById('inputContraseña');
        var icono = document.getElementById('iconoOjo');
        
        if (inputPass.type === "password") {
            inputPass.type = "text";
            icono.classList.remove('fa-eye');
            icono.classList.add('fa-eye-slash');
        } else {
            inputPass.type = "password";
            icono.classList.remove('fa-eye-slash');
            icono.classList.add('fa-eye');
        }
    });
</script>

<?php if ($registro_exitoso): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var elToast = document.getElementById('toastExito');
            var miToast = new bootstrap.Toast(elToast, { autohide: false });
            miToast.show();
        });
    </script>
<?php endif; ?>

</body>
</html>