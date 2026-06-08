<?php 
include 'header.php';
include 'conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'profesor') {
    header("Location: index.php");
    exit;
}

/* Lógica de proceso */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevo_alumno'])) {
    $stmt = $conn->prepare("INSERT INTO alumnos (nombre, cedula, ciudad, telefono) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $_POST['nombre'], $_POST['cedula'], $_POST['ciudad'], $_POST['telefono']);
    $stmt->execute();
    $alumno_id = $stmt->insert_id;
    $stmt->close();

    if (isset($_POST['presente'])) {
        $fecha = $_POST['fecha'];
        $valor = $_POST['presente'];
        $stmt2 = $conn->prepare("INSERT INTO asistencia (alumno_id, fecha, presente) VALUES (?, ?, ?)");
        $stmt2->bind_param("isi", $alumno_id, $fecha, $valor);
        $stmt2->execute();
        $stmt2->close();
    }
    
    $_SESSION['mensaje_exito'] = "¡Asistencia y alumno registrados con éxito!";
    header("Location: gestion_asistencia.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_alumno'])) {
    $stmt = $conn->prepare("UPDATE alumnos SET nombre=?, cedula=?, ciudad=?, telefono=? WHERE id=?");
    $stmt->bind_param("ssssi", $_POST['nombre'], $_POST['cedula'], $_POST['ciudad'], $_POST['telefono'], $_POST['id']);
    $stmt->execute();
    $stmt->close();

    if (isset($_POST['presente'])) {
        $fecha = $_POST['fecha'];
        $valor = $_POST['presente'];
        $exists = $conn->query("SELECT id FROM asistencia WHERE alumno_id={$_POST['id']} AND fecha='$fecha'")->fetch_assoc();
        if ($exists) {
            $stmt2 = $conn->prepare("UPDATE asistencia SET presente=? WHERE id=?");
            $stmt2->bind_param("ii", $valor, $exists['id']);
        } else {
            $stmt2 = $conn->prepare("INSERT INTO asistencia (alumno_id, fecha, presente) VALUES (?, ?, ?)");
            $stmt2->bind_param("isi", $_POST['id'], $fecha, $valor);
        }
        $stmt2->execute();
        $stmt2->close();
    }
    
    $_SESSION['mensaje_exito'] = "¡Registro de asistencia actualizado con éxito!";
    header("Location: gestion_asistencia.php");
    exit;
}

if (isset($_GET['eliminar_id'])) {
    $id = $_GET['eliminar_id'];
    $conn->query("DELETE FROM asistencia WHERE alumno_id=$id");
    $conn->query("DELETE FROM alumnos WHERE id=$id");
    
    $_SESSION['mensaje_exito'] = "Alumno y su historial eliminados correctamente.";
    header("Location: gestion_asistencia.php");
    exit;
}

$alumnos = $conn->query("SELECT * FROM alumnos ORDER BY nombre ASC");

$alumno_editar = null;
if (isset($_GET['editar_id'])) {
    $id_editar = $_GET['editar_id'];
    $alumno_editar = $conn->query("SELECT * FROM alumnos WHERE id=$id_editar")->fetch_assoc();
    $asis = $conn->query("SELECT presente FROM asistencia WHERE alumno_id=$id_editar AND fecha='".date('Y-m-d')."'")->fetch_assoc();
    $alumno_editar['presente'] = (isset($asis['presente'])) ? $asis['presente'] : '';
}
?>

<div class="container-fluid px-lg-5 mt-4 mb-4 positioning-container content-max-width">
    
    <div class="logout-wrapper">
        <a href="panel_profesor.php" class="btn-back-custom">
            <i class="fa-solid fa-arrow-left"></i> Volver al Inicio
        </a>
    </div>

    <div class="row align-items-start mb-3 header-spacing">
        <div class="col-md-2 col-12 text-start mb-2 mb-md-0">
        </div>

        <div class="col-12 col-md-8 text-center">
            <h2 class="display-6 main-title" style="font-size: 1.85rem;">Gestión de Clases</h2>
            <p class="sub-title small">Toma asistencia diaria o edita la información básica de tus alumnos matriculados.</p>
        </div>
        
        <div class="col-md-2 d-none d-md-block"></div>
    </div>

    <?php if (isset($_SESSION['mensaje_exito'])): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 p-2.5 mx-auto alert-soft-success mb-3" role="alert" style="max-width: 100%;">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-circle-check me-2" style="font-size: 1.1rem;"></i>
                <div class="small fw-bold text-success-custom"><?= $_SESSION['mensaje_exito']; ?></div>
            </div>
            <button type="button" class="btn-close style-close-alert" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['mensaje_exito']); ?>
    <?php endif; ?>

    <div class="row g-4 justify-content-center">
        <div class="col-12 col-lg-5">
            <div class="card border-0 p-4 card-quartz-light h-100 justify-content-center">
                <div class="card-body p-0">
                    <div class="d-flex align-items-center justify-content-center gap-2 mb-3">
                        <div class="icon-minimalista-mono">
                            <?= $alumno_editar ? '<i class="fa-solid fa-user-pen"></i>' : '<i class="fa-solid fa-user-plus"></i>' ?>
                        </div>
                        <h5 class="fw-bold text-dark mb-0 title-card-custom">
                            <?= $alumno_editar ? 'Editar Asistencia' : 'Nueva Asistencia' ?>
                        </h5>
                    </div>

                    <form method="post" class="text-start">
                        <?php if ($alumno_editar): ?>
                            <input type="hidden" name="editar_alumno" value="1">
                            <input type="hidden" name="id" value="<?= $alumno_editar['id'] ?>">
                        <?php else: ?>
                            <input type="hidden" name="nuevo_alumno" value="1">
                        <?php endif; ?>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold label-profile-custom">NOMBRE COMPLETO</label>
                                <input type="text" class="form-control input-quartz-custom" name="nombre" value="<?= $alumno_editar['nombre'] ?? '' ?>" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold label-profile-custom">CÉDULA</label>
                                <input type="text" class="form-control input-quartz-custom" name="cedula" value="<?= $alumno_editar['cedula'] ?? '' ?>" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold label-profile-custom">CIUDAD</label>
                                <input type="text" class="form-control input-quartz-custom" name="ciudad" value="<?= $alumno_editar['ciudad'] ?? '' ?>">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold label-profile-custom">TELÉFONO</label>
                                <input type="text" class="form-control input-quartz-custom" name="telefono" value="<?= $alumno_editar['telefono'] ?? '' ?>">
                            </div>
                        </div>

                        <div class="row g-3 mb-3 align-items-end">
                            <div class="col-6">
                                <label class="form-label small fw-bold label-profile-custom">FECHA DE CLASE</label>
                                <input type="date" name="fecha" class="form-control input-quartz-custom" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold label-profile-custom">ESTADO DE ASISTENCIA</label>
                                <div class="d-flex gap-2">
                                    <div class="flex-fill">
                                        <input type="radio" name="presente" value="1" id="asist-1" class="btn-check" <?= (isset($alumno_editar['presente']) && $alumno_editar['presente'] === 1) ? 'checked' : '' ?> required>
                                        <label class="btn btn-outline-success w-100 py-2 btn-asistencia-custom text-uppercase" for="asist-1">
                                            <i class="fa-solid fa-check me-1"></i> Pres
                                        </label>
                                    </div>
                                    <div class="flex-fill">
                                        <input type="radio" name="presente" value="0" id="asist-0" class="btn-check" <?= (isset($alumno_editar['presente']) && $alumno_editar['presente'] === 0) ? 'checked' : '' ?>>
                                        <label class="btn btn-outline-danger w-100 py-2 btn-asistencia-custom text-uppercase" for="asist-0">
                                            <i class="fa-solid fa-xmark me-1"></i> Aus
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button class="btn btn-minimal-action btn-hover-purple w-100 fw-bold py-2.5 mb-2">
                                <?= $alumno_editar ? "Guardar Registro" : "Guardar Registro" ?>
                            </button>
                            
                            <?php if($alumno_editar): ?>
                                <a href="gestion_asistencia.php" class="btn btn-cancel-custom w-100 py-2 text-center text-decoration-none d-block small fw-bold">Cancelar Edición</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-7">
            <div class="card border-0 p-4 card-quartz-light h-100">
                <div class="card-body p-0 d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="fw-bold text-dark mb-3 title-card-custom text-start">
                            <i class="fa-solid fa-users me-2 text-secondary"></i>Alumnos Matriculados
                        </h5>

                        <div class="table-responsive table-wrapper-quartz">
                            <table class="table table-hover align-middle table-quartz-custom mb-0">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Cédula</th>
                                        <th>Teléfono</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($alumnos as $a): ?>
                                        <tr>
                                            <td class="fw-bold text-dark-title"><?= htmlspecialchars($a['nombre']) ?></td>
                                            <td class="text-dark-muted font-monospace small"><?= htmlspecialchars($a['cedula']) ?></td>
                                            <td class="text-dark-muted small"><?= htmlspecialchars($a['telefono'] ?: '-') ?></td>
                                            <td class="text-center">
                                                <div class="d-inline-flex gap-1">
                                                    <a href="gestion_asistencia.php?editar_id=<?= $a['id'] ?>" class="btn btn-action-table btn-table-edit" title="Pasar Asistencia / Editar">
                                                        <i class="fa-solid fa-calendar-check"></i>
                                                    </a>
                                                    <a href="gestion_asistencia.php?eliminar_id=<?= $a['id'] ?>" class="btn btn-action-table btn-table-delete" 
                                                       onclick="return confirm('¿Seguro que deseas eliminar a este alumno y todo su historial de asistencia?')" title="Eliminar Alumno">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <a href="lista_asistencias.php" class="btn btn-minimal-action btn-hover-purple w-100 fw-bold py-2 text-center text-decoration-none d-block">
                            <i class="fa-solid fa-list-ul me-2"></i> Ver Historial Completo de Asistencias
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
   
    html, body {
        overflow: hidden !important; 
        height: 100% !important;
    }

    body { 
        background-image: linear-gradient(rgba(255, 255, 255, 0.25), rgba(255, 255, 255, 0.35)), url('img/fondo_paneles.jpg') !important; 
        background-size: cover !important;
        background-position: center !important;
        background-repeat: no-repeat !important;
        background-attachment: fixed !important;
        font-family: 'Segoe UI', system-ui, sans-serif;
    }

    .content-max-width {
        max-width: 1350px; 
        margin-left: auto;
        margin-right: auto;
    }

    .positioning-container { position: relative; }

    .logout-wrapper { position: absolute; top: 0; right: 15px; z-index: 10; }

    .btn-back-custom {
        text-decoration: none !important;
        display: inline-block;
        border-radius: 10px; 
        font-size: 0.75rem; 
        padding: 6px 14px;
        font-weight: 600;
        color: #495057 !important;
        border: 1px solid rgba(0, 0, 0, 0.15) !important;
        background-color: rgba(255, 255, 255, 0.6);
        backdrop-filter: blur(5px);
        transition: all 0.25s ease;
    }
    .btn-back-custom:hover {
        background-color: #6f2da8 !important;
        border-color: #6f2da8 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(111, 45, 168, 0.2);
    }

    .header-spacing { padding-top: 5px; }

    .text-perfil-lateral {
        color: #4a157d !important;
        text-decoration: none !important;
        font-size: 0.8rem; 
        font-weight: 700;
        letter-spacing: 0.5px;
        display: inline-block;
        text-shadow: 0 1px 2px rgba(255, 255, 255, 0.6);
    }

    .main-title {
        color: #1a0633 !important; 
        font-weight: 700; 
        text-shadow: 0 1px 2px rgba(255, 255, 255, 0.5);
    }

    .sub-title {
        color: #3b2d4a !important;
        font-weight: 500;
        text-shadow: 0 1px 2px rgba(255, 255, 255, 0.5);
    }

    /* Tarjeta de Cuarzo Rediseñada */
    .card-quartz-light { 
        background: rgba(255, 255, 255, 0.75); 
        backdrop-filter: blur(20px) saturate(140%);
        -webkit-backdrop-filter: blur(20px) saturate(140%);
        border: 1px solid rgba(255, 255, 255, 0.6) !important;
        border-radius: 22px !important; 
        box-shadow: 0 8px 32px rgba(31, 38, 135, 0.05) !important;
    }

    .icon-minimalista-mono {
        font-size: 1.35rem;
        color: #4a4a4a;
        opacity: 0.85;
    }

    .title-card-custom {
        font-size: 1.1rem; 
        color: #1a0633 !important;
    }

    .label-profile-custom {
        color: #5a5a5a !important;
        font-size: 0.7rem !important;
        letter-spacing: 0.3px;
        margin-bottom: 3px;
    }

    .input-quartz-custom {
        background: rgba(255, 255, 255, 0.5) !important;
        border: 1px solid rgba(0, 0, 0, 0.08) !important;
        border-radius: 12px !important;
        padding: 8px 12px !important;
        font-size: 0.85rem !important;
        color: #1a0633 !important;
        font-weight: 500;
        transition: all 0.25s ease !important;
    }
    .input-quartz-custom:focus {
        background: rgba(255, 255, 255, 0.95) !important;
        border-color: #6f2da8 !important;
        box-shadow: 0 0 0 4px rgba(111, 45, 168, 0.15) !important;
    }

    .btn-asistencia-custom {
        border-radius: 12px !important;
        font-size: 0.8rem !important;
        font-weight: 700 !important;
        padding: 7px !important;
        background: rgba(255, 255, 255, 0.4);
        transition: all 0.25s ease !important;
    }
    .btn-check:checked + .btn-outline-success {
        background-color: #198754 !important;
        border-color: #198754 !important;
        color: #ffffff !important;
    }
    .btn-check:checked + .btn-outline-danger {
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
        color: #ffffff !important;
    }

    .table-wrapper-quartz {
        max-height: 290px;
        overflow-y: auto;
        scrollbar-width: none;
    }
    .table-wrapper-quartz::-webkit-scrollbar { display: none; }

    .table-quartz-custom { background: transparent !important; }
    .table-quartz-custom thead tr th {
        background: rgba(0, 0, 0, 0.03) !important;
        color: #4a157d !important;
        font-size: 0.72rem !important;
        font-weight: 700 !important;
        text-transform: uppercase;
        border-bottom: 1px solid rgba(0, 0, 0, 0.08) !important;
        padding: 10px;
    }
    .table-quartz-custom tbody tr td {
        padding: 10px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.04) !important;
        background: transparent !important;
    }

    .text-dark-title { color: #1a0633; font-size: 0.88rem; }
    .text-dark-muted { color: #5a5a5a !important; }

    .btn-action-table {
        border-radius: 8px !important;
        padding: 4px 9px !important;
        font-size: 0.78rem !important;
        background: rgba(255, 255, 255, 0.6);
    }
    .btn-table-edit { border: 1px solid rgba(111, 45, 168, 0.3) !important; color: #6f2da8 !important; }
    .btn-table-edit:hover { background: #6f2da8 !important; color: #fff !important; }
    .btn-table-delete { border: 1px solid rgba(220, 53, 69, 0.3) !important; color: #dc3545 !important; }
    .btn-table-delete:hover { background: #dc3545 !important; color: #fff !important; }

    .btn-minimal-action {
        background: rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(0, 0, 0, 0.05);
        color: #444444;
        font-size: 0.85rem;
        border-radius: 12px;
        transition: all 0.25s ease;
    }
    .btn-hover-purple:hover { 
        background: #6f2da8 !important; 
        color: #ffffff !important; 
        border-color: #6f2da8 !important;
    }
    
    .btn-cancel-custom {
        background: transparent;
        border: 1px dashed rgba(0, 0, 0, 0.15);
        color: #6c757d;
        border-radius: 12px;
        font-size: 0.78rem;
    }

    .alert-soft-success {
        background: rgba(212, 239, 223, 0.85);
        border: 1px solid rgba(40, 167, 69, 0.2) !important;
        border-radius: 14px;
    }
    .text-success-custom { color: #196f3d; }

    @media (max-width: 992px) {
        html, body { overflow-y: auto !important; }
        .table-wrapper-quartz { max-height: none; }
        .logout-wrapper { position: static; text-align: left; margin-bottom: 15px; }
    }
</style>

<?php include 'footer.php'; ?>




