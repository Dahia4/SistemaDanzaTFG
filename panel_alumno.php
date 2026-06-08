<?php
include 'header.php';
include 'conexion.php';

// Verificar que sea alumno
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'alumno') {
    header("Location: index.php");
    exit;
}

$nombre_alumno = $_SESSION['nombre'];
$id_usuario = $_SESSION['id'];

// Consultar perfil completo
$query_perfil = $conn->query("SELECT cedula FROM usuarios WHERE id = $id_usuario");
$datos_perfil = $query_perfil->fetch_assoc();
$perfil_completo = !empty($datos_perfil['cedula']);
?>

<div class="container mt-4 positioning-container">
    
    <div class="logout-wrapper">
        <a href="logout.php" class="btn-logout-custom">
            <i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión
        </a>
    </div>

    <div class="row align-items-start mb-4 header-spacing">
        <div class="col-md-2 col-12 text-start mb-3 mb-md-0">
            <a href="completar_perfil.php" class="text-perfil-lateral">
                <i class="fa-solid fa-user-gear"></i> MI PERFIL
            </a>
        </div>

        <div class="col-12 col-md-8 text-center">
            <h2 class="display-6 main-title">
                ¡Hola, <?= htmlspecialchars($nombre_alumno) ?>! 
            </h2>
            <p class="sub-title">Bienvenido a tu portal de danza. Gestiona tu asistencia y recursos.</p>
        </div>
        
        <div class="col-md-2 d-none d-md-block"></div>
    </div>

    <?php if (!$perfil_completo): ?>
        <div class="alert shadow-sm border-0 d-flex align-items-center p-3 mb-4 mx-auto alert-soft-warning" role="alert" style="max-width: 850px;">
            <div style="font-size: 1.4rem;" class="me-3 text-warning-custom">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-0" style="color: #664d03; font-size: 0.95rem;">Vincular Perfil</h6>
                <p class="mb-0 small text-dark-muted">Tu cédula no está registrada. <a href="completar_perfil.php" class="fw-bold text-decoration-none" style="color: #6f2da8;">Completa tu perfil aquí</a> para registrar tus asistencias.</p>
            </div>
        </div>
    <?php endif; ?>

    <div class="row g-4 justify-content-center">
        
        <!-- Tarjeta 1: Biblioteca -->
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex">
            <div class="card w-100 border-0 text-center p-3 card-quartz-light">
                <div class="card-body d-flex flex-column justify-content-between p-0">
                    <div class="mb-2">
                        <div class="icon-minimalista-mono mb-3">
                            <i class="fa-solid fa-book-open"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2 title-card-custom">Biblioteca</h5>
                        <p class="text-dark-muted small mb-0">Explora la historia, videos y recursos técnicos de cada estilo.</p>
                    </div>
                    <a href="estilos_registrados.php" class="btn btn-minimal-action btn-hover-purple w-100 fw-bold">
                        Ver Biblioteca
                    </a>
                </div>
            </div>
        </div>

        <!-- Tarjeta 2: Mi Asistencia -->
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex">
            <div class="card w-100 border-0 text-center p-3 card-quartz-light">
                <div class="card-body d-flex flex-column justify-content-between p-0">
                    <div class="mb-2">
                        <div class="icon-minimalista-mono mb-3">
                            <i class="fa-solid fa-calendar-check"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2 title-card-custom">Mi Asistencia</h5>
                        <p class="text-dark-muted small mb-0">Revisa tu porcentaje de presentismo y el registro histórico.</p>
                    </div>
                    <a href="mis_asistencias.php" class="btn btn-minimal-action btn-hover-green w-100 fw-bold">
                        Ver Progreso
                    </a>
                </div>
            </div>
        </div>

        <!-- Tarjeta 3: Mis Favoritos -->
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex">
            <div class="card w-100 border-0 text-center p-3 card-quartz-light">
                <div class="card-body d-flex flex-column justify-content-between p-0">
                    <div class="mb-2">
                        <div class="icon-minimalista-mono mb-3">
                            <i class="fa-solid fa-heart"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2 title-card-custom">Mis Favoritos</h5>
                        <p class="text-dark-muted small mb-0">Acceso rápido a los estilos y teorías que más te gustan.</p>
                    </div>
                    <a href="mis_favoritos.php" class="btn btn-minimal-action btn-hover-red w-100 fw-bold">
                        Ver Guardados
                    </a>
                </div>
            </div>
        </div>

        <!-- Tarjeta 4: Consultas Privadas -->
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex">
            <div class="card w-100 border-0 text-center p-3 card-quartz-light">
                <div class="card-body d-flex flex-column justify-content-between p-0">
                    <div class="mb-2">
                        <div class="icon-minimalista-mono mb-3">
                            <i class="fa-solid fa-comment-dots"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2 title-card-custom">Consultas</h5>
                        <p class="text-dark-muted small mb-0">Chatea directamente con tus profesores ante cualquier duda.</p>
                    </div>
                    <a href="chat.php" class="btn btn-minimal-action btn-hover-purple w-100 fw-bold">
                        Abrir Chat
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    /* Ocultar elementos duplicados heredados */
    nav .btn-danger, header .btn-danger, .navbar .btn-danger,
    a[href="logout.php"]:not(.positioning-container a) { 
        display: none !important; 
    }

    body { 
        background-image: linear-gradient(rgba(255, 255, 255, 0.25), rgba(255, 255, 255, 0.35)), url('img/fondo_paneles.jpg') !important; 
        background-size: cover !important;
        background-position: center !important;
        background-repeat: no-repeat !important;
        background-attachment: fixed !important;
        height: 100vh; 
        overflow: hidden; 
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

    .btn-logout-custom {
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
    .btn-logout-custom:hover {
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.2);
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
        transition: opacity 0.2s ease;
        display: inline-block;
        text-shadow: 0 1px 2px rgba(255, 255, 255, 0.6);
    }
    .text-perfil-lateral:hover {
        opacity: 0.7;
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

    .card-quartz-light { 
        background: rgba(255, 255, 255, 0.75); 
        backdrop-filter: blur(20px) saturate(140%);
        -webkit-backdrop-filter: blur(20px) saturate(140%);
        border: 1px solid rgba(255, 255, 255, 0.6) !important;
        border-radius: 22px !important; 
        max-height: 255px;
        box-shadow: 0 8px 32px rgba(31, 38, 135, 0.05) !important;
        transition: transform 0.3s cubic-bezier(0.165, 0.84, 0.44, 1), background 0.3s, box-shadow 0.3s;
    }
    
    .card-quartz-light:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.92);
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.08) !important;
    }

    .icon-minimalista-mono {
        font-size: 1.35rem;
        color: #4a4a4a; 
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0.85;
    }

    .title-card-custom {
        font-size: 1.1rem; 
        color: #1a0633 !important;
        letter-spacing: -0.2px;
    }

    .text-dark-muted {
        color: #5a5a5a !important;
        font-size: 0.82rem;
        line-height: 1.4;
    }

    .btn-minimal-action {
        background: rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(0, 0, 0, 0.05);
        color: #444444;
        padding: 9px;
        font-size: 0.85rem;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.25s ease;
    }

    .btn-hover-purple:hover { background: #6f2da8 !important; color: #ffffff !important; border-color: #6f2da8 !important; }
    .btn-hover-green:hover { background: #6f2da8 !important; color: #ffffff !important; border-color: #6f2da8 !important; }
    .btn-hover-red:hover { background: #6f2da8 !important; color: #ffffff !important; border-color: #6f2da8 !important; }

    .alert-soft-warning {
        background: rgba(255, 243, 205, 0.85);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 193, 7, 0.25) !important;
        border-radius: 16px;
    }
    .text-warning-custom { color: #664d03; }

    @media (max-width: 768px) {
        .logout-wrapper { position: static; text-align: right; margin-bottom: 15px; padding-right: 15px; }
    }
</style>

<?php include 'footer.php'; ?>