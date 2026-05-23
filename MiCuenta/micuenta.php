<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['id'])) {
    header('Location: ../Login/login.php');
    exit();
}

$id_usuario = $_SESSION['id'];
$hoy = date('Y-m-d');

// Datos del usuario
$stmt = $pdo->prepare('SELECT nombre, email, saldo_monedas FROM usuarios WHERE id = ?');
$stmt->execute([$id_usuario]);
$usuario = $stmt->fetch();

// Próximas reservas (fecha >= hoy)
$stmt = $pdo->prepare('SELECT reservas.*, pistas.nombre AS nombre_pista FROM reservas JOIN pistas ON reservas.id_pista = pistas.id WHERE reservas.id_usuario = ? AND reservas.fecha >= ? ORDER BY reservas.fecha ASC');
$stmt->execute([$id_usuario, $hoy]);
$proximas = $stmt->fetchAll();

// Historial (fecha < hoy)
$stmt = $pdo->prepare('SELECT reservas.*, pistas.nombre AS nombre_pista FROM reservas JOIN pistas ON reservas.id_pista = pistas.id WHERE reservas.id_usuario = ? AND reservas.fecha < ? ORDER BY reservas.fecha DESC');
$stmt->execute([$id_usuario, $hoy]);
$historial = $stmt->fetchAll();

// Todos los movimientos de BMVCoins
$stmt = $pdo->prepare('SELECT reservas.*, pistas.nombre AS nombre_pista FROM reservas JOIN pistas ON reservas.id_pista = pistas.id WHERE reservas.id_usuario = ? ORDER BY reservas.fecha DESC');
$stmt->execute([$id_usuario]);
$todas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Cuenta</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playwrite+NZ+Basic:wght@100..400&display=swap" rel="stylesheet">
</head>
<body>
    <header class="header">
        <div class="logos">
            <a href="../Principal/principal.php">
                <img src="logo.png" alt="Logo" class="logo">
            </a>
            <img src="espana.png" alt="Logo" class="logo2">
        </div>
        <nav class="nav1">
            <a href="../Principal/principal.php" class="inicio">Inicio</a>
            <a href="../Torneos/torneos.php" class="torneos">Torneos</a>
            <a href="../Contacto/contacto.php" class="contacto">Contacto</a>
        </nav>
        <nav class="nav2">
            <div class="monedas">
                <img src="moneda.png" class="moneda">
                <span class="saldo"><?= $_SESSION['saldo_monedas'] ?? 0 ?></span>
            </div>
            <?php if (isset($_SESSION['id'])) { ?>
                <span class="login-button">Hola, <?= htmlspecialchars($_SESSION['nombre']) ?></span>
                <a href="../logout.php" class="cerrar">Cerrar Sesión</a>
            <?php } else { ?>
                <a href="../Login/login.php" class="login-button">Acceder</a>
            <?php } ?>
        </nav>
    </header>

    <main class="micuenta-container">

        <!-- MI CUENTA -->
        <section class="seccion">
            <h1>Mi Cuenta</h1>
            <p><strong>Nombre:</strong> <?= htmlspecialchars($usuario['nombre']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($usuario['email']) ?></p>
            <p><strong>BMVCoins:</strong> <?= $usuario['saldo_monedas'] ?> coins</p>
        </section>

        <!-- PRÓXIMAS RESERVAS -->
        <section class="seccion">
            <h2>Próximas Reservas</h2>
            <?php if (count($proximas) == 0) { ?>
                <p class="sin-datos">No tienes reservas próximas.</p>
            <?php } else { ?>
                <table class="tabla-reservas">
                    <thead>
                        <tr>
                            <th>Pista</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Duración</th>
                            <th>Precio</th>
                            <th>Pago</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($proximas as $reserva) { ?>
                        <tr>
                            <td><?= htmlspecialchars($reserva['nombre_pista']) ?></td>
                            <td><?= $reserva['fecha'] ?></td>
                            <td><?= $reserva['hora_inicio'] ?></td>
                            <td><?= $reserva['duracion_horas'] ?> hora/s</td>
                            <td><?= $reserva['precio_total'] ?></td>
                            <td><?= $reserva['tipo_pago'] ?></td>
                            <td><?= $reserva['estado'] ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
        </section>

        <!-- HISTORIAL -->
        <section class="seccion">
            <h2>Historial</h2>
            <?php if (count($historial) == 0) { ?>
                <p class="sin-datos">No tienes reservas anteriores.</p>
            <?php } else { ?>
                <table class="tabla-reservas">
                    <thead>
                        <tr>
                            <th>Pista</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Duración</th>
                            <th>Precio</th>
                            <th>Pago</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historial as $reserva) { ?>
                        <tr>
                            <td><?= htmlspecialchars($reserva['nombre_pista']) ?></td>
                            <td><?= $reserva['fecha'] ?></td>
                            <td><?= $reserva['hora_inicio'] ?></td>
                            <td><?= $reserva['duracion_horas'] ?> hora/s</td>
                            <td><?= $reserva['precio_total'] ?></td>
                            <td><?= $reserva['tipo_pago'] ?></td>
                            <td><?= $reserva['estado'] ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
        </section>

        <!-- BMVCOINS -->
        <section class="seccion">
            <h2>BMVCoins</h2>
            <p>Saldo actual: <strong><?= $usuario['saldo_monedas'] ?> coins</strong></p>
            <?php if (count($todas) == 0) { ?>
                <p class="sin-datos">No hay movimientos de BMVCoins.</p>
            <?php } else { ?>
                <table class="tabla-reservas">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Pista</th>
                            <th>Tipo</th>
                            <th>Coins</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($todas as $reserva) { ?>
                        <tr>
                            <td><?= $reserva['fecha'] ?></td>
                            <td><?= htmlspecialchars($reserva['nombre_pista']) ?></td>
                            <?php if ($reserva['tipo_pago'] == 'bmvcoins') { ?>
                                <td class="gasto">Pago con BMVCoins</td>
                                <td class="gasto">-<?= $reserva['precio_total'] ?> coins</td>
                            <?php } else { ?>
                                <td class="ganancia">Reserva pagada</td>
                                <td class="ganancia">+<?= (int)($reserva['precio_total'] / 2) ?> coins</td>
                            <?php } ?>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
        </section>

    </main>
</body>
</html>
