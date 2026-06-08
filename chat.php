<?php
include 'header.php';
include 'conexion.php';

// Verificar que haya una sesión activa
if (!isset($_SESSION['id']) || !isset($_SESSION['rol'])) {
    header("Location: index.php");
    exit;
}

$id_sesion = $_SESSION['id'];
$rol_sesion = $_SESSION['rol'];

$usuario_conversacion_id = null;
$nombre_conversacion = "";
$alumnos_chat = null;     
$profesores_chat = null;

if ($rol_sesion === 'alumno') {
    // 1. SI ES ALUMNO: Obtener la lista de todos los profesores disponibles
    $profesores_chat = $conn->query("SELECT id, nombre FROM usuarios WHERE rol = 'profesor' ORDER BY nombre ASC");
    
    // --- CAMBIO AQUÍ: Solo abrimos el chat si viene explícitamente en la URL ---
    if (isset($_GET['con_profe'])) {
        $usuario_conversacion_id = intval($_GET['con_profe']);
        $profe_act = $conn->query("SELECT nombre FROM usuarios WHERE id = $usuario_conversacion_id")->fetch_assoc();
        $nombre_conversacion = $profe_act['nombre'] ?? "Profesor";
    }
} else {
    // 2. SI ES PROFESOR: Obtener la lista de alumnos que tienen mensajes con ÉL
    $alumnos_chat = $conn->query("SELECT DISTINCT u.id, u.nombre FROM usuarios u 
                                  INNER JOIN chat_mensajes cm ON (u.id = cm.id_emisor OR u.id = cm.id_receptor)
                                  WHERE u.role = 'alumno' AND (cm.id_emisor = $id_sesion OR cm.id_receptor = $id_sesion)
                                  ORDER BY u.nombre ASC");
    
    // --- CAMBIO AQUÍ: Solo abrimos el chat si viene explícitamente en la URL ---
    if (isset($_GET['con_alumno'])) {
        $usuario_conversacion_id = intval($_GET['con_alumno']);
        $alumno_act = $conn->query("SELECT nombre FROM usuarios WHERE id = $usuario_conversacion_id")->fetch_assoc();
        $nombre_conversacion = $alumno_act['nombre'] ?? "Alumno";
    }
}

// 3. BUSCAR LOS MENSAJES SOLO SI HAY ALGUIEN SELECCIONADO
$mensajes = [];
if ($usuario_conversacion_id) {
    $mensajes = $conn->query("SELECT * FROM chat_mensajes 
                              WHERE (id_emisor = $id_sesion AND id_receptor = $usuario_conversacion_id)
                                 OR (id_emisor = $usuario_conversacion_id AND id_receptor = $id_sesion)
                              ORDER BY fecha_envio ASC");
}
?>

<!-- Contenedor expandido para el ecosistema visual de la app -->
<div class="container-fluid px-md-5 my-4">
    
    <!-- Fila del Botón de Regreso -->
    <div class="mb-4 px-2">
        <a href="<?= $rol_sesion === 'profesor' ? 'panel_profesor.php' : 'panel_alumno.php' ?>" class="btn-back-custom">
            <i class="fa-solid fa-arrow-left me-1"></i> Volver al Inicio
        </a>
    </div>

    <div class="row g-4">
        
        <!-- Columna Izquierda: Listado de Contactos (Estilo Cuarzo Fino) -->
        <div class="col-lg-4 col-md-5">
            <div class="card card-quartz-chat border-0 p-4 h-100">
                <span class="text-perfil-lateral text-uppercase mb-1 d-block">
                    <i class="fa-solid fa-layer-group me-1"></i> Bandeja de Entrada
                </span>
                <h5 class="fw-bold mb-4 text-dark-title">
                    <i class="fa-solid fa-comments me-2" style="color: #6f2da8;"></i> 
                    <?= $rol_sesion === 'alumno' ? 'Tus Profesores' : 'Consultas Alumnos' ?>
                </h5>
                
                <div class="list-group list-group-flush shadow-none container-scroll-contactos">
                    
                    <?php if ($rol_sesion === 'alumno'): ?>
                        <?php if ($profesores_chat->num_rows > 0): ?>
                            <?php while($pr = $profesores_chat->fetch_assoc()): 
                                $id_pr = $pr['id'];
                                $ultimo_msg = $conn->query("SELECT id_emisor FROM chat_mensajes 
                                                            WHERE (id_emisor = $id_sesion AND id_receptor = $id_pr) 
                                                               OR (id_emisor = $id_pr AND id_receptor = $id_sesion) 
                                                            ORDER BY id DESC LIMIT 1")->fetch_assoc();
                                $tiene_notificacion = ($ultimo_msg && $ultimo_msg['id_emisor'] == $id_pr);
                            ?>
                                <a href="chat.php?con_profe=<?= $pr['id'] ?>" 
                                   class="list-group-item list-group-item-action contact-item-custom border-0 p-3 mb-2 d-flex align-items-center justify-content-between <?= $usuario_conversacion_id == $pr['id'] ? 'item-activo' : 'item-inactivo' ?>">
                                   <div class="d-flex align-items-center text-truncate me-2">
                                        <div class="avatar-icon-mini-chat me-2.5">
                                            <i class="fa-solid fa-user-tie"></i>
                                        </div>
                                        <span class="text-truncate">Profe. <?= htmlspecialchars($pr['nombre']) ?></span>
                                   </div>
                                    
                                    <?php if ($tiene_notificacion && $usuario_conversacion_id != $pr['id']): ?>
                                        <span class="badge-notify-pulse">&nbsp;</span>
                                    <?php else: ?>
                                        <i class="fa-solid fa-chevron-right small-chevron-chat opacity-50"></i>
                                    <?php endif; ?>
                                </a>
                            <?php endwhile; ?>
                        <?php endif; ?>

                    <?php else: ?>
                        <?php if ($alumnos_chat->num_rows > 0): ?>
                            <?php while($al = $alumnos_chat->fetch_assoc()): 
                                $id_al = $al['id'];
                                $ultimo_msg = $conn->query("SELECT id_emisor FROM chat_mensajes 
                                                            WHERE (id_emisor = $id_sesion AND id_receptor = $id_al) 
                                                               OR (id_emisor = $id_al AND id_receptor = $id_sesion) 
                                                            ORDER BY id DESC LIMIT 1")->fetch_assoc();
                                $tiene_notificacion = ($ultimo_msg && $ultimo_msg['id_emisor'] == $id_al);
                            ?>
                                <a href="chat.php?con_alumno=<?= $al['id'] ?>" 
                                   class="list-group-item list-group-item-action contact-item-custom border-0 p-3 mb-2 d-flex align-items-center justify-content-between <?= $usuario_conversacion_id == $al['id'] ? 'item-activo' : 'item-inactivo' ?>">
                                   <div class="d-flex align-items-center text-truncate me-2">
                                        <div class="avatar-icon-mini-chat me-2.5">
                                            <i class="fa-solid fa-user-graduate"></i>
                                        </div>
                                        <span class="text-truncate"><?= htmlspecialchars($al['nombre']) ?></span>
                                   </div>
                                    
                                    <?php if ($tiene_notificacion && $usuario_conversacion_id != $al['id']): ?>
                                        <span class="badge-notify-pulse">&nbsp;</span>
                                    <?php else: ?>
                                        <i class="fa-solid fa-chevron-right small-chevron-chat opacity-50"></i>
                                    <?php endif; ?>
                                </a>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fa-regular fa-folder-open text-muted opacity-30 fs-1 mb-2"></i>
                                <p class="text-dark-muted small">No has recibido consultas de alumnos todavía.</p>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                </div>
            </div>
        </div>

        <!-- Columna Derecha: Contenedor Principal de la Conversación -->
        <div class="col-lg-8 col-md-7">
            <div class="card card-quartz-chat border-0 d-flex flex-column pane-chat-box">
                
                <?php if ($usuario_conversacion_id): ?>
                    <!-- Cabecera del Chat Activo -->
                    <div class="p-4 chat-header-border d-flex align-items-center Header-chat-glass">
                        <div class="avatar-active-chat me-3 shadow-sm">
                            <i class="fa-solid <?= $rol_sesion === 'alumno' ? 'fa-user-tie' : 'fa-user-graduate' ?> fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark-title"><?= htmlspecialchars($nombre_conversacion) ?></h5>
                            <small class="text-muted-custom text-uppercase fw-bold">
                                <span class="online-indicator-dot me-1"></span> Canal de Consulta Privado
                            </small>
                        </div>
                    </div>

                    <!-- Panel Central de Mensajes (Scrolleable) -->
                    <div class="flex-grow-1 p-4 overflow-auto dynamic-chat-body" id="cuerpoChat">
                        <?php if ($mensajes && $mensajes->num_rows > 0): ?>
                            <?php while($m = $mensajes->fetch_assoc()): 
                                $soy_yo = ($m['id_emisor'] == $id_sesion);
                            ?>
                                <div class="d-flex mb-3 <?= $soy_yo ? 'justify-content-end' : 'justify-content-start' ?>">
                                    <div class="msg-bubble-custom p-3 text-break <?= $soy_yo ? 'bubble-sender' : 'bubble-receiver' ?>">
                                        <p class="mb-1 m-0 text-msg-inner"><?= nl2br(htmlspecialchars($m['mensaje'])) ?></p>
                                        <small class="d-block text-end time-stamp-chat">
                                            <?= date('H:i', strtotime($m['fecha_envio'])) ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <!-- Vacío de Mensajes -->
                            <div class="text-center py-5 my-auto text-dark-muted">
                                <div class="mb-3">
                                    <i class="fa-regular fa-comments fa-3x opacity-25" style="color: #6f2da8;"></i>
                                </div>
                                <p class="mb-0 fw-bold text-dark-title">¡Inicia la conversación!</p>
                                <small class="small">Escribe tu duda o consulta en la barra inferior de forma segura.</small>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Barra de Entrada Inferior -->
                    <div class="p-3 chat-footer-border footer-chat-glass">
                        <form action="enviar_mensaje.php" method="POST" class="d-flex gap-2 m-0 align-items-center">
                            <input type="hidden" name="id_receptor" value="<?= $usuario_conversacion_id ?>">
                            <input type="text" name="mensaje" class="form-control input-chat-quartz py-2.5 px-3" placeholder="Escribe un mensaje aquí..." required autocomplete="off">
                            <button type="submit" class="btn btn-send-quartz-chat">
                                <i class="fa-solid fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>

                <?php else: ?>
                    <!-- Estado Inactivo General (Ninguna Conversación Seleccionada) -->
                    <div class="d-flex flex-column align-items-center justify-content-center h-100 p-5 text-center">
                        <div class="mb-4 d-inline-block p-4 rounded-circle circle-icon-chat-empty">
                            <i class="fa-solid fa-comments fa-4x animate-pulse-slow"></i>
                        </div>
                        <h4 class="fw-bold text-dark-title mb-2">Bandeja de Entrada Privada</h4>
                        <p class="text-dark-muted small px-md-5 mx-auto" style="max-width: 440px;">
                            Selecciona una conversación de la lista lateral izquierda para revisar los mensajes académicos o enviar una nueva consulta técnica.
                        </p>
                    </div>
                <?php endif; ?>

            </div>
        </div>

    </div>
</div>

<script>
// Desplazamiento automático al final de la ventana de chat
const cuerpoChat = document.getElementById('cuerpoChat');
if(cuerpoChat) { cuerpoChat.scrollTop = cuerpoChat.scrollHeight; }
</script>

<style>
    /* VARIABLES Y FONDOS INTEGRADOS */
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

    /* Tarjetas de Cuarzo de Chat */
    .card-quartz-chat {
        background: rgba(255, 255, 255, 0.75); 
        backdrop-filter: blur(15px) saturate(130%);
        -webkit-backdrop-filter: blur(15px) saturate(130%);
        border: 1px solid rgba(255, 255, 255, 0.5) !important;
        border-radius: 24px !important; 
        box-shadow: 0 8px 32px rgba(31, 38, 135, 0.04) !important;
    }
    .pane-chat-box {
        height: 580px;
        overflow: hidden;
    }

    .text-perfil-lateral {
        color: #6f2da8 !important;
        font-size: 0.72rem; 
        font-weight: 700;
        letter-spacing: 0.8px;
    }
    .text-dark-title { color: #1a0633 !important; }
    .text-dark-muted { color: #5a5a5a !important; }
    .text-muted-custom { color: #7b2cbf !important; font-size: 0.68rem; letter-spacing: 0.5px;}

    /* Listado de Contactos */
    .container-scroll-contactos {
        max-height: 440px;
        overflow-y: auto;
        padding-right: 2px;
    }
    .contact-item-custom {
        border-radius: 14px !important;
        font-weight: 600;
        font-size: 0.88rem;
        transition: all 0.25s ease !important;
    }
    .item-activo { 
        background: rgba(111, 45, 168, 0.12) !important; 
        color: #6f2da8 !important;
        border: 1px solid rgba(111, 45, 168, 0.15) !important;
    }
    .item-inactivo { 
        background: rgba(255, 255, 255, 0.4); 
        color: #495057; 
        border: 1px solid rgba(0, 0, 0, 0.03) !important;
    }
    .item-inactivo:hover { 
        background: rgba(111, 45, 168, 0.05) !important; 
        color: #6f2da8 !important; 
    }

    /* Avatares e Indicadores */
    .avatar-icon-mini-chat {
        width: 30px; height: 30px;
        border-radius: 50%;
        background: rgba(111, 45, 168, 0.08);
        color: #6f2da8;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
    }
    .avatar-active-chat {
        background-color: #6f2da8; 
        color: white;
        width: 44px; height: 44px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
    }
    .small-chevron-chat { font-size: 0.7rem; }
    .online-indicator-dot {
        height: 7px; width: 7px;
        background-color: #2ec4b6;
        border-radius: 50%;
        display: inline-block;
    }

    /* Notificación de Pulso Roja */
    .badge-notify-pulse {
        width: 9px; height: 9px;
        background-color: #e50914 !important;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 0 0 rgba(229, 9, 20, 0.4);
        animation: pulseRed 1.6s infinite;
    }
    @keyframes pulseRed {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(229, 9, 20, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(229, 9, 20, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(229, 9, 20, 0); }
    }

    /* Estilos de Cuerpo del Panel de Chat */
    .chat-header-border { border-bottom: 1px solid rgba(111, 45, 168, 0.08) !important; }
    .chat-footer-border { border-top: 1px solid rgba(111, 45, 168, 0.08) !important; }
    .Header-chat-glass { background: rgba(255, 255, 255, 0.3) !important; }
    .footer-chat-glass { background: rgba(255, 255, 255, 0.4) !important; }
    
    .dynamic-chat-body {
        background-color: rgba(252, 250, 255, 0.45) !important;
    }

    /* Burbujas de Mensajes Asimétricas */
    .msg-bubble-custom {
        max-width: 72%;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
    }
    .bubble-sender {
        border-radius: 16px 16px 0px 16px;
        background: linear-gradient(135deg, #7b2cbf, #6f2da8);
        color: #ffffff;
    }
    .bubble-receiver {
        border-radius: 16px 16px 16px 0px;
        background: rgba(255, 255, 255, 0.85);
        color: #2b2b2b;
        border: 1px solid rgba(0, 0, 0, 0.04);
    }
    .text-msg-inner { font-size: 0.92rem; line-height: 1.45; font-weight: 500; }
    .time-stamp-chat { font-size: 0.68rem; opacity: 0.65; margin-top: 4px; font-weight: 600; }

    /* Inputs y Botón de envío */
    .input-chat-quartz {
        border: 1px solid rgba(0, 0, 0, 0.08) !important;
        background: rgba(255, 255, 255, 0.8) !important;
        border-radius: 14px !important;
        font-size: 0.88rem;
        font-weight: 500;
        box-shadow: none !important;
        transition: all 0.2s ease;
    }
    .input-chat-quartz:focus {
        background: #ffffff !important;
        border-color: #6f2da8 !important;
        box-shadow: 0 0 0 3px rgba(111, 45, 168, 0.12) !important;
    }
    .btn-send-quartz-chat {
        background: #6f2da8 !important;
        color: #ffffff !important;
        border-radius: 14px !important;
        padding: 10px 18px !important;
        border: none;
        transition: all 0.2s ease;
    }
    .btn-send-quartz-chat:hover {
        background: #4a157d !important;
        box-shadow: 0 4px 10px rgba(111, 45, 168, 0.2);
    }

    /* Estado vacío central */
    .circle-icon-chat-empty {
        background: rgba(111, 45, 168, 0.06); 
        color: #6f2da8;
    }
    .animate-pulse-slow {
        animation: pulseSlow 2.5s infinite ease-in-out;
    }
    @keyframes pulseSlow {
        0%, 100% { opacity: 0.25; transform: scale(1); }
        50% { opacity: 0.55; transform: scale(1.05); }
    }

    /* Scrollbars internas pulidas */
    .container-scroll-contactos::-webkit-scrollbar,
    .dynamic-chat-body::-webkit-scrollbar { 
        width: 5px; 
    }
    .container-scroll-contactos::-webkit-scrollbar-track,
    .dynamic-chat-body::-webkit-scrollbar-track { 
        background: transparent; 
    }
    .container-scroll-contactos::-webkit-scrollbar-thumb,
    .dynamic-chat-body::-webkit-scrollbar-thumb { 
        background-color: rgba(111, 45, 168, 0.15); 
        border-radius: 10px; 
    }
</style>

<?php include 'footer.php'; ?>


