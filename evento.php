<?php
include 'includes/db.php';
include 'includes/header.php';

// Validar ID
if (!isset($_GET['id'])) { echo "<script>window.location.href='index.php';</script>"; exit(); }
$id_evento = intval($_GET['id']);

// --- LÓGICA DE ASISTENCIA ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['usuario_id']) && isset($_POST['accion_asistencia'])) {
    $uid = $_SESSION['usuario_id'];
    if ($_POST['accion_asistencia'] == 'join') {
        $conn->query("INSERT IGNORE INTO asistentes (evento_id, usuario_id) VALUES ($id_evento, $uid)");
    } elseif ($_POST['accion_asistencia'] == 'leave') {
        $conn->query("DELETE FROM asistentes WHERE evento_id=$id_evento AND usuario_id=$uid");
    }
    echo "<script>window.location.href='evento.php?id=$id_evento';</script>";
    exit();
}

// --- LÓGICA COMENTARIOS ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['usuario_id']) && isset($_POST['comentario'])) {
    $comentario = $conn->real_escape_string($_POST['comentario']);
    $uid = $_SESSION['usuario_id'];
    $conn->query("INSERT INTO comentarios (evento_id, usuario_id, comentario) VALUES ($id_evento, $uid, '$comentario')");
}
// Borrar comentario
if (isset($_GET['borrar_comentario']) && isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] == 'admin') {
    $id_com = intval($_GET['borrar_comentario']);
    $conn->query("DELETE FROM comentarios WHERE id = $id_com");
    echo "<script>window.location.href='evento.php?id=$id_evento';</script>";
}

// OBTENER EVENTO
$res = $conn->query("SELECT * FROM eventos WHERE id = $id_evento");
if ($res->num_rows == 0) { echo "<script>window.location.href='index.php';</script>"; exit(); }
$evento = $res->fetch_assoc();

// Idiomas
$titulo = ($idioma == 'ca' && !empty($evento['titulo_ca'])) ? $evento['titulo_ca'] : $evento['titulo'];
$desc = ($idioma == 'ca' && !empty($evento['descripcion_ca'])) ? $evento['descripcion_ca'] : $evento['descripcion'];
$fecha = date("d/m/Y", strtotime($evento['fecha_evento']));

// OBTENER ASISTENTES (Protegido contra fallos)
$total_asistentes = 0;
$res_asistentes = false;
// Intentamos consultar, si la tabla no existe no explotará
$sql_asistentes = "SELECT u.nombre, u.id as uid, c.foto 
                   FROM asistentes a 
                   JOIN usuarios u ON a.usuario_id = u.id 
                   LEFT JOIN coches c ON c.usuario_id = u.id 
                   WHERE a.evento_id = $id_evento";
if ($result = $conn->query($sql_asistentes)) {
    $res_asistentes = $result;
    $total_asistentes = $result->num_rows;
}

// VERIFICAR SI YO VOY
$yo_voy = false;
if (isset($_SESSION['usuario_id'])) {
    $check = $conn->query("SELECT id FROM asistentes WHERE evento_id=$id_evento AND usuario_id=" . $_SESSION['usuario_id']);
    if ($check && $check->num_rows > 0) $yo_voy = true;
}
?>

<div class="container" style="max-width: 900px;">
    <a href="index.php" style="color: #666;"><i class="fas fa-arrow-left"></i> <?php echo $lang['volver']; ?></a>

    <div class="card" style="margin-top: 20px; border-top: 5px solid var(--accent);">
        <div class="card-body">
            <div style="display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap; gap: 20px;">
                <div style="flex: 1;">
                    <span style="background: var(--primary); color: white; padding: 5px 10px; border-radius: 4px; font-size: 0.9rem;">
                        <i class="fas fa-calendar-alt"></i> <?php echo $fecha; ?>
                    </span>
                    <h1 style="margin-top: 15px; color: var(--dark);"><?php echo $titulo; ?></h1>
                    <p style="font-size: 1.1rem; margin-top: 15px; white-space: pre-wrap;"><?php echo $desc; ?></p>
                </div>

                <div style="min-width: 200px; text-align: center; background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <h4 style="margin-bottom: 10px;"><?php echo $lang['asistentes_total']; ?> <span style="font-size: 1.5rem; color: var(--accent);"><?php echo $total_asistentes; ?></span></h4>
                    
                    <?php if (isset($_SESSION['usuario_id'])): ?>
                        <form method="POST">
                            <?php if ($yo_voy): ?>
                                <input type="hidden" name="accion_asistencia" value="leave">
                                <button type="submit" class="btn btn-secondary" style="width: 100%; border-color: #dc3545; color: #dc3545;">
                                    <i class="fas fa-times"></i> <?php echo $lang['btn_borrarse']; ?>
                                </button>
                                <div style="margin-top: 5px; color: green; font-size: 0.8rem;"><i class="fas fa-check-circle"></i> Confirmado</div>
                            <?php else: ?>
                                <input type="hidden" name="accion_asistencia" value="join">
                                <button type="submit" class="btn" style="width: 100%; background: #28a745;">
                                    <i class="fas fa-car-side"></i> <?php echo $lang['btn_apuntarse']; ?>
                                </button>
                            <?php endif; ?>
                        </form>
                    <?php else: ?>
                        <a href="login.php" style="color: var(--primary); font-weight: bold; font-size: 0.9rem;"><?php echo $lang['login_para_apuntarse']; ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div style="margin-top: 30px;">
        <h3><i class="fas fa-users"></i> <?php echo $lang['asistentes_titulo']; ?></h3>
        <div style="display: flex; gap: 15px; flex-wrap: wrap; margin-top: 15px;">
            <?php 
            if ($res_asistentes && $total_asistentes > 0) {
                while ($asis = $res_asistentes->fetch_assoc()) {
                    $avatar = (!empty($asis['foto']) && file_exists("uploads/".$asis['foto'])) ? "uploads/".$asis['foto'] : "https://via.placeholder.com/60x60?text=S";
                    ?>
                    <div style="text-align: center; width: 80px;">
                        <img src="<?php echo $avatar; ?>" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary);">
                        <div style="font-size: 0.8rem; margin-top: 5px; font-weight: bold; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            <?php echo $asis['nombre']; ?>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p style='color: #888;'>" . $lang['asistentes_vacio'] . "</p>";
            }
            ?>
        </div>
    </div>

    <div style="margin-top: 40px; border-top: 1px solid #eee; padding-top: 30px;">
        <h3><i class="fas fa-comments"></i> <?php echo $lang['comentarios_titulo']; ?></h3>
        <div style="margin: 20px 0;">
            <?php
            $sql_com = "SELECT c.*, u.nombre, u.rol FROM comentarios c JOIN usuarios u ON c.usuario_id = u.id WHERE c.evento_id = $id_evento ORDER BY c.fecha ASC";
            $res_com = $conn->query($sql_com);
            if ($res_com && $res_com->num_rows > 0) {
                while ($row = $res_com->fetch_assoc()) {
                    $es_admin = ($row['rol'] == 'admin') ? 'color: var(--accent); font-weight:bold;' : '';
                    echo "<div style='background: white; padding: 15px; border-radius: 5px; margin-bottom: 10px; border-left: 3px solid #ddd;'>";
                    echo "<div style='font-size: 0.9rem; color: #666; margin-bottom: 5px;'><span style='$es_admin'>{$row['nombre']}</span> • ".date("d/m H:i", strtotime($row['fecha']))."</div>";
                    echo "<div>".nl2br(htmlspecialchars($row['comentario']))."</div>";
                    echo "</div>";
                }
            } else {
                echo "<p style='color: #888;'>" . $lang['no_comentarios'] . "</p>";
            }
            ?>
        </div>
        <?php if (isset($_SESSION['usuario_id'])): ?>
            <form method="POST"><textarea name="comentario" required placeholder="<?php echo $lang['placeholder_comentario']; ?>" style="width: 100%; padding: 15px; border: 1px solid #ccc;"></textarea><button type="submit" class="btn" style="margin-top: 10px;"><?php echo $lang['btn_comentar']; ?></button></form>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>