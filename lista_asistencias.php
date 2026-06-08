<?php
include 'header.php';
include 'conexion.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'profesor') {
    header("Location: index.php");
    exit;
}

// Agregamos asis.id para poder referenciar la fila exacta
$sql = "
    SELECT asis.id as asistencia_id, a.nombre, a.cedula, a.ciudad, a.telefono,
           asis.presente, asis.fecha
    FROM alumnos a
    INNER JOIN asistencia asis
        ON a.id = asis.alumno_id
    ORDER BY asis.fecha DESC, a.nombre ASC
";

$result = $conn->query($sql);
?>

<div class="container-fluid px-lg-5 mt-4 mb-4 positioning-container content-max-width">
    
    <div class="logout-wrapper">
        <a href="panel_profesor.php" class="btn-back-custom">
            <i class="fa-solid fa-arrow-left"></i> Volver al Inicio
        </a>
    </div>

    <div class="row align-items-start mb-3 header-spacing">
        <div class="col-md-2 col-12 text-start mb-2 mb-md-0">
            <span class="text-perfil-lateral" style="cursor: default;">
                <i class="fa-solid fa-calendar-days"></i> HISTORIAL
            </span>
        </div>

        <div class="col-12 col-md-8 text-center">
            <h2 class="display-6 main-title" style="font-size: 1.85rem;">Historial de Asistencias</h2>  
        </div>
        
        <div class="col-md-2 d-none d-md-block"></div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card border-0 p-4 card-quartz-light">
                <div class="card-body p-0">
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-dark mb-0 title-card-custom">
                            <i class="fa-solid fa-clock-rotate-left me-2 text-secondary"></i>Registros Guardados
                        </h5>
                        
                        <div class="input-group input-group-quartz" style="max-width: 320px;">
                            <span class="input-group-text border-0 bg-transparent ps-3"><i class="fa-solid fa-magnifying-glass text-muted small"></i></span>
                            <input type="text" id="buscador" class="form-control border-0 bg-transparent py-2 input-quartz-inner text-dark-title" placeholder="Buscar alumno, cédula o fecha...">
                        </div>
                    </div>

                    <div class="table-responsive table-wrapper-quartz">
                        <table class="table table-hover align-middle table-quartz-custom mb-0" id="tablaAsistencia">
                            <thead>
                                <tr>
                                    <th class="ps-4">Nombre</th>
                                    <th>Cédula</th>
                                    <th>Ciudad</th>
                                    <th>Estado</th>
                                    <th class="text-center">Fecha</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="listaBody">
                                <?php if ($result->num_rows > 0) { ?>
                                    <?php while ($row = $result->fetch_assoc()) { ?>
                                        <tr>
                                            <td class="ps-4 fw-bold text-dark-title"><?= htmlspecialchars($row['nombre']) ?></td>
                                            <td class="text-dark-muted font-monospace small"><?= htmlspecialchars($row['cedula']) ?></td>
                                            <td class="text-dark-muted small"><?= htmlspecialchars($row['ciudad'] ?: '-') ?></td>
                                            <td>
                                                <?php if ($row['presente']): ?>
                                                    <span class="badge badge-soft-success px-3 py-1.5 text-uppercase">Presente</span>
                                                <?php else: ?>
                                                    <span class="badge badge-soft-danger px-3 py-1.5 text-uppercase">Ausente</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center text-dark-muted small font-monospace fw-bold"><?= date("d/m/Y", strtotime($row['fecha'])) ?></td>
                                            <td class="text-center">
                                                <div class="d-inline-flex gap-1">
                                                    <a href="editar_asistencia.php?id=<?= $row['asistencia_id'] ?>" class="btn btn-action-table btn-table-edit" title="Editar Registro">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </a>
                                                    <a href="eliminar_asistencia.php?id=<?= $row['asistencia_id'] ?>" 
                                                       class="btn btn-action-table btn-table-delete" 
                                                       onclick="return confirm('¿Estás seguro de eliminar este registro de asistencia?')" title="Eliminar Registro">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-dark-muted fw-bold small">No hay registros de asistencia en la base de datos.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('buscador').addEventListener('keyup', function() {
    let filtro = this.value.toLowerCase();
    let filas = document.querySelectorAll('#listaBody tr');

    filas.forEach(fila => {
        let texto = fila.innerText.toLowerCase();
        // Evita ocultar la fila de "No hay registros" si existiera
        if(fila.cells.length > 1){
            fila.style.display = texto.includes(filtro) ? '' : 'none';
        }
    });
});
</script>

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

    /* Tarjeta de Cuarzo */
    .card-quartz-light { 
        background: rgba(255, 255, 255, 0.75); 
        backdrop-filter: blur(20px) saturate(140%);
        -webkit-backdrop-filter: blur(20px) saturate(140%);
        border: 1px solid rgba(255, 255, 255, 0.6) !important;
        border-radius: 22px !important; 
        box-shadow: 0 8px 32px rgba(31, 38, 135, 0.05) !important;
    }

    .title-card-custom {
        font-size: 1.1rem; 
        color: #1a0633 !important;
    }

    /* Barra de búsqueda integrada al Cuarzo */
    .input-group-quartz {
        background: rgba(255, 255, 255, 0.5) !important;
        border: 1px solid rgba(0, 0, 0, 0.08) !important;
        border-radius: 12px !important;
        transition: all 0.25s ease !important;
    }
    .input-group-quartz:focus-within {
        background: rgba(255, 255, 255, 0.95) !important;
        border-color: #6f2da8 !important;
        box-shadow: 0 0 0 4px rgba(111, 45, 168, 0.15) !important;
    }
    .input-quartz-inner {
        font-size: 0.85rem !important;
        font-weight: 500;
        box-shadow: none !important;
    }

   
    .table-wrapper-quartz {
        max-height: 380px; 
        overflow-y: auto;
        scrollbar-width: none;
    }
    .table-wrapper-quartz::-webkit-scrollbar { display: none; }

    .table-quartz-custom {
        background: transparent !important;
    }
    .table-quartz-custom thead tr th {
        background: rgba(0, 0, 0, 0.03) !important;
        color: #4a157d !important;
        font-size: 0.72rem !important;
        font-weight: 700 !important;
        text-transform: uppercase;
        border-bottom: 1px solid rgba(0, 0, 0, 0.08) !important;
        padding: 12px 10px;
    }
    .table-quartz-custom tbody tr {
        transition: background 0.2s ease;
    }
    .table-quartz-custom tbody tr:hover {
        background: rgba(255, 255, 255, 0.45) !important;
    }
    .table-quartz-custom tbody tr td {
        padding: 11px 10px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.04) !important;
        background: transparent !important;
    }

    .text-dark-title { color: #1a0633; font-size: 0.88rem; }
    .text-dark-muted { color: #5a5a5a !important; }

    /* Badges de Estado Estilizados */
    .badge {
        font-weight: 700;
        font-size: 0.68rem !important;
        letter-spacing: 0.3px;
        border-radius: 8px !important;
    }
    .badge-soft-success {
        background-color: rgba(25, 135, 84, 0.15) !important;
        color: #198754 !important;
        border: 1px solid rgba(25, 135, 84, 0.1) !important;
    }
    .badge-soft-danger {
        background-color: rgba(220, 53, 69, 0.15) !important;
        color: #dc3545 !important;
        border: 1px solid rgba(220, 53, 69, 0.1) !important;
    }

    /* Botones de acción */
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

    @media (max-width: 992px) {
        html, body { overflow-y: auto !important; }
        .table-wrapper-quartz { max-height: none; }
        .logout-wrapper { position: static; text-align: left; margin-bottom: 15px; }
        .input-group-quartz { max-width: 100% !important; margin-top: 10px; }
    }
</style>

<?php include 'footer.php'; ?>
