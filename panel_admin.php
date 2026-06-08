<?php
session_start();
include 'conexion.php';

// Verificar que sea administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
    header("Location: index.php");
    exit;
}

// Obtener todos los usuarios activos (que no sean el propio admin y no estén eliminados)
$id_admin_actual = $_SESSION['id'];
$query_usuarios = $conn->query("SELECT id, nombre, email, rol FROM usuarios WHERE eliminado = 0 AND id != $id_admin_actual");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('img/fondo_paneles.jpg');
            background-size: cover; background-position: center; background-attachment: fixed;
            min-height: 100vh; color: #fff;
        }
        .admin-card {
            background: rgba(255, 255, 255, 0.95); color: #333; border-radius: 20px; padding: 2rem;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Panel de Administración</h1>
        <a href="logout.php" class="btn btn-danger btn-sm"><i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión</a>
    </div>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Usuario dado de baja correctamente de la plataforma.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card admin-card shadow">
        <h4 class="fw-bold mb-4 text-purple" style="color: #6f2da8;">Gestión de Usuarios Existentes</h4>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th class="text-center">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($user = $query_usuarios->fetch_assoc()): ?>
                    <tr>
                        <td class="fw-bold"><?= htmlspecialchars($user['nombre']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><span class="badge bg-secondary"><?= strtoupper($user['rol']) ?></span></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-outline-danger btn-sm" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalEliminar" 
                                    data-id="<?= $user['id'] ?>" 
                                    data-nombre="<?= htmlspecialchars($user['nombre']) ?>">
                                <i class="fa-solid fa-user-minus"></i> Eliminar Cuenta
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form action="eliminar_usuario.php" method="POST" class="modal-content text-dark">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title"><i class="fa-solid fa-exclamation-triangle"></i> Dar de Baja Usuario</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-content-body p-4">
        <input type="hidden" name="id_usuario" id="modal_id_usuario">
        <p>¿Estás seguro de que deseas eliminar la cuenta de <strong id="modal_nombre_usuario"></strong>?</p>
        
        <div class="mb-3">
            <label for="motivo" class="form-label fw-bold">Selecciona el motivo del bloqueo / baja:</label>
            <select name="motivo" id="motivo" class="form-select" required>
                <option value="" disabled selected>-- Elige una opción --</option>
                <option value="Falta de pago de las mensualidades de danza.">Falta de pago de mensualidad</option>
                <option value="Comportamiento inadecuado o violar las normas de la academia.">Comportamiento inadecuado</option>
                <option value="Inactividad prolongada en la plataforma.">Inactividad prolongada</option>
                <option value="Baja voluntaria solicitada por el usuario.">Baja voluntaria del alumno/profesor</option>
                <option value="Otro motivo administrativo personalizado.">Otro motivo administrativo</option>
            </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-danger">Confirmar Eliminación</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Pasar los datos del usuario seleccionado directamente al Modal interactivo
    const modalEliminar = document.getElementById('modalEliminar');
    modalEliminar.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const nombre = button.getAttribute('data-nombre');
        
        modalEliminar.querySelector('#modal_id_usuario').value = id;
        modalEliminar.querySelector('#modal_nombre_usuario').textContent = nombre;
    });
</script>
</body>
</html>