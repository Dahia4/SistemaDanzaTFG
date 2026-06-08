<?php
include 'header.php';
include 'conexion.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'profesor') {
    header("Location: index.php");
    exit;
}

$mensaje_exito = "";
$carpeta_imagenes = "uploads/";
if (!file_exists($carpeta_imagenes)) {
    mkdir($carpeta_imagenes, 0777, true);
}

// 1. AGREGAR NUEVO ESTILO
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nuevo'])) {
    $imagen_nombre = "";
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $imagen_nombre = $carpeta_imagenes . time() . "_" . uniqid() . "." . $ext;
        move_uploaded_file($_FILES['imagen']['tmp_name'], $imagen_nombre);
    }

    $stmt = $conn->prepare("INSERT INTO estilos (nombre, subestilo, descripcion, categoria, link, imagen) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $_POST['nombre'], $_POST['subestilo'], $_POST['descripcion'], $_POST['categoria'], $_POST['link'], $imagen_nombre);
    $stmt->execute();
    $stmt->close();
    header("Location: biblioteca_estilos.php?status=created");
    exit;
}

// 2. EDITAR ESTILO EXISTENTE
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editar'])) {
    $id_edit = intval($_POST['id']);
    $imagen_nombre = $_POST['imagen_actual'];

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $imagen_nombre = $carpeta_imagenes . time() . "_" . uniqid() . "." . $ext;
        move_uploaded_file($_FILES['imagen']['tmp_name'], $imagen_nombre);
        if (!empty($_POST['imagen_actual']) && file_exists($_POST['imagen_actual'])) {
            unlink($_POST['imagen_actual']);
        }
    }

    $stmt = $conn->prepare("UPDATE estilos SET nombre=?, subestilo=?, descripcion=?, categoria=?, link=?, imagen=? WHERE id=?");
    $stmt->bind_param("ssssssi", $_POST['nombre'], $_POST['subestilo'], $_POST['descripcion'], $_POST['categoria'], $_POST['link'], $imagen_nombre, $id_edit);
    $stmt->execute();
    $stmt->close();
    header("Location: biblioteca_estilos.php?status=updated");
    exit;
}

// 3. ELIMINAR ESTILO
if (isset($_GET['eliminar'])) {
    $id_eliminar = intval($_GET['eliminar']);
    $res = $conn->query("SELECT imagen FROM estilos WHERE id = $id_eliminar")->fetch_assoc();
    if (!empty($res['imagen']) && file_exists($res['imagen'])) {
        unlink($res['imagen']);
    }
    $conn->query("DELETE FROM estilos WHERE id = $id_eliminar");
    header("Location: estilos_registrados.php?status=deleted");
    exit;
}

if (isset($_GET['status'])) {
    if ($_GET['status'] == 'created') $mensaje_exito = "¡Estilo publicado con éxito en la biblioteca!";
    if ($_GET['status'] == 'updated') $mensaje_exito = "¡Cambios guardados correctamente!";
}

$editando = false;
if (isset($_GET['editar'])) {
    $editando = true;
    $id_edit = intval($_GET['editar']);
    $estilo = $conn->query("SELECT * FROM estilos WHERE id=$id_edit")->fetch_assoc();
}
?>

<div class="container-fluid px-md-5 my-4">
    
    <div class="row align-items-center mb-4 px-2">
        <div class="col-md-4 col-12 text-start order-2 order-md-1 mt-2 mt-md-0">
            <a href="<?= $editando ? 'ver_estilo.php?id='.$id_edit : 'panel_profesor.php' ?>" class="btn-back-custom">
                <i class="fa-solid fa-arrow-left me-1"></i> Volver
            </a>
        </div>

        <div class="col-md-4 col-12 text-center order-1 order-md-2">
            <span class="text-perfil-lateral text-uppercase mb-1 d-block">
                <i class="fa-solid fa-pen-to-square me-1"></i> Gestión de Contenidos
            </span>
            <h2 class="display-6 main-title" style="font-size: 1.85rem; margin: 0; font-weight: bold;">
                <?= $editando ? 'Modificar Estilo de Danza' : 'Publicar Nuevo Estilo' ?>
            </h2>
        </div>
        
        <div class="col-md-4 col-12 text-end order-3 mt-2 mt-md-0">
            <a href="estilos_registrados.php" class="link-biblioteca-minimal">
                Explorar Biblioteca <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>
    </div>

    <?php if ($mensaje_exito): ?>
        <div class="alert alert-soft-success border-0 fade show p-3 text-center mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> <?= $mensaje_exito ?>
            <button type="button" class="btn-close shadow-none float-end opacity-75" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-12 col-xl-11">
            <div class="card border-0 p-4 p-md-5 card-quartz-wide">
                <form method="post" enctype="multipart/form-data">
                    <?php if ($editando): ?>
                        <input type="hidden" name="editar" value="1">
                        <input type="hidden" name="id" value="<?= $estilo['id'] ?>">
                        <input type="hidden" name="imagen_actual" value="<?= htmlspecialchars($estilo['imagen'] ?? '') ?>">
                    <?php else: ?>
                        <input type="hidden" name="nuevo" value="1">
                    <?php endif; ?>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label label-custom">Nombre del Estilo</label>
                            <input type="text" name="nombre" class="form-control input-custom text-dark-title" placeholder="Ej: Hip Hop, Ballet, Salsa..." value="<?= $editando ? htmlspecialchars($estilo['nombre']) : '' ?>" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label label-custom">Categoría</label>
                            <input type="text" name="categoria" class="form-control input-custom text-dark-title" placeholder="Ej: Danza Urbana, Clásica..." value="<?= $editando ? htmlspecialchars($estilo['categoria']) : '' ?>" required>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label label-custom">Subestilo <span class="text-muted fw-normal lowercase" style="font-size: 0.72rem; font-style: italic;">(opcional)</span></label>
                            <input type="text" name="subestilo" class="form-control input-custom text-dark-title" placeholder="Ej: Locking, Popping, Lyrical..." value="<?= $editando ? htmlspecialchars($estilo['subestilo']) : '' ?>">
                        </div>

                        <div class="col-md-7">
                            <label class="form-label label-custom">Link de Video (YouTube)</label>
                            <div class="input-group group-yt-quartz">
                                <span class="input-group-text bg-transparent border-0 ps-3 text-danger"><i class="fa-brands fa-youtube fs-5"></i></span>
                                <input type="text" name="link" class="form-control bg-transparent border-0 input-quartz-inner text-dark-title py-2.5" placeholder="https://www.youtube.com/watch?v=..." value="<?= $editando ? htmlspecialchars($estilo['link']) : '' ?>">
                            </div>
                        </div>

                        <div class="col-md-7 d-flex flex-column justify-content-between">
                            <div>
                                <label class="form-label label-custom">Descripción Técnica / Teoría</label>
                                <textarea name="descripcion" class="form-control input-custom text-dark-title" rows="8" placeholder="Escribe aquí de manera detallada los fundamentos teóricos, pasos clave e historia de este estilo..." required><?= $editando ? htmlspecialchars($estilo['descripcion']) : '' ?></textarea>
                            </div>
                        </div>

                        <div class="col-md-5 d-flex flex-column justify-content-between">
                            <div>
                                <label class="form-label label-custom">Foto o Portada del Estilo</label>
                                <div class="zona-subida-imagen p-4 text-center d-flex flex-column align-items-center justify-content-center h-100" style="min-height: 218px;">
                                    <i class="fa-regular fa-image icono-subida mb-2"></i>
                                    <input type="file" name="imagen" class="file-input-hidden" accept="image/*" id="inputImagen">
                                    <div class="mt-1">
                                        <label for="inputImagen" class="btn btn-purple-upload px-4 py-2">Seleccionar Archivo</label>
                                    </div>
                                    <span class="d-block small text-dark-muted mt-2 font-monospace" id="nombreArchivo" style="font-size: 0.72rem;">Formatos: PNG, JPG o JPEG</span>
                                    
                                    <?php if ($editando && !empty($estilo['imagen'])): ?>
                                        <div class="mt-2 pt-2 border-top border-secondary-subtle w-100 d-flex align-items-center justify-content-center gap-2">
                                            <span class="small text-dark-title fw-bold" style="font-size:0.75rem;">Actual:</span>
                                            <img src="<?= $estilo['imagen'] ?>" alt="Portada" class="img-preview-mini">
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-submit-custom w-100 py-3">
                                <?php if($editando): ?>
                                    <i class="fa-solid fa-floppy-disk me-2"></i> Guardar y Actualizar Cambios del Estilo
                                <?php else: ?>
                                    <i class="fa-solid fa-cloud-arrow-up me-2"></i> Publicar en Biblioteca de Estilos
                                <?php endif; ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('inputImagen').addEventListener('change', function(e){
    var name = e.target.files[0] ? e.target.files[0].name : "Formatos permitidos: PNG, JPG o JPEG";
    var label = document.getElementById('nombreArchivo');
    label.innerText = "Archivo listo: " + name;
    label.style.color = "#4a157d";
    label.style.fontWeight = "700";
});
</script>

<style>
    /* DESPLAZAMIENTO FLUIDO Y NATURAL */
    html, body {
        overflow-x: hidden !important;
        overflow-y: auto !important;
        height: auto !important;
    }

    /* Scrollbar estético translúcido */
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

    /* Botón Volver Minimalista */
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
        font-weight: bold; 
        text-shadow: 0 1px 2px rgba(255, 255, 255, 0.5);
    }

    .link-biblioteca-minimal {
        color: #6f2da8 !important;
        font-weight: 700;
        font-size: 0.85rem;
        text-decoration: none !important;
        transition: color 0.2s ease;
    }
    .link-biblioteca-minimal:hover {
        color: #1a0633 !important;
    }

    .alert-soft-success {
        background: rgba(25, 135, 84, 0.15) !important;
        color: #155724 !important;
        border: 1px solid rgba(25, 135, 84, 0.2) !important;
        backdrop-filter: blur(10px);
        border-radius: 14px;
        font-weight: 600;
        font-size: 0.9rem;
    }

    /* NUEVA TARJETA ANCHA (Ocupa el ancho completo como tus otras pantallas) */
    .card-quartz-wide { 
        background: rgba(255, 255, 255, 0.7); 
        backdrop-filter: blur(20px) saturate(140%);
        -webkit-backdrop-filter: blur(20px) saturate(140%);
        border: 1px solid rgba(255, 255, 255, 0.6) !important;
        border-radius: 24px !important; 
        box-shadow: 0 10px 35px rgba(31, 38, 135, 0.04) !important;
        width: 100% !important;
    }

    .label-custom {
        font-weight: 700;
        font-size: 0.72rem;
        text-transform: uppercase;
        color: #4a157d;
        letter-spacing: 0.6px;
        margin-bottom: 6px;
    }

    .input-custom {
        background: rgba(255, 255, 255, 0.5) !important;
        border: 1px solid rgba(0, 0, 0, 0.1) !important;
        border-radius: 12px !important;
        padding: 11px 16px !important;
        font-size: 0.9rem;
        font-weight: 500;
        transition: all 0.25s ease !important;
    }
    .input-custom:focus {
        background: rgba(255, 255, 255, 0.95) !important;
        border-color: #6f2da8 !important;
        box-shadow: 0 0 0 4px rgba(111, 45, 168, 0.15) !important;
    }

    .zona-subida-imagen {
        background: rgba(255, 255, 255, 0.4);
        border: 2px dashed rgba(111, 45, 168, 0.25);
        border-radius: 16px;
        transition: all 0.25s ease;
    }
    .zona-subida-imagen:hover {
        border-color: #6f2da8;
        background: rgba(255, 255, 255, 0.6);
    }
    .icono-subida {
        font-size: 1.8rem;
        color: rgba(111, 45, 168, 0.5);
    }
    .btn-purple-upload {
        background-color: rgba(111, 45, 168, 0.12);
        color: #6f2da8;
        font-weight: 700;
        font-size: 0.78rem;
        border-radius: 10px;
        border: 1px solid rgba(111, 45, 168, 0.05);
        transition: all 0.2s ease;
    }
    .btn-purple-upload:hover {
        background-color: #6f2da8;
        color: #ffffff;
    }
    .file-input-hidden { display: none; }
    
    .img-preview-mini {
        height: 38px;
        width: 38px;
        border-radius: 8px;
        object-fit: cover;
        box-shadow: 0 4px 8px rgba(0,0,0,0.08);
        border: 2px solid #fff;
    }

    .group-yt-quartz {
        background: rgba(255, 255, 255, 0.5) !important;
        border: 1px solid rgba(0, 0, 0, 0.1) !important;
        border-radius: 12px !important;
        transition: all 0.25s ease !important;
    }
    .group-yt-quartz:focus-within {
        background: rgba(255, 255, 255, 0.95) !important;
        border-color: #6f2da8 !important;
        box-shadow: 0 0 0 4px rgba(111, 45, 168, 0.15) !important;
    }
    .input-quartz-inner {
        font-size: 0.9rem !important;
        font-weight: 500;
        box-shadow: none !important;
    }

    .btn-submit-custom {
        background: #6f2da8;
        color: #ffffff;
        border: none;
        border-radius: 14px;
        font-weight: 700;
        font-size: 1rem;
        box-shadow: 0 4px 12px rgba(111, 45, 168, 0.2);
        transition: all 0.25s ease;
    }
    .btn-submit-custom:hover {
        background: #4a157d;
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(111, 45, 168, 0.3);
    }

    .text-dark-title { color: #1a0633; }
    .text-dark-muted { color: #5a5a5a !important; }
    .lowercase { text-transform: none !important; }
</style>

<?php include 'footer.php'; ?>
