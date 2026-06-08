<?php
include 'header.php';
include 'conexion.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'alumno') { 
    header("Location: index.php"); 
    exit; 
}

$mensaje = "";
$usuario_id = $_SESSION['id'];

// CARGAR DATOS ACTUALES (Para visualizar lo ya cargado)
$res = $conn->query("SELECT * FROM usuarios WHERE id = $usuario_id");
$user_db = $res->fetch_assoc();
$cedula_actual = $user_db['cedula'];

$datos_alumno = ['ciudad' => '', 'telefono' => ''];
if (!empty($cedula_actual)) {
    $res_alumno = $conn->query("SELECT * FROM alumnos WHERE cedula = '$cedula_actual'");
    if ($res_alumno->num_rows > 0) {
        $datos_alumno = $res_alumno->fetch_assoc();
    }
}

// PROCESAR ACTUALIZACIÓN
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $cedula = trim($_POST['cedula']);
    $ciudad = $_POST['ciudad'];
    $telefono = $_POST['telefono'];

  
    // Validar cédula en la tabla 'usuarios'
    $stmt_check = $conn->prepare("SELECT id FROM usuarios WHERE cedula = ? AND id != ?");
    $stmt_check->bind_param("si", $cedula, $usuario_id);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result();

    if ($res_check->num_rows > 0) {
        $mensaje = "Error: El número de cédula '" . htmlspecialchars($cedula) . "' ya se encuentra registrado por otro usuario.";
    } else {
        
        $check = $conn->query("SELECT id FROM alumnos WHERE cedula = '$cedula'");
        
        if ($check->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE alumnos SET nombre=?, ciudad=?, telefono=? WHERE cedula=?");
            $stmt->bind_param("ssss", $nombre, $ciudad, $telefono, $cedula);
        } else {
            $stmt = $conn->prepare("INSERT INTO alumnos (nombre, cedula, ciudad, telefono) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nombre, $cedula, $ciudad, $telefono);
        }
        
        if ($stmt->execute()) {
            $upd = $conn->prepare("UPDATE usuarios SET cedula = ? WHERE id = ?");
            $upd->bind_param("si", $cedula, $usuario_id);
            $upd->execute();

            $_SESSION['cedula'] = $cedula;
            header("Location: panel_alumno.php?update=exitoso");
            exit;
        } else {
            $mensaje = "Error al guardar los datos. Inténtalo de nuevo.";
        }
        $stmt->close();
    }
    $stmt_check->close();
}
?>

<div class="container mt-4 mb-5 positioning-container content-wrapper-profile">
    
    <div class="logout-wrapper">
        <a href="panel_alumno.php" class="btn-back-custom">
            <i class="fa-solid fa-arrow-left"></i> Volver al Inicio
        </a>
    </div>

    <div class="row align-items-start mb-4 header-spacing">
        <div class="col-md-2 col-12 text-start mb-3 mb-md-0">
            <span class="text-perfil-lateral" style="cursor: default;">
                <i class="fa-solid fa-user-gear"></i> AJUSTES DE PERFIL
            </span>
        </div>

        <div class="col-12 col-md-8 text-center">
            <h2 class="display-6 main-title">Mi Perfil</h2>
            <p class="sub-title">Gestiona tu información personal y vinculación académica.</p>
        </div>
        
        <div class="col-md-2 d-none d-md-block"></div>
    </div>

    <div class="row justify-content-center pb-5">
        <div class="col-12 col-md-6 col-lg-5">
            <div class="card border-0 text-center p-4 card-quartz-light mx-auto" style="max-width: 460px;">
                <div class="card-body p-0">
                    
                    <div class="icon-minimalista-mono mb-4">
                        <i class="fa-solid fa-user-pen"></i>
                    </div>
                    
                    <?php if($mensaje): ?> 
                        <div class="alert alert-danger d-flex align-items-center mb-3" style="border-radius: 14px; background: rgba(220, 53, 69, 0.15); border: 1px solid rgba(220, 53, 69, 0.2); color: #842029;">
                            <i class="fa-solid fa-triangle-exclamation me-2" style="font-size: 1.1rem;"></i>
                            <div class="small text-start"><?= $mensaje ?></div>
                        </div> 
                    <?php endif; ?>

                    <form method="POST" class="text-start">
                        <div class="mb-3">
                            <label class="form-label small fw-bold label-profile-custom">NOMBRE Y APELLIDO</label>
                            <input type="text" name="nombre" class="form-control input-quartz-custom" 
                                   value="<?= htmlspecialchars($_POST['nombre'] ?? $datos_alumno['nombre'] ?? $_SESSION['nombre']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold label-profile-custom">CÉDULA DE IDENTIDAD</label>
                            <input type="text" name="cedula" class="form-control input-quartz-custom" 
                                   value="<?= htmlspecialchars($_POST['cedula'] ?? $cedula_actual) ?>" 
                                   placeholder="Ej: 1.234.567" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold label-profile-custom">CIUDAD</label>
                            <input type="text" name="ciudad" class="form-control input-quartz-custom" 
                                   value="<?= htmlspecialchars($_POST['ciudad'] ?? $datos_alumno['ciudad']) ?>" 
                                   placeholder="Tu ciudad actual">
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold label-profile-custom">TELÉFONO</label>
                            <input type="tel" name="telefono" class="form-control input-quartz-custom" 
                                   value="<?= htmlspecialchars($_POST['telefono'] ?? $datos_alumno['telefono']) ?>" 
                                   placeholder="Ej: 0981 123 456">
                        </div>

                        <button type="submit" class="btn btn-minimal-action btn-hover-purple w-100 fw-bold py-2.5">
                            <i class="fa-solid fa-floppy-disk me-2"></i> Guardar Cambios
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* PERMITIR SUBIR Y BAJAR OCULTANDO LA BARRA DE SCROLL VISUAL */
    html, body {
        scrollbar-width: none; /* Firefox */
        -ms-overflow-style: none; /* IE y Edge */
        overflow-y: auto !important; /* Habilitar scroll de contenido */
        height: 100% !important;
    }
    html::-webkit-scrollbar, body::-webkit-scrollbar {
        display: none; /* Chrome, Safari y Opera */
    }

    body { 
        background-image: linear-gradient(rgba(255, 255, 255, 0.25), rgba(255, 255, 255, 0.35)), url('img/fondo_paneles.jpg') !important; 
        background-size: cover !important;
        background-position: center !important;
        background-repeat: no-repeat !important;
        background-attachment: fixed !important;
        font-family: 'Segoe UI', system-ui, sans-serif;
    }

    .positioning-container {
        position: relative;
    }

    .logout-wrapper {
        position: absolute; 
        top: 0; 
        right: 15px; 
        z-index: 10;
    }

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

    .header-spacing {
        padding-top: 10px;
    }

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
        margin-bottom: 4px;
        text-shadow: 0 1px 2px rgba(255, 255, 255, 0.5);
    }

    .sub-title {
        color: #3b2d4a !important;
        font-size: 0.95rem;
        font-weight: 500;
        text-shadow: 0 1px 2px rgba(255, 255, 255, 0.5);
    }

    /* Estilo exacto de la tarjeta de cuarzo */
    .card-quartz-light { 
        background: rgba(255, 255, 255, 0.75); 
        backdrop-filter: blur(20px) saturate(140%);
        -webkit-backdrop-filter: blur(20px) saturate(140%);
        border: 1px solid rgba(255, 255, 255, 0.6) !important;
        border-radius: 22px !important; 
        box-shadow: 0 8px 32px rgba(31, 38, 135, 0.05) !important;
    }

    .icon-minimalista-mono {
        font-size: 1.6rem;
        color: #4a4a4a; 
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0.85;
    }

    .label-profile-custom {
        color: #5a5a5a !important;
        font-size: 0.75rem !important;
        letter-spacing: 0.3px;
        margin-bottom: 5px;
    }

    /* Inputs estilizados */
    .input-quartz-custom {
        background: rgba(255, 255, 255, 0.5) !important;
        border: 1px solid rgba(0, 0, 0, 0.08) !important;
        border-radius: 12px !important;
        padding: 10px 14px !important;
        font-size: 0.9rem !important;
        color: #1a0633 !important;
        font-weight: 500;
        transition: all 0.25s ease !important;
    }
    .input-quartz-custom:focus {
        background: rgba(255, 255, 255, 0.95) !important;
        border-color: #6f2da8 !important;
        box-shadow: 0 0 0 4px rgba(111, 45, 168, 0.15) !important;
    }

    .btn-minimal-action {
        background: rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(0, 0, 0, 0.05);
        color: #444444;
        padding: 11px;
        font-size: 0.9rem;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.25s ease;
    }
    .btn-hover-purple:hover { 
        background: #6f2da8 !important; 
        color: #ffffff !important; 
        border-color: #6f2da8 !important; 
        box-shadow: 0 4px 15px rgba(111, 45, 168, 0.2);
    }

    @media (max-width: 768px) {
        .logout-wrapper { position: static; text-align: left; margin-bottom: 15px; }
    }
</style>

<?php include 'footer.php'; ?>