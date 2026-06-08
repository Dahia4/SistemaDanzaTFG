<?php 
include 'header.php';
include 'conexion.php';

if (!isset($_GET['id'])) {
    header("Location: estilos_registrados.php");
    exit;
}

$id = intval($_GET['id']);
$estilo = $conn->query("SELECT * FROM estilos WHERE id=$id")->fetch_assoc();

if (!$estilo) {
    echo "<div class='container mt-5 alert alert-danger'>Estilo no encontrado.</div>";
    exit;
}

//BOTÓN VOLVER 
$url_retorno = "estilos_registrados.php";
$texto_retorno = "Volver a la Biblioteca";

if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'mis_favoritos.php') !== false) {
    $url_retorno = "mis_favoritos.php";
    $texto_retorno = "Volver a Mis Favoritos";
}

$imagen = !empty($estilo['imagen']) ? htmlspecialchars($estilo['imagen']) : '';

// Cálculo estimado de tiempo de lectura 
$palabras = str_word_count(strip_tags($estilo['descripcion']));
$tiempo_lectura = max(1, ceil($palabras / 200)); 
?>

<div class="container mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="<?= $url_retorno ?>" class="btn btn-volver-minimal-back text-decoration-none">
            <i class="fa-solid fa-chevron-left me-2"></i> <?= $texto_retorno ?>
        </a>
        
        <?php if ($_SESSION['rol'] === 'alumno'): ?>
            <?php
            $id_estilo = $estilo['id'];
            $id_user = $_SESSION['id'];
            $fav_check = $conn->query("SELECT id FROM favoritos WHERE id_usuario = $id_user AND id_estilo = $id_estilo");
            $es_fav = ($fav_check->num_rows > 0);
            $parametro_retorno = ($url_retorno === "mis_favoritos.php") ? "&from=fav" : "";
            ?>
            <a href="agregar_favorito.php?id=<?= $id_estilo . $parametro_retorno ?>" 
               class="btn shadow-sm btn-favorito <?= $es_fav ? 'btn-fav-active' : 'btn-fav-inactive' ?>">
                <i class="fa-<?= $es_fav ? 'solid' : 'regular' ?> fa-heart me-1"></i> 
                <?= $es_fav ? 'En mis favoritos' : 'Añadir a favoritos' ?>
            </a>
        <?php endif; ?>
    </div>

    <div class="row g-4 align-items-start">
        
        <div class="col-lg-4 columna-sidebar-sticky">
            <div class="card border-0 shadow-sm p-4 card-info-lateral-premium">
                <div class="text-center mb-4">
                    <?php if ($imagen && file_exists($imagen)): ?>
                        <div class="mb-3 contenedor-foto-perfil-estilo">
                            <img src="<?= $imagen ?>" alt="Portada" class="img-fluid foto-estilo-vista">
                        </div>
                    <?php else: ?>
                        <div class="mb-3 d-inline-block p-4 rounded-circle bg-icon-fallback">
                            <i class="fa-solid fa-music fa-3x"></i>
                        </div>
                    <?php endif; ?>

                    <h2 class="fw-bold mt-2 estilo-titulo-lateral"><?= htmlspecialchars($estilo['nombre']) ?></h2>
                    <span class="badge badge-categoria-premium"><?= htmlspecialchars($estilo['categoria']) ?></span>
                </div>

                <hr class="separator-sutil">

                <div class="mb-3 px-2">
                    <label class="label-sidebar-titulo">Subestilo</label>
                    <p class="text-sidebar-contenido"><?= !empty($estilo['subestilo']) ? htmlspecialchars($estilo['subestilo']) : '<span class="text-muted italic">General</span>' ?></p>
                </div>

                <?php if(!empty($estilo['link'])): ?>
                    <div class="mt-3 px-2">
                        <label class="label-sidebar-titulo">Material de Apoyo</label>
                        <a href="<?= htmlspecialchars($estilo['link']) ?>" target="_blank" class="btn btn-yt-premium w-100 py-2 shadow-sm">
                            <i class="fa-brands fa-youtube me-2"></i> Ver video de referencia
                        </a>
                    </div>
                <?php endif; ?>

                <?php if ($_SESSION['rol'] === 'profesor'): ?>
                    <div class="mt-4 pt-3 border-top px-2">
                        <p class="label-sidebar-titulo mb-2">Administración</p>
                        <div class="d-flex gap-2">
                            <a href="biblioteca_estilos.php?editar=<?= $estilo['id'] ?>" class="btn btn-action-edit flex-fill shadow-sm">
                                <i class="fa-solid fa-pen me-1"></i> Editar
                            </a>
                            <a href="biblioteca_estilos.php?eliminar=<?= $estilo['id'] ?>" 
                               onclick="return confirm('¿Estás seguro de eliminar este estilo?')" 
                               class="btn btn-action-delete flex-fill shadow-sm">
                                <i class="fa-solid fa-trash me-1"></i> Borrar
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card card-teoria-editorial border-0 shadow-sm p-4 p-md-5">
                
                <div class="d-flex align-items-center justify-content-between border-b-editorial pb-3 mb-4">
                    <h3 class="titulo-editorial m-0">
                        <i class="fa-solid fa-bookmark me-2 icon-purple-accent"></i> Descripción del Estilo 
                    </h3>
                    <span class="badge-reading-time text-muted">
                        <i class="fa-regular fa-clock me-1"></i> Lectura: <?= $tiempo_lectura ?> min
                    </span>
                </div>

                <div class="descripcion-texto-editorial">
                    <?= nl2br(htmlspecialchars($estilo['descripcion'])) ?>
                </div>
                
                <div class="d-flex justify-content-center mt-5 opacity-25">
                    <span class="decoracion-puntos">•••</span>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    body { background-color: #f4f6f9; font-family: 'Inter', system-ui, -apple-system, sans-serif; }
    
    @media (min-width: 992px) {
        .columna-sidebar-sticky {
            position: -webkit-sticky;
            position: sticky;
            top: 24px;
            z-index: 10;
        }
    }

    /* Botón Volver */
    .btn-volver-minimal-back {
        color: #6f2da8; 
        font-weight: 600;
        background: #fff;
        padding: 8px 16px;
        border-radius: 12px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.03);
        border: 1px solid #e2e8f0;
        transition: all 0.2s;
    }
    .btn-volver-minimal-back:hover {
        background: #f8fafc;
        color: #4b1d74;
    }

    /* Tarjeta Lateral */
    .card-info-lateral-premium {
        border-radius: 24px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02) !important;
        background: #ffffff;
    }
    .estilo-titulo-lateral {
        color: #1a202c; 
        font-size: 1.4rem;
        letter-spacing: -0.3px;
    }
    .badge-categoria-premium {
        background-color: #efe6f7;
        color: #6f2da8;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 30px;
        font-size: 0.8rem;
    }
    .separator-sutil {
        border-top: 1px solid #f1f5f9;
        margin: 20px 0;
        opacity: 1;
    }
    .label-sidebar-titulo {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #94a3b8;
        letter-spacing: 0.8px;
        display: block;
        margin-bottom: 4px;
    }
    .text-sidebar-contenido {
        font-size: 1rem;
        color: #334155;
        font-weight: 500;
    }

    /* Foto de Perfil Lateral */
    .contenedor-foto-perfil-estilo {
        width: 100%;
        max-width: 180px;
        height: 180px;
        margin: 0 auto;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(111, 45, 168, 0.08);
        border: 4px solid #fff;
    }
    .foto-estilo-vista { width: 100%; height: 100%; object-fit: cover; }
    .bg-icon-fallback { background: rgba(111, 45, 168, 0.06); color: #6f2da8; }

    /* Botones de acción rápidos */
    .btn-yt-premium {
        background-color: #fff1f2;
        color: #e11d48;
        border: 1px solid #ffe4e6;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.2s;
    }
    .btn-yt-premium:hover { background-color: #ffe4e6; color: #be123c; }
    
    .btn-action-edit { background: #f0fdf4; color: #16a34a; border-radius: 10px; font-weight: 600; font-size: 0.85rem; border: none; }
    .btn-action-edit:hover { background: #dcfce7; color: #15803d; }
    .btn-action-delete { background: #fef2f2; color: #dc2626; border-radius: 10px; font-weight: 600; font-size: 0.85rem; border: none; }
    .btn-action-delete:hover { background: #fee2e2; color: #b91c1c; }

 
    .card-teoria-editorial {
        border-radius: 24px;
        background: #ffffff;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02) !important;
    }
    .border-b-editorial {
        border-bottom: 2px solid #f1f5f9;
    }
    .titulo-editorial {
        color: #1e1b4b;
        font-weight: 700;
        font-size: 1.35rem;
        letter-spacing: -0.3px;
    }
    .icon-purple-accent {
        color: #6f2da8;
    }
    .badge-reading-time {
        font-size: 0.8rem;
        font-weight: 500;
        background-color: #f8fafc;
        padding: 4px 10px;
        border-radius: 8px;
    }

    /* Formateo avanzado del texto largo */
    .descripcion-texto-editorial {
        line-height: 1.85; 
        color: #334155; /* Gris suave  */
        font-size: 1.08rem;
        text-align: justify;
        font-weight: 400;
        letter-spacing: 0.1px;
    }
    
    .descripcion-texto-editorial::first-line {
        font-weight: 500;
        color: #0f172a;
    }

    .decoracion-puntos {
        letter-spacing: 6px;
        font-size: 1.5rem;
        color: #cbd5e1;
    }

    /* Favoritos */
    .btn-favorito { border-radius: 12px; padding: 8px 18px; font-weight: 600; font-size: 0.9rem; transition: all 0.2s; }
    .btn-fav-inactive { background: #fff; color: #dc2626; border: 1px solid #fee2e2; }
    .btn-fav-inactive:hover { background: #fef2f2; }
    .btn-fav-active { background: #e2e8f0; color: #475569; border: none; }
</style>
