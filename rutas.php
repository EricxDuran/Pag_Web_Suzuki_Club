<?php
include 'includes/db.php';
include 'includes/header.php';
?>
<div class="container">
    <h2 class="section-title" style="text-align: center; margin-bottom: 30px;"><?php echo $lang['rutas_titulo']; ?></h2>
    <div class="grid-3">
        <?php
        $sql = "SELECT * FROM rutas ORDER BY id DESC";
        $res = $conn->query($sql);
        if ($res && $res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                $img = "uploads/" . $row['foto_portada'];
                if (!file_exists($img)) $img = "https://via.placeholder.com/400x250?text=Ruta";
                ?>
                <div class="card">
                    <div class="card-img" style="background-image: url('<?php echo $img; ?>'); height: 200px;"></div>
                    <div class="card-body">
                        <h3><?php echo htmlspecialchars($row['titulo']); ?></h3>
                    </div>
                    <div class="card-footer">
                        <a href="ruta_detalle.php?id=<?php echo $row['id']; ?>" class="btn" style="width: 100%;"><?php echo $lang['rutas_btn_ver']; ?></a>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p style='text-align:center; grid-column: 1/-1;'>" . $lang['rutas_vacio'] . "</p>";
        }
        ?>
    </div>
</div>
<?php include 'includes/footer.php'; ?>