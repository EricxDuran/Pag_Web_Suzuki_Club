<?php 
include 'includes/header.php'; 
include_once 'includes/db.php'; 

// --- LÓGICA DE BORRADO ---
if (isset($_POST['borrar_evento'])) {
    $id_ev = intval($_POST['evento_id']);
    $user_id = $_SESSION['usuario_id'];
    
    // Solo permite borrar si el ID del usuario coincide con el creador de la ruta
    $stmt = $conn->prepare("DELETE FROM eventos WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("ii", $id_ev, $user_id);
    $stmt->execute();
    $stmt->close();
    
    echo "<script>window.location.href='index.php';</script>";
    exit();
}
?>

<section class="hero">
    <div class="hero-content">
        <h1><?php echo $lang['hero_titulo']; ?></h1>
        <p><?php echo $lang['hero_desc']; ?></p>
    </div>
</section>

<div class="container" id="tablon">
    <div style="display: flex; flex-wrap: wrap; gap: 30px; align-items: flex-start;">
        
        <div class="notice-board" style="flex: 2; min-width: 300px;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--accent); padding-bottom: 10px; margin-bottom: 20px;">
                <h3 style="margin: 0;">
                    <i class="fas fa-bullhorn"></i> <?php echo $lang['tablon_titulo']; ?>
                </h3>
                
                <?php $destino_ruta = isset($_SESSION['usuario_id']) ? 'registrar_ruta.php' : 'login.php'; ?>
                <a href="<?php echo $destino_ruta; ?>" class="btn" style="padding: 6px 12px; font-size: 0.85rem; background-color: var(--primary);">
                    <i class="fas fa-plus-circle"></i> <?php echo isset($lang['btn_registrar_ruta']) ? $lang['btn_registrar_ruta'] : 'Registrar Ruta'; ?>
                </a>
            </div>

            <?php
            $sql = "SELECT * FROM eventos WHERE fecha_evento >= CURDATE() ORDER BY fecha_evento ASC LIMIT 3";
            $resultado = $conn->query($sql);

            if ($resultado && $resultado->num_rows > 0) {
                while($evento = $resultado->fetch_assoc()) {
                    $fechaObj = new DateTime($evento['fecha_evento']);
                    $dia = $fechaObj->format('d');
                    $mes = $fechaObj->format('M');

                    $sql_count = "SELECT COUNT(*) as total FROM asistentes WHERE evento_id = " . $evento['id'];
                    $res_count = $conn->query($sql_count);
                    $num_asistentes = ($res_count) ? $res_count->fetch_assoc()['total'] : 0;

                    if ($idioma == 'ca' && !empty($evento['titulo_ca'])) {
                        $titulo_mostrar = $evento['titulo_ca'];
                        $desc_mostrar = $evento['descripcion_ca'];
                    } else {
                        $titulo_mostrar = $evento['titulo'];
                        $desc_mostrar = $evento['descripcion'];
                    }
                    ?>

                    <div class="event-row">
                        <div class="event-date">
                            <span><?php echo $dia; ?></span><?php echo strtoupper($mes); ?>
                        </div>
                        <div class="event-info" style="width: 100%;">
                            <h4><?php echo $titulo_mostrar; ?></h4>
                            <p><?php echo $desc_mostrar; ?></p>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px; border-top: 1px dashed #eee; padding-top: 8px;">
                                <a href="evento.php?id=<?php echo $evento['id']; ?>" style="color: var(--accent); font-size: 0.9rem; font-weight: bold;">
                                    <i class="fas fa-comment-dots"></i> <?php echo $lang['ver_detalles']; ?>
                                </a>

                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $evento['usuario_id']): ?>
                                        <form method="POST" onsubmit="return confirm('<?php echo $lang['confirm_borrar_ruta']; ?>');" style="margin:0;">
                                            <input type="hidden" name="evento_id" value="<?php echo $evento['id']; ?>">
                                            <button type="submit" name="borrar_evento" style="background:none; border:none; color:#e74c3c; cursor:pointer; font-size:0.85rem; font-weight:bold;">
                                                <i class="fas fa-trash-alt"></i> <?php echo $lang['btn_borrar']; ?>
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <span style="font-size: 0.85rem; color: #666; background: #f0f0f0; padding: 2px 8px; border-radius: 10px;">
                                        <i class="fas fa-car"></i> 
                                        <b><?php echo $num_asistentes; ?></b> 
                                        <?php echo ($idioma == 'ca') ? 'Confirmats' : 'Confirmados'; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php
                }
            } else {
                $msg_vacio = ($idioma == 'ca') ? "No hi ha rutes programades properament." : "No hay rutas programadas próximamente.";
                echo "<p style='padding: 20px; text-align: center; color: #666;'>$msg_vacio</p>";
            }
            ?>
        </div>

        <div class="notice-board" style="flex: 1.3; min-width: 300px;">
            <h3 style="border-bottom: 2px solid var(--primary); padding-bottom: 10px; margin-bottom: 20px; color: var(--primary);">
                <i class="fas fa-handshake"></i> <?php echo isset($lang['colaboradores_titulo']) ? $lang['colaboradores_titulo'] : 'Webs Colaboradoras'; ?>
            </h3>
            
            <ul style="list-style: none; padding: 0;">
                <li style="margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                    <a href="https://www.cartago4x4.es" target="_blank" style="color: var(--dark); font-weight: bold; text-decoration: none; font-size: 1.1rem;">
                        <i class="fas fa-external-link-alt" style="font-size: 0.9rem; color: var(--accent);"></i> Cartago 4x4
                    </a>
                    <p style="margin: 5px 0 0 0; font-size: 0.95rem; color: #555;">
                        <?php echo isset($lang['colaboradores_cartago']) ? $lang['colaboradores_cartago'] : '3% Dto. usando el código <b>SAMURAITEAM</b> en toda la tienda.'; ?>
                    </p>
                </li>
                
                <li style="margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                    <a href="https://sogo4x4.com" target="_blank" style="color: var(--dark); font-weight: bold; text-decoration: none; font-size: 1.1rem;">
                        <i class="fas fa-external-link-alt" style="font-size: 0.9rem; color: var(--accent);"></i> SOGO4X4
                    </a>
                    <p style="margin: 5px 0 0 0; font-size: 0.95rem; color: #555;">
                        <?php echo $lang['colaboradores_sogo']; ?>
                    </p>
                </li>
            </ul>
        </div>
    </div>
</div>

<section class="container">
    <div class="grid-3">
        <div class="card">
            <div class="card-img" style="background-image: url('img/manuales.jpg');"></div>
            <div class="card-body">
                <h3><i class="fas fa-book"></i> <?php echo $lang['card1_titulo']; ?></h3>
                <p><?php echo $lang['card1_desc']; ?></p>
            </div>
            <div class="card-footer">
                <a href="manuales.php" class="link-arrow"><?php echo $lang['card1_link']; ?> &rarr;</a>
            </div>
        </div>

        <div class="card">
            <div class="card-img" style="background-image: url('img/rutas.jpg');"></div>
            <div class="card-body">
                <h3><i class="fas fa-map-marked-alt"></i> <?php echo $lang['card2_titulo']; ?></h3>
                <p><?php echo $lang['card2_desc']; ?></p>
            </div>
            <div class="card-footer">
                <a href="rutas.php" class="link-arrow"><?php echo $lang['card2_link']; ?> &rarr;</a>
            </div>
        </div>

        <div class="card">
            <div class="card-img" style="background-image: url('img/garage.jpg');"></div>
            <div class="card-body">
                <h3><i class="fas fa-camera"></i> <?php echo $lang['card3_titulo']; ?></h3>
                <p><?php echo $lang['card3_desc']; ?></p>
            </div>
            <div class="card-footer">
                <a href="miembros.php" class="link-arrow"><?php echo $lang['card3_link']; ?> &rarr;</a>
            </div>
        </div>
    </div>
</section>

<section style="background-color: #e9ecef; padding: 60px 20px; text-align: center;">
    <div style="max-width: 800px; margin: 0 auto;">
        <h2 style="color: var(--primary); margin-bottom: 20px;"><?php echo $lang['historia_titulo']; ?></h2>
        <p style="font-size: 1.2rem; margin-bottom: 30px;"><?php echo $lang['historia_desc']; ?></p>
        <a href="historia.php" class="btn btn-secondary"><?php echo $lang['historia_btn']; ?></a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>