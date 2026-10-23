<?php
include 'includes/db.php';
include 'includes/header.php';

// --- LÓGICA DE PRECIOS ---
$coste_camiseta = 7.00; 
$coste_cliche = 30.00; 

// Calcular totales
$sql_stats = "SELECT SUM(cantidad) as total_camisetas FROM pedidos";
$res_stats = $conn->query($sql_stats);
$row_stats = $res_stats->fetch_assoc();
$total_camisetas = $row_stats['total_camisetas'] ?? 0;

// Calcular precio dinámico
if ($total_camisetas > 0) {
    $parte_cliche = $coste_cliche / $total_camisetas;
    $precio_actual = $coste_camiseta + $parte_cliche;
} else {
    $precio_actual = $coste_camiseta + $coste_cliche; 
}

// --- PROCESAR FORMULARIO ---
$msg = "";
$tipo_msg = ""; 

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['usuario_id'])) {
    
    // BORRAR
    if (isset($_POST['borrar_id'])) {
        $id_borrar = intval($_POST['borrar_id']);
        $mi_id = $_SESSION['usuario_id'];
        $mi_rol = $_SESSION['usuario_rol'];
        $sql_borrar = "DELETE FROM pedidos WHERE id=$id_borrar AND (usuario_id=$mi_id OR '$mi_rol'='admin')";
        if($conn->query($sql_borrar)){ echo "<script>window.location.href='camisetas.php';</script>"; }
    } 
    // NUEVO PEDIDO
    else {
        $nombre_pedido = trim($conn->real_escape_string($_POST['nombre_pedido'])); 
        $talla = $conn->real_escape_string($_POST['talla']);
        $cantidad = intval($_POST['cantidad']);
        $usuario_id = $_SESSION['usuario_id'];

        // CORRECCIÓN: Comprobamos si el nombre existe Y SI ES DE OTRA PERSONA
        $sql_check = "SELECT id FROM pedidos WHERE nombre_pedido = '$nombre_pedido' AND usuario_id != $usuario_id";
        $check = $conn->query($sql_check);
        
        if ($check->num_rows > 0) {
            $msg = $lang['tienda_error_nombre'];
            $tipo_msg = "error";
        } else {
            if ($cantidad > 0 && !empty($nombre_pedido)) {
                $sql = "INSERT INTO pedidos (usuario_id, nombre_pedido, talla, cantidad) VALUES ($usuario_id, '$nombre_pedido', '$talla', $cantidad)";
                if ($conn->query($sql)) {
                    $msg = $lang['tienda_msg_ok'];
                    $tipo_msg = "success";
                    // QUITADO: El meta refresh que recargaba la página.
                    // Al quitarlo, la página sigue cargando y la tabla de abajo
                    // saldrá actualizada automáticamente.
                    
                    // Recalcular totales para que se vea el cambio de precio al instante
                    $res_stats = $conn->query("SELECT SUM(cantidad) as total_camisetas FROM pedidos");
                    $row_stats = $res_stats->fetch_assoc();
                    $total_camisetas = $row_stats['total_camisetas'] ?? 0;
                    if ($total_camisetas > 0) {
                        $parte_cliche = $coste_cliche / $total_camisetas;
                        $precio_actual = $coste_camiseta + $parte_cliche;
                    }
                } else {
                    $msg = "Error: " . $conn->error;
                }
            }
        }
    }
}

// --- ORDENACIÓN ---
$orden = isset($_GET['orden']) ? $_GET['orden'] : 'fecha';
$sql_order = "p.fecha DESC"; 

switch ($orden) {
    case 'nombre':
        $sql_order = "p.nombre_pedido ASC"; 
        break;
    case 'talla':
        $sql_order = "FIELD(p.talla, 'S', 'M', 'L', 'XL', 'XXL', '3XL') ASC";
        break;
    case 'cantidad':
        $sql_order = "p.cantidad DESC";
        break;
    case 'fecha':
    default:
        $sql_order = "p.fecha DESC";
        break;
}
?>

<div class="container">
    
    <div style="text-align: center; margin: 40px 0;">
        <h1 style="color: var(--primary); text-transform: uppercase; border-bottom: 3px solid var(--accent); display: inline-block;">
            <i class="fas fa-tshirt"></i> <?php echo $lang['tienda_titulo']; ?>
        </h1>
        <p style="font-size: 1.2rem; margin-top: 10px;"><?php echo $lang['tienda_subtitulo']; ?></p>
    </div>

    <div class="grid-3" style="margin-bottom: 40px;">
        <div class="card" style="text-align: center; padding: 20px; border-bottom-color: var(--dark);">
            <i class="fas fa-users" style="font-size: 3rem; color: var(--dark); margin-bottom: 10px;"></i>
            <h3><?php echo $lang['tienda_total_pedidas']; ?></h3>
            <span style="font-size: 2.5rem; font-weight: bold; color: var(--accent);"><?php echo $total_camisetas; ?></span>
        </div>
        <div class="card" style="text-align: center; padding: 20px; border-bottom-color: var(--primary);">
            <i class="fas fa-tags" style="font-size: 3rem; color: var(--primary); margin-bottom: 10px;"></i>
            <h3><?php echo $lang['tienda_precio_actual']; ?></h3>
            <span style="font-size: 2.5rem; font-weight: bold; color: #28a745;"><?php echo number_format($precio_actual, 2); ?>€</span>
            <p>/ u.</p>
        </div>
        <div class="card" style="padding: 20px; font-size: 0.9rem; background: #f9f9f9;">
            <h4 style="color: var(--accent);"><i class="fas fa-calculator"></i> <?php echo $lang['tienda_transparencia']; ?>:</h4>
            <ul style="margin-top: 10px; line-height: 1.8;">
                <li><strong><?php echo $lang['tienda_base']; ?>:</strong> <?php echo number_format($coste_camiseta, 2); ?>€</li>
                <li><strong><?php echo $lang['tienda_cliche']; ?>:</strong> <?php echo number_format($coste_cliche, 2); ?>€</li>
                <li><strong><?php echo $lang['tienda_repartir']; ?>:</strong> <?php echo ($total_camisetas>0)? number_format($coste_cliche/$total_camisetas, 2) : 30; ?>€ / pers.</li>
            </ul>
        </div>
    </div>

    <?php if (isset($_SESSION['usuario_id'])): ?>
        
        <div class="card" style="margin-bottom: 40px; border-left: 5px solid var(--primary);">
            <div class="card-body">
                <h3><i class="fas fa-plus-circle"></i> <?php echo $lang['tienda_zona_privada_titulo']; ?></h3>
                
                <?php if($msg): ?>
                    <div style="padding: 10px; margin-bottom: 15px; border-radius: 4px; color: white; background: <?php echo ($tipo_msg=='error')?'#dc3545':'#28a745'; ?>;">
                        <?php echo $msg; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" style="display: flex; gap: 20px; align-items: flex-end; flex-wrap: wrap; margin-top: 15px;">
                    
                    <div style="flex: 2; min-width: 200px;">
                        <label style="font-weight: bold;"><?php echo $lang['tienda_label_nombre']; ?>:</label>
                        <input type="text" name="nombre_pedido" 
                               value="<?php echo isset($_SESSION['usuario_nombre']) ? $_SESSION['usuario_nombre'] : ''; ?>" 
                               placeholder="<?php echo $lang['tienda_placeholder_nombre']; ?>" 
                               required style="width: 100%; padding: 10px; border: 1px solid #ccc;">
                    </div>

                    <div style="flex: 1; min-width: 100px;">
                        <label style="font-weight: bold;"><?php echo $lang['tienda_talla']; ?>:</label>
                        <select name="talla" required style="width: 100%; padding: 10px; border: 1px solid #ccc;">
                            <option value="S">S</option>
                            <option value="M" selected>M</option>
                            <option value="L">L</option>
                            <option value="XL">XL</option>
                            <option value="XXL">XXL</option>
                            <option value="3XL">3XL</option>
                        </select>
                    </div>

                    <div style="flex: 1; min-width: 80px;">
                        <label style="font-weight: bold;"><?php echo $lang['tienda_cantidad']; ?>:</label>
                        <input type="number" name="cantidad" value="1" min="1" max="10" required style="width: 100%; padding: 10px; border: 1px solid #ccc;">
                    </div>

                    <button type="submit" class="btn" style="height: 42px; margin-bottom: 2px;"><?php echo $lang['tienda_btn_reservar']; ?></button>
                </form>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 10px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">
            <h3><i class="fas fa-list-alt"></i> <?php echo $lang['tienda_lista_titulo']; ?></h3>
            <div style="font-size: 0.9rem;">
                <i class="fas fa-filter"></i> <?php echo $lang['tienda_ordenar_ayuda']; ?>
            </div>
        </div>
        
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                <thead style="background: var(--dark); color: white;">
                    <tr>
                        <th style="padding: 12px; text-align: left;">
                            <a href="?orden=nombre" style="color: white; text-decoration: none;">
                                <?php echo $lang['tienda_usuario']; ?> <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        <th style="padding: 12px; text-align: center;">
                            <a href="?orden=talla" style="color: white; text-decoration: none;">
                                <?php echo $lang['tienda_talla']; ?> <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        <th style="padding: 12px; text-align: center;">
                            <a href="?orden=cantidad" style="color: white; text-decoration: none;">
                                <?php echo $lang['tienda_cantidad']; ?> <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        <th style="padding: 12px; text-align: right;">
                            <a href="?orden=fecha" style="color: white; text-decoration: none;">
                                <?php echo $lang['tienda_fecha']; ?> <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        <th style="padding: 12px; text-align: center;"><?php echo $lang['tienda_accion']; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql_lista = "SELECT p.*, u.id as uid FROM pedidos p JOIN usuarios u ON p.usuario_id = u.id ORDER BY $sql_order";
                    $res_lista = $conn->query($sql_lista);
                    
                    if ($res_lista->num_rows > 0) {
                        while($row = $res_lista->fetch_assoc()) {
                            $es_mio = ($row['uid'] == $_SESSION['usuario_id']);
                            $bg = $es_mio ? "background: #fff9e6;" : "border-bottom: 1px solid #eee;";
                            
                            echo "<tr style='$bg'>";
                            echo "<td style='padding: 12px; font-weight: bold;'><i class='fas fa-user'></i> " . htmlspecialchars($row['nombre_pedido']) . "</td>";
                            echo "<td style='padding: 12px; text-align: center;'><span style='background:#eee; padding:3px 8px; border-radius:3px;'>" . $row['talla'] . "</span></td>";
                            echo "<td style='padding: 12px; text-align: center; font-weight:bold;'>" . $row['cantidad'] . "</td>";
                            echo "<td style='padding: 12px; text-align: right; color: #777; font-size: 0.9rem;'>" . date("d/m", strtotime($row['fecha'])) . "</td>";
                            echo "<td style='padding: 12px; text-align: center;'>";
                            if ($es_mio || $_SESSION['usuario_rol'] == 'admin') {
                                echo "<form method='POST' onsubmit=\"return confirm('Borrar?');\" style='display:inline;'>";
                                echo "<input type='hidden' name='borrar_id' value='".$row['id']."'>";
                                echo "<button type='submit' style='background:none; border:none; color:red; cursor:pointer;'><i class='fas fa-trash'></i></button>";
                                echo "</form>";
                            }
                            echo "</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' style='padding: 20px; text-align:center;'>" . $lang['tienda_vacio'] . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

    <?php else: ?>
        <div style="background: #e9ecef; padding: 30px; text-align: center; border-radius: 8px; margin-top: 30px;">
            <h3>🔒 <?php echo $lang['tienda_login_titulo']; ?></h3>
            <p style="margin-bottom: 20px;"><?php echo $lang['tienda_login_desc']; ?></p>
            <a href="login.php" class="btn"><?php echo $lang['login_titulo']; ?></a>
        </div>
    <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>