<?php
include 'includes/db.php';
include 'includes/header.php';

// SEGURIDAD: Solo Admins
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] != 'admin') {
    echo "<script>window.location.href='index.php';</script>";
    exit();
}
?>

<div class="container">
    <h1 style="border-bottom: 4px solid var(--accent); display: inline-block; margin-bottom: 30px;">
        <i class="fas fa-cogs"></i> Panel de Control
    </h1>

    <div class="grid-3" style="margin-bottom: 50px;">
        <div class="card" style="border-bottom-color: var(--accent);">
            <div class="card-body" style="text-align: center;">
                <i class="fas fa-calendar-plus" style="font-size: 3rem; color: var(--accent); margin-bottom: 15px;"></i>
                <h3>Nuevo Evento</h3>
                <a href="crear_evento.php" class="btn" style="margin-top: 10px; width: 100%;">Publicar</a>
            </div>
        </div>

        <div class="card" style="border-bottom-color: var(--primary);">
            <div class="card-body" style="text-align: center;">
                <i class="fas fa-file-upload" style="font-size: 3rem; color: var(--primary); margin-bottom: 15px;"></i>
                <h3>Registrar Manual</h3>
                <a href="subir_manual.php" class="btn" style="margin-top: 10px; width: 100%;">Gestionar Archivos FTP</a>
            </div>
        </div>
    </div>

    <h2 style="color: var(--dark); margin-bottom: 20px;"><i class="fas fa-list"></i> Gestionar Eventos</h2>
    <div class="card" style="margin-bottom: 40px;">
        <div class="card-body">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #eee; text-align: left;"><th style="padding: 10px;">Fecha</th><th style="padding: 10px;">Título</th><th style="padding: 10px;">Acciones</th></tr>
                </thead>
                <tbody>
                    <?php
                    $res = $conn->query("SELECT * FROM eventos ORDER BY fecha_evento DESC");
                    if ($res->num_rows > 0) {
                        while($row = $res->fetch_assoc()) {
                            echo "<tr style='border-bottom: 1px solid #ddd;'>
                                    <td style='padding: 10px;'>".date("d/m/Y", strtotime($row['fecha_evento']))."</td>
                                    <td style='padding: 10px;'>{$row['titulo']}</td>
                                    <td style='padding: 10px;'>
                                        <a href='editar_evento.php?id={$row['id']}' style='color: var(--primary); margin-right: 10px;'><i class='fas fa-edit'></i></a>
                                        <a href='borrar_evento.php?id={$row['id']}' onclick=\"return confirm('¿Borrar?');\" style='color: red;'><i class='fas fa-trash-alt'></i></a>
                                    </td>
                                  </tr>";
                        }
                    } else { echo "<tr><td colspan='3' style='padding: 10px;'>No hay eventos.</td></tr>"; }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <h2 style="color: var(--dark); margin-bottom: 20px;"><i class="fas fa-book"></i> Gestionar Biblioteca</h2>
    <div class="card" style="margin-bottom: 40px;">
        <div class="card-body">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #eee; text-align: left;"><th style="padding: 10px;">Archivo</th><th style="padding: 10px;">Título</th><th style="padding: 10px;">Acciones</th></tr>
                </thead>
                <tbody>
                    <?php
                    $res = $conn->query("SELECT * FROM manuales ORDER BY id DESC");
                    if ($res->num_rows > 0) {
                        while($row = $res->fetch_assoc()) {
                            echo "<tr style='border-bottom: 1px solid #ddd;'>
                                    <td style='padding: 10px; font-size:0.8rem; color:#666;'>{$row['archivo']}</td>
                                    <td style='padding: 10px;'>{$row['titulo']}</td>
                                    <td style='padding: 10px;'>
                                        <a href='borrar_manual.php?id={$row['id']}' onclick=\"return confirm('¿Borrar este manual?');\" style='color: red;'><i class='fas fa-trash-alt'></i></a>
                                    </td>
                                  </tr>";
                        }
                    } else { echo "<tr><td colspan='3' style='padding: 10px;'>No hay manuales subidos.</td></tr>"; }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <h2 style="color: var(--dark); margin-bottom: 20px;"><i class="fas fa-map"></i> <?php echo $lang['admin_rutas']; ?></h2>
    <div class="card">
        <div class="card-body">
            <form action="crear_ruta.php" method="POST" enctype="multipart/form-data" style="background: #f9f9f9; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                <h4><?php echo $lang['admin_ruta_nueva']; ?></h4>
                <input type="text" name="titulo" placeholder="<?php echo $lang['admin_ruta_titulo']; ?>" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
                <label><?php echo $lang['admin_ruta_portada']; ?>:</label>
                <input type="file" name="foto_portada" accept="image/*" required style="margin-bottom: 10px;">
                <button type="submit" class="btn"><i class="fas fa-upload"></i> Publicar Ruta</button>
            </form>

            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="background: #eee; border-bottom: 2px solid #ddd;">
                        <th style="padding: 10px;">ID</th>
                        <th style="padding: 10px;">Ruta</th>
                        <th style="padding: 10px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $res_rutas = $conn->query("SELECT * FROM rutas ORDER BY id DESC");
                    if ($res_rutas->num_rows > 0) {
                        while($r = $res_rutas->fetch_assoc()): ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 10px;"><?php echo $r['id']; ?></td>
                                <td style="padding: 10px;"><?php echo $r['titulo']; ?></td>
                                <td style="padding: 10px;">
                                    <a href="admin_fotos_ruta.php?id=<?php echo $r['id']; ?>" class="btn btn-secondary" style="padding: 5px 10px; font-size: 0.8rem; margin-right: 10px;">
                                        <i class="fas fa-images"></i> <?php echo $lang['admin_gestionar_fotos']; ?>
                                    </a>
                                    <a href="borrar_ruta.php?id=<?php echo $r['id']; ?>" onclick="return confirm('¿Borrar ruta entera?');" style="color: red;"><i class="fas fa-trash-alt"></i></a>
                                </td>
                            </tr>
                        <?php endwhile;
                    } else { echo "<tr><td colspan='3' style='padding: 10px;'>No hay rutas creadas.</td></tr>"; }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php include 'includes/footer.php'; ?>