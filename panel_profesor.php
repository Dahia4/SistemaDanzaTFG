<?php
include 'header.php';
include 'conexion.php';

// Verificar que sea profesor
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'profesor') {
    header("Location: index.php");
    exit;
}

$nombre_profesor = $_SESSION['nombre'];
?>

<div class="container mt-4 positioning-container">
    
    <div class="logout-wrapper">
        <a href="logout.php" class="btn-logout-custom">
            <i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión
        </a>
    </div>

    <div class="row align-items-start mb-4 header-spacing">
        <div class="col-md-2 col-12 text-start mb-3 mb-md-0">
    </div>

        <div class="col-12 col-md-8 text-center">
            <h2 class="display-5 main-title">
                ¡Hola, Prof. <?= htmlspecialchars($nombre_profesor) ?>! 
            </h2>
            <p class="sub-title">Bienvenido a tu portal de administración. Gestiona la biblioteca, asistencias y alumnos.</p>
        </div>
        
        <div class="col-md-2 d-none d-md-block"></div>
    </div>

    <!-- Tarjetas con la misma disposición horizontal -->
    <div class="row g-4 justify-content-center row-cols-1 row-cols-sm-2 row-cols-md-4 row-cols-lg-5">
        
        <!-- Tarjeta 1: Cargar Estilos -->
        <div class="col d-flex">
            <div class="card w-100 border-0 text-center p-3 card-quartz-light">
                <div class="card-body d-flex flex-column justify-content-between p-0">
                    <div class="mb-2">
                        <div class="icon-minimalista-mono mb-3">
                            <i class="fa-solid fa-pen-nib"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2 title-card-custom">Cargar Estilos</h5>
                        <p class="text-dark-muted small mb-0">Registra y publica nuevas danzas en la plataforma académica.</p>
                    </div>
                    <a href="biblioteca_estilos.php" class="btn btn-minimal-action btn-hover-purple w-100 fw-bold">
                        Ingresar
                    </a>
                </div>
            </div>
        </div>

        <!-- Tarjeta 2: Biblioteca -->
        <div class="col d-flex">
            <div class="card w-100 border-0 text-center p-3 card-quartz-light">
                <div class="card-body d-flex flex-column justify-content-between p-0">
                    <div class="mb-2">
                        <div class="icon-minimalista-mono mb-3">
                            <i class="fa-solid fa-book"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2 title-card-custom">Biblioteca</h5>
                        <p class="text-dark-muted small mb-0">Visualiza, edita o elimina los registros teóricos actuales.</p>
                    </div>
                    <a href="estilos_registrados.php" class="btn btn-minimal-action btn-hover-purple w-100 fw-bold">
                        Ingresar
                    </a>
                </div>
            </div>
        </div>

        <!-- Tarjeta 3: Tomar Asistencia -->
        <div class="col d-flex">
            <div class="card w-100 border-0 text-center p-3 card-quartz-light">
                <div class="card-body d-flex flex-column justify-content-between p-0">
                    <div class="mb-2">
                        <div class="icon-minimalista-mono mb-3">
                            <i class="fa-solid fa-calendar-plus"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2 title-card-custom">Asistencia</h5>
                        <p class="text-dark-muted small mb-0">Gestiona y marca los alumnos presentes en las clases del día.</p>
                    </div>
                    <a href="gestion_asistencia.php" class="btn btn-minimal-action btn-hover-purple w-100 fw-bold">
                        Tomar Lista
                    </a>
                </div>
            </div>
        </div>

        <!-- Tarjeta 4: Reporte de Asistencias -->
        <div class="col d-flex">
            <div class="card w-100 border-0 text-center p-3 card-quartz-light">
                <div class="card-body d-flex flex-column justify-content-between p-0">
                    <div class="mb-2">
                        <div class="icon-minimalista-mono mb-3">
                            <i class="fa-solid fa-list-check"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2 title-card-custom">Reportes</h5>
                        <p class="text-dark-muted small mb-0">Revisa el historial general, porcentajes y faltas acumuladas.</p>
                    </div>
                    <a href="lista_asistencias.php" class="btn btn-minimal-action btn-hover-purple w-100 fw-bold">
                        Ver Reportes
                    </a>
                </div>
            </div>
        </div>

        <!-- Tarjeta 5: Consultas Privadas -->
        <div class="col d-flex">
            <div class="card w-100 border-0 text-center p-3 card-quartz-light">
                <div class="card-body d-flex flex-column justify-content-between p-0">
                    <div class="mb-2">
                        <div class="icon-minimalista-mono mb-3">
                            <i class="fa-solid fa-comments"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2 title-card-custom">Consultas</h5>
                        <p class="text-dark-muted small mb-0">Atiende de manera directa las dudas y chats de tus alumnos.</p>
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

    @media (max-width: 768px) {
        .logout-wrapper { position: static; text-align: right; margin-bottom: 15px; padding-right: 15px; }
        body { height: auto; overflow: visible; }
        .card-quartz-light { max-height: none; }
    }
</style>

<?php include 'footer.php'; ?>