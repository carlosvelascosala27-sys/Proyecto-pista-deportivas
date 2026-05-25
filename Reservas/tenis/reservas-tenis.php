<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['id'])) {
    header('Location: ../../Login/login.php');
    exit();
}

// Fecha de hoy por defecto, o la que venga al pulsar las flechas
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');
$hoy = date('Y-m-d');

// No permitir fechas pasadas
if ($fecha < $hoy) {
    $fecha = $hoy;
}

// Fechas para las flechas
$fecha_anterior = date('Y-m-d', strtotime($fecha . ' -1 day'));
$fecha_siguiente = date('Y-m-d', strtotime($fecha . ' +1 day'));

// Horas disponibles
$horas = ['11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00'];

// Pistas de tenis (id 1 al 4 en la BD)
$pistas = [
    ['id' => 11, 'nombre' => 'Pista Cubierta 1', 'imagen' => 'teniscubierto.jpg'],
    ['id' => 12, 'nombre' => 'Pista Cubierta 2', 'imagen' => 'teniscubierto.jpg'],
    ['id' => 13, 'nombre' => 'Pista Exterior 1', 'imagen' => 'tenisexterior.png'],
    ['id' => 14, 'nombre' => 'Pista Exterior 2', 'imagen' => 'tenisexterior.png'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reservas Tenis</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playwrite+NZ+Basic:wght@100..400&display=swap" rel="stylesheet">
</head>
<body>
    <header class="header">
        <div class="logos">
            <a href="../../Principal/principal.php">
                <img src="logo.png" alt="Logo" class="logo">
            </a>
            <img src="espana.png" alt="Logo" class="logo2">
        </div>
        <nav class="nav1">
            <a href="../../Principal/principal.php" class="inicio">Inicio</a>
            <a href="../../Torneos/torneos.php" class="torneos">Torneos</a>
            <a href="../../Contacto/contacto.php" class="contacto">Contacto</a>
        </nav>
        <nav class="nav2">
            <div class="monedas">
                <img src="moneda.png" class="moneda">
                <span class="saldo"><?= $_SESSION['saldo_monedas'] ?? 0 ?></span>
            </div>
            <?php if (isset($_SESSION['id'])): ?>
                <a href="../MiCuenta/micuenta.php" class="login-button">Hola, <?= htmlspecialchars($_SESSION['nombre']) ?></a>
                <a href="../../logout.php" class="cerrar">Cerrar Sesión</a>
            <?php else: ?>
                <a href="../../Login/login.php" class="login-button">Acceder</a>
            <?php endif; ?>
        </nav>
    </header>

    <main class="reservas">
        <h1 class="titulo-deporte">TENIS</h1>

        <?php foreach ($pistas as $pista): ?>
        <details class="panel">
            <summary class="panel-header">
                <img src="<?= $pista['imagen'] ?>" alt="<?= $pista['nombre'] ?>">
                <div class="header-info">
                    <span class="titulo"><?= $pista['nombre'] ?></span>
                    <span class="precio">10€/hora</span>
                </div>
            </summary>

            <!-- Selector de fecha -->
            <div class="selector-fecha">
                <?php if ($fecha > $hoy): ?>
                    <a href="?fecha=<?= $fecha_anterior ?>" class="flecha">&#8249;</a>
                <?php else: ?>
                    <span class="flecha" style="opacity:0.3;">&#8249;</span>
                <?php endif; ?>

                <span class="fecha"><?= date('d/m/Y', strtotime($fecha)) ?></span>

                <a href="?fecha=<?= $fecha_siguiente ?>" class="flecha">&#8250;</a>
            </div>

            <p class="estado">
                Disponible <span style="color:green;">&#128994;</span> |
                Ocupado <span style="color:red;">&#128308;</span>
            </p>

            <div class="contenido">
                <div class="horario-grid">
                    <?php foreach ($horas as $hora): ?>
                        <?php
                        // Comprobamos si esa hora está reservada en la BD
                        $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservas WHERE id_pista = ? AND fecha = ? AND hora_inicio = ?");
                        $stmt->execute([$pista['id'], $fecha, $hora . ':00']);
                        $ocupado = $stmt->fetchColumn() > 0;
                        ?>
                        <?php if ($ocupado): ?>
                            <div class="slot ocupado"><?= $hora ?></div>
                        <?php else: ?>
                            <a href="formulario-tenis.php?id_pista=<?= $pista['id'] ?>&fecha=<?= $fecha ?>&hora=<?= $hora ?>" class="slot disponible"><?= $hora ?></a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </details>
        <?php endforeach; ?>
    </main>
</body>
</html>
