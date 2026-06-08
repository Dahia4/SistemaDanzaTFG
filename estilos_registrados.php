<?php 
include 'header.php';
include 'conexion.php';

// Validación de rol
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] != 'profesor' && $_SESSION['rol'] != 'alumno')) {
    header("Location: index.php");
    exit;
}

// Determinar la ruta según el rol
$url_inicio = ($_SESSION['rol'] === 'profesor') ? 'panel_profesor.php' : 'panel_alumno.php';
?>

<div class="container-fluid px-lg-5 mt-4 mb-5 content-max-width">
    
    <!-- Encabezado alineado -->
    <div class="row align-items-center mb-5 header-spacing">
        <!-- Columna Izquierda: Botón Volver -->
        <div class="col-md-3 col-12 mb-3 mb-md-0 text-start">
            <a href="<?= $url_inicio ?>" class="btn-back-custom">
                <i class="fa-solid fa-arrow-left me-1"></i> Volver al Inicio
            </a>
        </div>

        <!-- Columna Central: Título principal -->
        <div class="col-md-6 col-12 text-center">
            <span class="text-perfil-lateral text-uppercase mb-1 d-block">
                <i class="fa-solid fa-layer-group me-1"></i> Recursos Disponibles
            </span>
            <h2 class="display-6 main-title" style="font-size: 2rem;">Biblioteca de Estilos</h2>
            <p class="sub-title small mb-0">Explora recursos multimedia e información de tus disciplinas de danza favoritas.</p>
        </div>
        
        <!-- Columna Derecha: Buscador -->
        <div class="col-md-3 col-12 mt-3 mt-md-0 text-end">
            <div class="input-group input-group-quartz ms-auto" style="max-width: 300px;">
                <span class="input-group-text border-0 bg-transparent ps-3"><i class="fa-solid fa-magnifying-glass text-muted small"></i></span>
                <input type="text" id="buscadorEstilos" class="form-control border-0 bg-transparent py-2 input-quartz-inner text-dark-title" placeholder="Buscar estilo o categoría...">
            </div>
        </div>
    </div>

    <div class="row g-4" id="contenedorEstilos">
    <?php
    $estilos = $conn->query("SELECT * FROM estilos ORDER BY fecha_creacion DESC");
    if ($estilos->num_rows > 0):
        while ($fila = $estilos->fetch_assoc()):
            $nombre = htmlspecialchars($fila['nombre']);
            $subestilo = !empty($fila['subestilo']) ? htmlspecialchars($fila['subestilo']) : "General";
            $categoria = htmlspecialchars($fila['categoria']);
            $id = $fila['id'];
            $imagen = !empty($fila['imagen']) ? htmlspecialchars($fila['imagen']) : '';
    ?>
        <div class="col-sm-6 col-md-4 col-lg-3 card-item">
            <div class="card h-100 border-0 card-quartz-light card-estilo-moderno">
                
                <?php if ($imagen && file_exists($imagen)): ?>
                    <div class="portada-estilo-foto position-relative" style="background-image: url('<?= $imagen ?>');">
                        <span class="badge label-categoria-badge position-absolute bottom-0 start-0 m-3 text-uppercase">
                            <?= $categoria ?>
                        </span>
                    </div>
                <?php else: ?>
                    <div class="icono-estilo position-relative">
                        <i class="fa-solid fa-music"></i>
                        <span class="badge label-categoria-badge position-absolute bottom-0 start-0 m-3 text-uppercase">
                            <?= $categoria ?>
                        </span>
                    </div>
                <?php endif; ?>
                
                <div class="card-body text-center d-flex flex-column p-4 justify-content-between">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark-title"><?= $nombre ?></h5>
                        <p class="text-dark-muted small text-uppercase mb-4" style="letter-spacing: 0.8px; font-size: 0.68rem; font-weight: 600;"><?= $subestilo ?></p>
                    </div>
                    
                    <div class="mt-auto">
                        <a href="ver_estilo.php?id=<?= $id ?>" class="btn btn-minimal-action btn-hover-purple w-100 fw-bold py-2 text-decoration-none d-block">
                            Ver Detalles
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php 
        endwhile; 
    else:
    ?>
        <div class="col-12 text-center py-5">
            <i class="fa-solid fa-record-vinyl fa-3x text-dark-muted opacity-25 mb-3"></i>
            <h5 class="text-dark-muted fw-bold small">No hay estilos registrados todavía en la biblioteca.</h5>
        </div>
    <?php endif; ?>
    </div>
</div>

<script>
document.getElementById('buscadorEstilos').addEventListener('keyup', function() {
    let filtro = this.value.toLowerCase();
    let tarjetas = document.querySelectorAll('.card-item');

    tarjetas.forEach(tarjeta => {
        let contenido = tarjeta.innerText.toLowerCase();
        tarjeta.style.display = contenido.includes(filtro) ? "block" : "none";
    });
});
</script>

<style>
   
    html, body {
        overflow-x: hidden !important;
        overflow-y: auto !important; /* Permitir scroll vertical normal */
        height: auto !important;
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
        max-width: 1400px;
        margin-left: auto;
        margin-right: auto;
    }

    .btn-back-custom {
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        border-radius: 10px; 
        font-size: 0.8rem; 
        padding: 8px 16px;
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

    .text-perfil-lateral {
        color: #6f2da8 !important;
        font-size: 0.75rem; 
        font-weight: 700;
        letter-spacing: 1px;
    }

    .main-title {
        color: #1a0633 !important; 
        font-weight: 700; 
        text-shadow: 0 1px 2px rgba(255, 255, 255, 0.5);
    }

    .sub-title {
        color: #5a5a5a !important;
        font-weight: 500;
    }

    /* Buscador integrado */
    .input-group-quartz {
        background: rgba(255, 255, 255, 0.5) !important;
        border: 1px solid rgba(0, 0, 0, 0.1) !important;
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

    .card-quartz-light { 
        background: rgba(255, 255, 255, 0.7) ; 
        backdrop-filter: blur(20px) saturate(140%);
        -webkit-backdrop-filter: blur(20px) saturate(140%);
        border: 1px solid rgba(255, 255, 255, 0.6) !important;
        border-radius: 22px !important; 
        box-shadow: 0 8px 32px rgba(31, 38, 135, 0.04) !important;
        overflow: hidden;
    }

    .portada-estilo-foto {
        height: 145px; 
        background-size: cover; 
        background-position: center;
        transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }
    
    .icono-estilo {
        background: linear-gradient(135deg, rgba(111, 45, 168, 0.85), rgba(162, 82, 238, 0.85)); 
        height: 145px; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        color: white;
    }
    .icono-estilo i {
        font-size: 45px; 
        opacity: 0.4;
        transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .card-estilo-moderno {
        transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.4s ease;
    }
    .card-estilo-moderno:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 24px rgba(111, 45, 168, 0.12) !important;
    }
    .card-estilo-moderno:hover .portada-estilo-foto {
        transform: scale(1.04);
    }
    .card-estilo-moderno:hover .icono-estilo i {
        transform: scale(1.12) rotate(-6deg);
    }

    .label-categoria-badge {
        background: rgba(26, 6, 51, 0.6) !important; 
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        color: #fff !important;
        font-weight: 700;
        font-size: 0.65rem !important;
        letter-spacing: 0.5px;
        padding: 6px 10px !important;
        border-radius: 8px !important;
        border: 1px solid rgba(255, 255, 255, 0.15);
    }

    .text-dark-title { color: #1a0633; font-size: 0.95rem; }
    .text-dark-muted { color: #5a5a5a !important; }

    .btn-minimal-action {
        background: rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(0, 0, 0, 0.05);
        color: #444444;
        font-size: 0.82rem;
        border-radius: 12px;
        transition: all 0.25s ease;
    }
    .btn-hover-purple:hover { 
        background: #6f2da8 !important; 
        color: #ffffff !important; 
        border-color: #6f2da8 !important;
        box-shadow: 0 4px 12px rgba(111, 45, 168, 0.15);
    }
</style>

<?php include 'footer.php'; ?>