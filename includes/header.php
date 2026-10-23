<?php
// Iniciar sesión
session_start();

// --- LÓGICA DE IDIOMA ---
if (isset($_GET['lang'])) {
    $idioma = $_GET['lang'];
    $_SESSION['idioma'] = $idioma;
} else if (isset($_SESSION['idioma'])) {
    $idioma = $_SESSION['idioma'];
} else {
    $idioma = 'es'; // Español por defecto
}

// Seguridad
if ($idioma != 'es' && $idioma != 'ca') { $idioma = 'es'; } 

// Cargar diccionario
include "lang/" . $idioma . ".php";

// --- FUNCIÓN PARA MANTENER FILTROS AL CAMBIAR IDIOMA ---
function getUrlConIdioma($nuevo_idioma) {
    $params = $_GET; 
    $params['lang'] = $nuevo_idioma; 
    return '?' . http_build_query($params); 
}
?>

<!DOCTYPE html>
<html lang="<?php echo $idioma; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suzuki Samurai Team</title>
    
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="css/style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<header>
    <div class="navbar">
        <a href="index.php" class="logo">SUZUKI<span>SAMURAI</span>TEAM</a>
        
        <div class="menu-toggle" id="mobile-menu">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </div>
        
        <nav class="nav-links">
            <a href="index.php" class="<?php echo (basename($_SERVER['PHP_SELF'])=='index.php')?'active':''; ?>">
                <?php echo $lang['menu_inicio']; ?>
            </a>
            
            <a href="manuales.php"><?php echo isset($lang['menu_bricos']) ? $lang['menu_bricos'] : 'Bricos'; ?></a>
            <a href="miembros.php"><?php echo isset($lang['card3_titulo']) ? $lang['card3_titulo'] : 'Miembros'; ?></a>
            <a href="camisetas.php"><?php echo isset($lang['menu_tienda']) ? $lang['menu_tienda'] : 'Camisetas'; ?></a>
            
            <div class="lang-switch">
                <a href="<?php echo getUrlConIdioma('es'); ?>" class="<?php if($idioma=='es') echo 'lang-active'; ?>">ES</a>
                <span>|</span>
                <a href="<?php echo getUrlConIdioma('ca'); ?>" class="<?php if($idioma=='ca') echo 'lang-active'; ?>">CA</a>
            </div>

            <?php if(isset($_SESSION['usuario_id'])): ?>
                
                <?php if($_SESSION['usuario_rol'] == 'admin'): ?>
                    <a href="admin.php" title="Panel de Control" style="color: var(--accent);">
                        <i class="fas fa-cogs"></i>
                    </a>
                <?php endif; ?>

                <a href="perfil.php" style="color: white; font-weight: bold;">
                    <i class="fas fa-car" style="margin-right: 5px;"></i> 
                    <?php echo isset($lang['perfil_titulo']) ? $lang['perfil_titulo'] : 'Perfil'; ?>
                </a>

                <span style="font-size: 0.9rem; color: #ccc; border-left: 1px solid #555; padding-left: 10px;">
                    <?php echo $_SESSION['usuario_nombre']; ?>
                </span>
                
                <a href="logout.php" class="btn btn-secondary" style="padding: 5px 10px; font-size: 0.8rem; border-color: #666; color: #ccc;">
                    <i class="fas fa-power-off"></i>
                </a>

            <?php else: ?>
                <a href="login.php" class="btn" style="padding: 8px 15px; font-size: 0.8rem;">
                    <?php echo isset($lang['menu_unirse']) ? $lang['menu_unirse'] : 'Unirse'; ?>
                </a>
            <?php endif; ?>

        </nav>
    </div>
</header>

<script>
    const menu = document.querySelector('#mobile-menu');
    const navLinks = document.querySelector('.nav-links');

    if (menu) {
        menu.addEventListener('click', function() {
            menu.classList.toggle('is-active');
            navLinks.classList.toggle('active');
        });
    }
</script>