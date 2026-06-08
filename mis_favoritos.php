<?php
include 'header.php';
include 'conexion.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'alumno') {
    header("Location: index.php");
    exit;
}

$id_usuario = $_SESSION['id'];

$sql = "SELECT e.* FROM estilos e 
        INNER JOIN favoritos f ON e.id = f.id_estilo 
        WHERE f.id_usuario = $id_usuario 
        ORDER BY f.fecha_favorito DESC";

$resultado = $conn->query($sql);
?>

<div class="container-fluid px-md-5 my-4">
    
    <div class="row align-items-center mb-5 px-2">
        <div class="col-md-3 col-12 order-2 order-md-1 mt-3 mt-md-0">
            <a href="panel_alumno.php" class="btn-back-custom">
                <i class="fa-solid fa-arrow-left me-1"></i> Volver al Inicio
            </a>
        </div>

        <div class="col-md-6 col-12 text-center order-1 order-md-2">
            <span class="text-perfil-lateral text-uppercase mb-1 d-block">
                <i class="fa-solid fa-star me-1"></i> Colección Personal
            </span>
            <h2 class="display-6 main-title" style="font-size: 1.85rem; margin: 0; font-weight: bold;">
                Mis Favoritos <i class=""></i>
            </h2>
            <p class="text-dark-muted small mb-0 mt-1">Tus estilos y recursos teóricos guardados.</p>
        </div>

        <div class="col-md-3 col-12 order-3 mt-3 mt-md-0">
            <div class="input-group group-search-quartz">
                <span class="input-group-text bg-transparent border-0 ps-3 text-muted"><i class="fa-solid fa-magnifying-glass fs-6"></i></span>
                <input type="text" id="buscadorFav" class="form-control bg-transparent border-0 input-quartz-inner text-dark-title py-2" placeholder="Buscar en tus favoritos...">
            </div>
        </div>
    </div>

    <div class="row g-4" id="contenedorFavoritos">
        <?php if ($resultado->num_rows > 0): ?>
            <?php while($estilo = $resultado->fetch_assoc()): ?>
                <div class="col-sm-6 col-md-4 col-lg-3 card-fav-item">
                    <div class="card h-100 card-quartz-light border-0 card-estilo-moderno">
                        
                        <div class="position-relative card-img-container">
                            <?php if (!empty($estilo['imagen']) && file_exists($estilo['imagen'])): ?>
                                <img src="<?= $estilo['imagen'] ?>" class="card-img-top img-style-cover" alt="<?= htmlspecialchars($estilo['nombre']) ?>">
                            <?php else: ?>
                                <div class="fallback-gradient-card">
                                    <i class="fa-solid fa-graduation-cap text-white opacity-25" style="font-size: 3rem;"></i>
                                </div>
                            <?php endif; ?>
                            
                            <span class="badge-heart-floating">
                                <i class="fa-solid fa-heart text-white"></i>
                            </span>
                        </div>

                        <div class="card-body d-flex flex-column p-4">
                            <small class="text-uppercase fw-bold text-muted-custom mb-1">
                                <?= htmlspecialchars($estilo['categoria']) ?>
                            </small>
                            <h5 class="fw-bold mb-2 text-dark-title text-truncate-1"><?= htmlspecialchars($estilo['nombre']) ?></h5>
                            
                            <p class="text-dark-muted small mb-4 text-description-trunc">
                                <?= substr(htmlspecialchars($estilo['descripcion']), 0, 85) ?>...
                            </p>

                            <div class="mt-auto d-flex justify-content-between align-items-center gap-2">
                                <a href="ver_estilo.php?id=<?= $estilo['id'] ?>" class="btn btn-action-view w-100 py-2">
                                    Ver Detalles <i class="fa-solid fa-arrow-right ms-1 small-icon"></i>
                                </a>
                                <a href="agregar_favorito.php?id=<?= $estilo['id'] ?>&from=fav" 
                                   class="btn btn-trash-custom" 
                                   onclick="return confirm('¿Quitar este estilo de tus favoritos?')">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <div class="card card-quartz-light mx-auto p-5 border-0" style="max-width: 600px; border-radius: 24px;">
                    <div class="mb-3">
                        <i class="fa-regular fa-heart fa-4x opacity-25" style="color: #6f2da8;"></i>
                    </div>
                    <h4 class="text-dark-title fw-bold">No tienes favoritos todavía</h4>
                    <p class="text-dark-muted">Cuando encuentres un estilo que te interese en la biblioteca, marca el corazón para guardarlo aquí.</p>
                    <div class="mt-4">
                        <a href="estilos_registrados.php" class="btn btn-submit-custom px-4 py-2.5 d-inline-flex align-items-center gap-2">
                            <i class="fa-solid fa-magnifying-glass"></i> Explorar Biblioteca
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Buscador en tiempo real de favoritos
document.getElementById('buscadorFav').addEventListener('keyup', function() {
    let filtro = this.value.toLowerCase();
    let tarjetas = document.querySelectorAll('.card-fav-item');

    tarjetas.forEach(tarjeta => {
        let texto = tarjeta.innerText.toLowerCase();
        tarjeta.style.display = texto.includes(filtro) ? "block" : "none";
    });
});
</script>

<style>
    /* COMPORTAMIENTO DE PANTALLA COMPLETA Y NATURAL */
    html, body {
        overflow-x: hidden !important;
        overflow-y: auto !important;
        height: auto !important;
    }

    /* Scrollbar minimalista del sistema */
    html::-webkit-scrollbar, body::-webkit-scrollbar {
        width: 8px;
        background-color: transparent;
    }
    html::-webkit-scrollbar-track, body::-webkit-scrollbar-track {
        background: transparent;
    }
    html::-webkit-scrollbar-thumb, body::-webkit-scrollbar-thumb {
        background: rgba(111, 45, 168, 0.25);
        border-radius: 10px;
    }

    body { 
        background-image: linear-gradient(rgba(255, 255, 255, 0.25), rgba(255, 255, 255, 0.35)), url('img/fondo_paneles.jpg') !important; 
        background-size: cover !important;
        background-position: center !important;
        background-repeat: no-repeat !important;
        background-attachment: fixed !important;
        font-family: 'Segoe UI', system-ui, sans-serif;
    }

    /* Botón Volver Estilo Cuarzo */
    .btn-back-custom {
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        border-radius: 10px; 
        font-size: 0.8rem; 
        padding: 9px 18px;
        font-weight: 600;
        color: #495057 !important;
        border: 1px solid rgba(0, 0, 0, 0.12) !important;
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
        font-weight: bold; 
        text-shadow: 0 1px 2px rgba(255, 255, 255, 0.5);
    }

    /* Buscador integrado de Cuarzo */
    .group-search-quartz {
        background: rgba(255, 255, 255, 0.6) !important;
        border: 1px solid rgba(0, 0, 0, 0.1) !important;
        border-radius: 12px !important;
        backdrop-filter: blur(5px);
        transition: all 0.25s ease !important;
    }
    .group-search-quartz:focus-within {
        background: rgba(255, 255, 255, 0.95) !important;
        border-color: #6f2da8 !important;
        box-shadow: 0 0 0 4px rgba(111, 45, 168, 0.15) !important;
    }
    .input-quartz-inner {
        font-size: 0.88rem !important;
        font-weight: 500;
        box-shadow: none !important;
    }

    /* Tarjetas Translúcidas de Cuarzo */
    .card-quartz-light { 
        background: rgba(255, 255, 255, 0.75); 
        backdrop-filter: blur(15px) saturate(130%);
        -webkit-backdrop-filter: blur(15px) saturate(130%);
        border: 1px solid rgba(255, 255, 255, 0.5) !important;
        border-radius: 20px !important; 
        box-shadow: 0 8px 25px rgba(31, 38, 135, 0.03) !important;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }
    .card-estilo-moderno:hover {
        transform: translateY(-6px);
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 12px 26px rgba(111, 45, 168, 0.12) !important;
        border-color: rgba(111, 45, 168, 0.2) !important;
    }

    /* Manejo de Imágenes dentro de la Tarjeta */
    .card-img-container {
        height: 140px;
        overflow: hidden;
        background: #f1f2f6;
    }
    .img-style-cover {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    .card-estilo-moderno:hover .img-style-cover {
        transform: scale(1.04);
    }

    /* Fondo degradado alternativo si no hay imagen */
    .fallback-gradient-card {
        background: linear-gradient(135deg, #e1bee7, #ce93d8);
        height: 100%;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Badge de Corazón Flotante */
    .badge-heart-floating {
        position: absolute;
        top: 12px;
        right: 12px;
        background: #e50914;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 3px 8px rgba(229, 9, 20, 0.3);
        z-index: 2;
    }

    /* Tipografías y textos */
    .text-muted-custom {
        font-size: 0.68rem;
        letter-spacing: 0.8px;
        color: #7b2cbf;
    }
    .text-dark-title {
        color: #1a0633;
    }
    .text-truncate-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .text-description-trunc {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        color: #555555;
        line-height: 1.4;
    }
    .text-dark-muted { color: #5a5a5a !important; }

    /* Botones de acción inferiores */
    .btn-action-view {
        background: #6f2da8;
        color: #ffffff !important;
        border: none;
        font-weight: 600;
        font-size: 0.82rem;
        border-radius: 10px;
        transition: all 0.2s ease;
    }
    .btn-action-view:hover {
        background: #4a157d;
        box-shadow: 0 3px 10px rgba(111, 45, 168, 0.2);
    }
    .small-icon {
        font-size: 0.75rem;
    }

    /* Botón Basura de Cuarzo */
    .btn-trash-custom {
        background: rgba(229, 9, 20, 0.08);
        color: #e50914 !important;
        border: 1px solid rgba(229, 9, 20, 0.1);
        padding: 7px 12px;
        border-radius: 10px;
        transition: all 0.2s ease;
    }
    .btn-trash-custom:hover {
        background: #e50914;
        color: #ffffff !important;
        box-shadow: 0 4px 10px rgba(229, 9, 20, 0.2);
    }

    /* Botón Global para Estado Vacío */
    .btn-submit-custom {
        background: #6f2da8;
        color: #ffffff;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.9rem;
        box-shadow: 0 4px 12px rgba(111, 45, 168, 0.2);
        transition: all 0.2s ease;
        text-decoration: none !important;
    }
    .btn-submit-custom:hover {
        background: #4a157d;
        color: #ffffff;
    }
</style>

<?php include 'footer.php'; ?>