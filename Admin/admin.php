<?php
session_start();
require_once '../config/db.php';
// Verificar si el usuario no está autenticado o no es administrador
if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'admin')
{
    header('Location: ../login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administración de Pistas</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playwrite+NZ+Basic:wght@100..400&display=swap" rel="stylesheet">
</head>
<body>

    <div class="admin-container">

        <aside class="sidebar">
            <h2 class="logo-admin">A3Pistas</h2>

            <nav class="menu-admin">
                <a href="#dashboard">Dashboard</a>
                <a href="#pistas">Pistas</a>
                <a href="#reservas">Reservas</a>
                <a href="#usuarios">Usuarios</a>
                <a href="../logout.php" class="cerrar-admin">Cerrar Sesión</a>
            </nav>
        </aside>

       <main class="contenido-admin">

            <section id="dashboard" class="seccion-admin">
                <h1>Dashboard</h1>

                <?php
                // Contamos usuarios
                $total_usuarios = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();

                // Contamos reservas de hoy
                $total_reservas = $pdo->query("SELECT COUNT(*) FROM reservas WHERE fecha = CURDATE()")->fetchColumn();

                // Contamos pistas activas
                $total_pistas = $pdo->query("SELECT COUNT(*) FROM pistas WHERE estado = 'activa'")->fetchColumn();
                ?>

                <div class="bloque-admin">
                    <h3>Usuarios registrados</h3>
                    <p><?= $total_usuarios ?> usuarios en total</p>
                </div>

                <div class="bloque-admin">
                    <h3>Reservas hoy</h3>
                    <p><?= $total_reservas ?> reservas hoy</p>
                </div>

                <div class="bloque-admin">
                    <h3>Pistas disponibles</h3>
                    <p><?= $total_pistas ?> pistas activas</p>
                </div>
            </section>


            <section id="pistas" class="seccion-admin">
                <h1>Gestión de Pistas</h1>

                <?php
                $pistas = $pdo->query("
                    SELECT p.nombre, p.precio_hora, p.estado,
                        d.nombre AS deporte
                    FROM pistas p
                    JOIN deportes d ON p.id_deporte = d.id
                ")->fetchAll();

                foreach ($pistas as $p):
                ?>
                    <div class="bloque-admin">
                        <h3><?= htmlspecialchars($p['nombre']) ?></h3>
                        <p>Deporte: <?= htmlspecialchars($p['deporte']) ?></p>
                        <p>Precio/hora: <?= $p['precio_hora'] ?>€</p>
                        <p>Estado: <?= $p['estado'] ?></p>
                    </div>
                <?php endforeach; ?>
            </section>


            <section id="reservas" class="seccion-admin">
                <h1>Gestión de Reservas</h1>

                <?php
                $reservas = $pdo->query("
                    SELECT r.fecha, r.hora_inicio, r.estado,
                        u.nombre, u.apellidos,
                        p.nombre AS nombre_pista
                    FROM reservas r
                    JOIN usuarios u ON r.id_usuario = u.id
                    JOIN pistas p ON r.id_pista = p.id
                    ORDER BY r.fecha DESC
                ")->fetchAll();

                foreach ($reservas as $r):
                ?>
                    <div class="bloque-admin">
                        <h3><?= htmlspecialchars($r['nombre'] . ' ' . $r['apellidos']) ?></h3>
                        <p>Pista: <?= htmlspecialchars($r['nombre_pista']) ?></p>
                        <p>Fecha: <?= $r['fecha'] ?></p>
                        <p>Hora: <?= $r['hora_inicio'] ?></p>
                        <p>Estado: <?= $r['estado'] ?></p>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($reservas)): ?>
                    <p>No hay reservas todavía.</p>
                <?php endif; ?>
            </section>


            <!-- GESTIÓN USUARIOS -->
            <section id="usuarios" class="seccion-admin">
                <h1>Gestión de Usuarios</h1>

                <?php
                $usuarios = $pdo->query("SELECT nombre, apellidos, email, rol FROM usuarios")->fetchAll();
                foreach ($usuarios as $u):
                ?>
                    <div class="bloque-admin">
                        <h3><?= htmlspecialchars($u['nombre'] . ' ' . $u['apellidos']) ?></h3>
                        <p>Email: <?= htmlspecialchars($u['email']) ?></p>
                        <p>Rol: <?= $u['rol'] ?></p>
                    </div>
                <?php endforeach; ?>
            </section>

        </main>

    </div>

</body>
</html>