<?php
include 'includes/db.php';
include 'includes/header.php';

// TEXTOS (Puedes editarlos aquí directamente)
if ($idioma == 'ca') {
    $titulo = "La Llegenda del Samurai";
    $subtitulo = "De l'SJ410 fins avui: Una història de resistència.";
    
    $t1 = "Els Orígens";
    $p1 = "Tot va començar als anys 80. Mentre el món buscava cotxes ràpids i aerodinàmics, Suzuki va apostar per una capsa de sabates amb rodes. El Samurai (SJ) va néixer com una eina de treball, però aviat es va convertir en una icona de llibertat. Lleuger, simple i imparable.";
    
    $t2 = "El Naixement del Team";
    $p2 = "El nostre club va néixer de la necessitat. Quan tens un cotxe de 30 anys, passes més temps al garatge que a la muntanya. Vam decidir ajuntar-nos per compartir coneixements, peces i, sobretot, rutes. El que va començar com un grup de WhatsApp és avui una família.";
    
    $t3 = "La Nostra Filosofia";
    $p3 = "\"Ni un camí sense explorar\". No busquem la velocitat, busquem llocs on ningú més pot arribar. Creiem en la mecànica clàssica, en tacar-se les mans de greix i en que un bon dia acaba sempre amb el cotxe ple de fang.";
} else {
    $titulo = "La Leyenda del Samurai";
    $subtitulo = "Del SJ410 hasta hoy: Una historia de resistencia.";
    
    $t1 = "Los Orígenes";
    $p1 = "Todo comenzó en los años 80. Mientras el mundo buscaba coches rápidos y aerodinámicos, Suzuki apostó por una caja de zapatos con ruedas. El Samurai (SJ) nació como una herramienta de trabajo, pero pronto se convirtió en un icono de libertad. Ligero, simple e imparable.";
    
    $t2 = "El Nacimiento del Team";
    $p2 = "Nuestro club nació de la necesidad. Cuando tienes un coche de 30 años, pasas más tiempo en el garaje que en el monte. Decidimos juntarnos para compartir conocimientos, piezas y, sobre todo, rutas. Lo que empezó como un grupo de WhatsApp es hoy una familia.";
    
    $t3 = "Nuestra Filosofía";
    $p3 = "\"Ni un camino sin explorar\". No buscamos la velocidad, buscamos lugares donde nadie más puede llegar. Creemos en la mecánica clásica, en mancharse las manos de grasa y en que un buen día acaba siempre con el coche lleno de barro.";
}
?>

<div class="container">
    <div style="text-align: center; margin-bottom: 50px;">
        <h1 class="section-title" style="margin-bottom: 10px;"><?php echo $titulo; ?></h1>
        <p style="font-size: 1.2rem; color: #666;"><?php echo $subtitulo; ?></p>
    </div>

    <div class="historia-grid" style="display: flex; gap: 40px; align-items: center; margin-bottom: 60px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 300px;">
            <img src="img/poster2.jpg" style="width: 100%; border-radius: 8px; box-shadow: var(--shadow);">
        </div>
        <div style="flex: 1; min-width: 300px;">
            <h2 style="color: var(--primary); margin-bottom: 15px; border-left: 4px solid var(--accent); padding-left: 15px;"><?php echo $t1; ?></h2>
            <p style="font-size: 1.1rem; line-height: 1.8; color: #444;"><?php echo $p1; ?></p>
        </div>
    </div>

    <div class="historia-grid invert" style="display: flex; gap: 40px; align-items: center; margin-bottom: 60px; flex-wrap: wrap-reverse;">
        <div style="flex: 1; min-width: 300px;">
            <h2 style="color: var(--primary); margin-bottom: 15px; border-left: 4px solid var(--accent); padding-left: 15px;"><?php echo $t2; ?></h2>
            <p style="font-size: 1.1rem; line-height: 1.8; color: #444;"><?php echo $p2; ?></p>
        </div>
        <div style="flex: 1; min-width: 300px;">
            <img src="img/poster1.PNG" style="width: 100%; border-radius: 8px; box-shadow: var(--shadow);">
        </div>
    </div>

    <div style="background-color: var(--dark); color: white; padding: 40px; border-radius: 8px; text-align: center; margin-bottom: 40px;">
        <i class="fas fa-quote-left" style="font-size: 2rem; color: var(--accent); margin-bottom: 20px;"></i>
        <h2 style="margin-bottom: 20px; color: var(--primary);"><?php echo $t3; ?></h2>
        <p style="font-size: 1.2rem; max-width: 700px; margin: 0 auto;"><?php echo $p3; ?></p>
    </div>

    <div style="text-align: center;">
        <a href="login.php" class="btn" style="font-size: 1.2rem; padding: 15px 30px;"><?php echo $lang['menu_unirse']; ?></a>
    </div>

</div>

<?php include 'includes/footer.php'; ?>