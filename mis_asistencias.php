<?php
include 'header.php';
include 'conexion.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'alumno') {
    header("Location: index.php");
    exit;
}

$usuario_id = $_SESSION['id'];
$nombre_alumno = $_SESSION['nombre'];

$res_usuario = $conn->query("SELECT cedula FROM usuarios WHERE id = $usuario_id");
$user_data = $res_usuario->fetch_assoc();
$mi_cedula = $user_data['cedula'] ?? null;

$asistencias_list = [];
$error_vincular = false;
$stats = ['presentes' => 0, 'ausentes' => 0, 'total' => 0];

if ($mi_cedula) {
    $res_alumno = $conn->query("SELECT id FROM alumnos WHERE cedula = '$mi_cedula'");
    if ($res_alumno->num_rows > 0) {
        $alumno_data = $res_alumno->fetch_assoc();
        $id_interno = $alumno_data['id'];

        $res_asis = $conn->query("SELECT fecha, presente FROM asistencia WHERE alumno_id = $id_interno ORDER BY fecha DESC");
        while($row = $res_asis->fetch_assoc()){
            $asistencias_list[] = $row;
            $row['presente'] == 1 ? $stats['presentes']++ : $stats['ausentes']++;
            $stats['total']++;
        }
    } else {
        $error_vincular = true;
    }
} else {
    $error_vincular = true;
}
?>

<div class="container mt-4 mb-5">
    
    <div class="row align-items-center mb-5">
        <div class="col-md-3 col-12 mb-3 mb-md-0">
            <a href="panel_alumno.php" class="btn btn-outline-secondary shadow-sm" style="border-radius: 10px;">
                <i class="fa-solid fa-arrow-left"></i> Volver al Inicio
            </a>
        </div>

        <div class="col-md-6 col-12 text-center">
            <h2 style="color: #6f2da8; font-weight: bold; margin: 0;">Mi Asistencia</h2>
            <p class="text-muted mb-0">Historial detallado de tus clases.</p>
        </div>
    </div>

    <?php if ($error_vincular): ?>
        <div class="card shadow-sm border-0 p-5 text-center mx-auto" style="border-radius: 20px; max-width: 800px;">
            <div class="mb-3">
                <i class="fa-solid fa-user-slash fa-4x text-muted opacity-25"></i>
            </div>
            <h4 class="fw-bold">Datos no vinculados</h4>
            <p class="text-muted">Tu cédula (<?= htmlspecialchars($mi_cedula) ?>) no coincide con ningún alumno registrado por el profesor.</p>
            <div class="d-flex justify-content-center">
                <a href="completar_perfil.php" class="btn btn-primary px-4" style="background: #6f2da8; border:none; border-radius: 10px;">
                    Verificar mi Perfil
                </a>
            </div>
        </div>
    <?php else: ?>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 text-center h-100" style="border-radius: 20px; border-bottom: 5px solid #6f2da8 !important;">
                    <small class="text-muted fw-bold text-uppercase d-block mb-2">Clases Totales</small>
                    <h2 class="fw-bold mb-0" style="color: #333;"><?= $stats['total'] ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 text-center h-100" style="border-radius: 20px; border-bottom: 5px solid #198754 !important;">
                    <small class="text-muted fw-bold text-uppercase d-block mb-2 text-success">Asistencias</small>
                    <h2 class="fw-bold mb-0 text-success"><?= $stats['presentes'] ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 text-center h-100" style="border-radius: 20px; border-bottom: 5px solid #dc3545 !important;">
                    <small class="text-muted fw-bold text-uppercase d-block mb-2 text-danger">Inasistencias</small>
                    <h2 class="fw-bold mb-0 text-danger"><?= $stats['ausentes'] ?></h2>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0" style="border-radius: 25px; overflow: hidden;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background-color: #fcfaff;">
                            <tr>
                                <th class="ps-5 py-4 border-0 text-muted small text-uppercase">Fecha de Clase</th>
                                <th class="py-4 border-0 text-center text-muted small text-uppercase">Estado</th>
                                <th class="pe-5 py-4 border-0 text-end text-muted small text-uppercase">Detalle</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($stats['total'] > 0): ?>
                                <?php foreach($asistencias_list as $asis): ?>
                                    <tr>
                                        <td class="ps-5 fw-bold" style="color: #444;">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-date me-3 text-muted">
                                                    <i class="fa-regular fa-calendar"></i>
                                                </div>
                                                <?= date("d/m/Y", strtotime($asis['fecha'])) ?>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($asis['presente'] == 1): ?>
                                                <span class="badge px-3 py-2" style="border-radius: 10px; background-color: rgba(25, 135, 84, 0.1); color: #198754;">
                                                    <i class="fa-solid fa-check-circle me-1"></i> PRESENTE
                                                </span>
                                            <?php else: ?>
                                                <span class="badge px-3 py-2" style="border-radius: 10px; background-color: rgba(220, 53, 69, 0.1); color: #dc3545;">
                                                    <i class="fa-solid fa-circle-xmark me-1"></i> AUSENTE
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="pe-5 text-end text-muted small">
                                            Registro Oficial
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted">
                                        <i class="fa-solid fa-inbox fa-3x mb-3 d-block opacity-25"></i>
                                        Aún no tienes asistencias registradas.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    body { background-color: #fcfaff; }
    .badge { font-weight: 700; font-size: 0.75rem; letter-spacing: 0.5px; }
    .table-hover tbody tr:hover { background-color: #f9f6ff; transition: 0.2s; }
    .icon-date { width: 30px; height: 30px; background: #eee; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; }
</style>

<?php include 'footer.php'; ?>