<?php
include 'header.php';
include 'conexion.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'profesor') {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: lista_asistencias.php");
    exit;
}

$id = intval($_GET['id']);

// Obtener datos actuales de la asistencia y el nombre del alumno
$query = "SELECT asis.*, a.nombre 
          FROM asistencia asis 
          JOIN alumnos a ON asis.alumno_id = a.id 
          WHERE asis.id = $id";
$res = $conn->query($query);
$asis = $res->fetch_assoc();

if (!$asis) {
    echo "Registro no encontrado.";
    exit;
}

// Procesar la actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_estado = $_POST['presente'];
    $nueva_fecha = $_POST['fecha'];

    $stmt = $conn->prepare("UPDATE asistencia SET presente = ?, fecha = ? WHERE id = ?");
    $stmt->bind_param("isi", $nuevo_estado, $nueva_fecha, $id);

    if ($stmt->execute()) {
        header("Location: lista_asistencias.php?msg=editado");
    } else {
        echo "Error al actualizar.";
    }
    exit;
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-4" style="color: #6f2da8;">
                        <i class="fa-solid fa-user-check me-2"></i>Editar Asistencia
                    </h4>
                    <p class="text-muted">Alumno: <strong><?= htmlspecialchars($asis['nombre']) ?></strong></p>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Fecha del Registro</label>
                            <input type="date" name="fecha" class="form-control" value="<?= $asis['fecha'] ?>" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small d-block">Estado</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="presente" id="pres" value="1" <?= $asis['presente'] ? 'checked' : '' ?>>
                                <label class="btn btn-outline-success" for="pres">Presente</label>

                                <input type="radio" class="btn-check" name="presente" id="aus" value="0" <?= !$asis['presente'] ? 'checked' : '' ?>>
                                <label class="btn btn-outline-danger" for="aus">Ausente</label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="lista_asistencias.php" class="btn btn-light w-100" style="border-radius: 10px;">Cancelar</a>
                            <button type="submit" class="btn btn-primary w-100" style="background: #6f2da8; border: none; border-radius: 10px;">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>