<?php
include 'includes/db.php';
include 'includes/header.php';

$txt_leer_mas = ($idioma == 'ca') ? "Llegir més..." : "Leer más...";
$txt_leer_menos = ($idioma == 'ca') ? "Llegir menys" : "Leer menos";
?>

<div class="container">
    <div style="text-align: center; margin-bottom: 40px;">
        <h2 class="section-title" style="margin-bottom: 10px;"><?php echo $lang['galeria_titulo']; ?></h2>
        <p style="color: #666; font-size: 1.1rem;"><?php echo $lang['galeria_subtitulo']; ?></p>
    </div>

    <div class="grid-3">
        <?php
        // Filtro: Solo muestra coches que tengan modelo, modificaciones O una foto que no sea la default
        $sql = "SELECT c.*, u.nombre FROM coches c 
                JOIN usuarios u ON c.usuario_id = u.id 
                WHERE (c.modelo != '' OR c.modificaciones != '' OR (c.foto != 'default.jpg' AND c.foto != ''))
                ORDER BY c.created_at DESC";
        
        $res = $conn->query($sql);

        if ($res && $res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                $ruta_img = "uploads/" . $row['foto'];
                if (!file_exists($ruta_img) || empty($row['foto'])) { 
                    $ruta_img = "img/default_samurai.jpg"; 
                }
                
                $txt_anyo = ($row['anyo'] > 0) ? " (" . $row['anyo'] . ")" : "";
                $mods = htmlspecialchars($row['modificaciones']);
                $id_unico = "mod_" . $row['id'];
                ?>
                
                <div class="card">
                    <div class="card-img" style="background-image: url('<?php echo $ruta_img; ?>'); height: 250px;"></div>
                    <div class="card-body">
                        <h3 style="color: var(--primary); font-size: 1.3rem; margin-bottom: 5px;">
                            <?php echo htmlspecialchars($row['modelo']) . $txt_anyo; ?>
                        </h3>
                        
                        <p style="font-size: 0.9rem; color: #888; margin-bottom: 15px;">
                            <i class="fas fa-user-circle"></i> <?php echo $lang['galeria_propietario']; ?> 
                            <strong style="color: var(--dark);"><?php echo htmlspecialchars($row['nombre']); ?></strong>
                        </p>
                        
                        <div style="background: #f9f9f9; padding: 10px; border-radius: 4px; font-size: 0.9rem; color: #555;">
                            <strong><?php echo $lang['galeria_mods_titulo']; ?></strong><br>
                            
                            <?php if(strlen($mods) > 100): ?>
                                <span id="short_<?php echo $id_unico; ?>">
                                    <?php echo substr($mods, 0, 100) . "..."; ?>
                                    <a href="#" onclick="document.getElementById('short_<?php echo $id_unico; ?>').style.display='none'; document.getElementById('full_<?php echo $id_unico; ?>').style.display='inline'; return false;" style="color: var(--accent); font-weight: bold; font-size: 0.8rem;">
                                        <?php echo $txt_leer_mas; ?>
                                    </a>
                                </span>
                                <span id="full_<?php echo $id_unico; ?>" style="display: none;">
                                    <?php echo $mods; ?>
                                    <br>
                                    <a href="#" onclick="document.getElementById('full_<?php echo $id_unico; ?>').style.display='none'; document.getElementById('short_<?php echo $id_unico; ?>').style.display='inline'; return false;" style="color: #666; font-size: 0.8rem;">
                                        <?php echo $txt_leer_menos; ?>
                                    </a>
                                </span>
                            <?php else: ?>
                                <?php echo !empty($mods) ? $mods : '---'; ?>
                            <?php endif; ?>
                        </div>

                        <?php if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] == 'admin'): ?>
                             <div style="margin-top: 15px; text-align: right;">
                                <a href="borrar_coche.php?id=<?php echo $row['id']; ?>" 
                                   onclick="return confirm('¿Borrar?');" 
                                   style="color: red; font-size: 0.8rem; font-weight: bold;">
                                    <i class="fas fa-trash-alt"></i> <?php echo $lang['txt_borrar']; ?>
                                </a>
                             </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<div style='grid-column: 1 / -1; text-align: center; padding: 40px; background: white; border-radius: 8px;'>";
            echo "<i class='fas fa-car-crash' style='font-size: 3rem; color: #ccc; margin-bottom: 15px;'></i>";
            echo "<p>" . $lang['galeria_sin_coches'] . "</p>";
            echo "</div>";
        }
        ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>