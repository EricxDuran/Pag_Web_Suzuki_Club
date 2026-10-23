<?php
include 'includes/db.php';
include 'includes/header.php';

// Títulos según idioma
$titulo_seccion = ($idioma == 'ca') ? "Biblioteca Tècnica" : "Biblioteca Técnica";
$subtitulo = ($idioma == 'ca') ? "Manuals oficials i documentació per al teu Suzuki." : "Manuales oficiales y documentación para tu Suzuki.";
$btn_descarga = ($idioma == 'ca') ? "Descarregar" : "Descargar";
$sin_manuales = ($idioma == 'ca') ? "Encara no hi ha manuals disponibles." : "Aún no hay manuales disponibles.";
?>

<div class="container">
    <div style="text-align: center; margin-bottom: 40px;">
        <h2 class="section-title" style="margin-bottom: 10px;">
            <i class="fas fa-book-reader"></i> <?php echo $titulo_seccion; ?>
        </h2>
        <p style="color: #666; font-size: 1.1rem;"><?php echo $subtitulo; ?></p>
    </div>

    <div class="grid-3">
        <?php
        $sql = "SELECT * FROM manuales ORDER BY id DESC";
        $res = $conn->query($sql);

        if ($res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                // Seleccionar idioma
                $t = ($idioma == 'ca' && !empty($row['titulo_ca'])) ? $row['titulo_ca'] : $row['titulo'];
                $d = ($idioma == 'ca' && !empty($row['descripcion_ca'])) ? $row['descripcion_ca'] : $row['descripcion'];
                $archivo = "descargas/" . $row['archivo'];

                // Icono según extensión (Visual)
                $ext = pathinfo($archivo, PATHINFO_EXTENSION);
                $icono = "fa-file-alt"; // Por defecto
                $color_fondo = "#95a5a6"; // Gris
                
                if(strtolower($ext) == 'pdf') { 
                    $icono = "fa-file-pdf"; 
                    $color_fondo = "#e74c3c"; // Rojo
                } elseif(in_array(strtolower($ext), ['zip', 'rar'])) {
                    $icono = "fa-file-archive";
                    $color_fondo = "#f39c12"; // Naranja
                }
                ?>
                
                <div class="card">
                    <div style="background: <?php echo $color_fondo; ?>; height: 150px; display: flex; align-items: center; justify-content: center; color: white; font-size: 4rem;">
                        <i class="fas <?php echo $icono; ?>"></i>
                    </div>
                    <div class="card-body">
                        <h3 style="font-size: 1.2rem; min-height: 50px;"><?php echo $t; ?></h3>
                        <p style="font-size: 0.9rem; color: #666; margin-bottom: 15px; min-height: 60px;">
                            <?php echo $d; ?>
                        </p>
                        <a href="<?php echo $archivo; ?>" target="_blank" class="btn" style="width: 100%; text-align: center; background: var(--dark);">
                            <i class="fas fa-download"></i> <?php echo $btn_descarga; ?>
                        </a>
                    </div>
                </div>

                <?php
            }
        } else {
            echo "<div style='grid-column: 1/-1; text-align:center; padding: 40px; background:#fff;'>";
            echo "<i class='fas fa-inbox' style='font-size:3rem; color:#ccc;'></i>";
            echo "<p style='margin-top:10px; color:#666;'>$sin_manuales</p>";
            echo "</div>";
        }
        ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>