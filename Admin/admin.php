<?php
session_start();
require_once '../config/db.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Si se envia el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accion = $_POST['accion'];

    // Crear usuario nuevo
    if ($accion == 'crear') {
        $nombre = $_POST['nombre'];
        $apellidos = $_POST['apellidos'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $rol = $_POST['rol'];
        $pdo->query("INSERT INTO usuarios (nombre, apellidos, email, password, rol) VALUES ('$nombre', '$apellidos', '$email', '$password', '$rol')");
    }

    // Eliminar usuario
    if ($accion == 'eliminar') {
        $id = $_POST['id_usuario'];
        $pdo->query("DELETE FROM usuarios WHERE id = $id");
    }

    // Editar usuario
    if ($accion == 'editar') {
        $id = $_POST['id_usuario'];
        $nombre = $_POST['nombre'];
        $apellidos = $_POST['apellidos'];
        $email = $_POST['email'];
        $rol = $_POST['rol'];
        $pdo->query("UPDATE usuarios SET nombre = '$nombre', apellidos = '$apellidos', email = '$email', rol = '$rol' WHERE id = $id");
    }

    //Crear reserva nueva
    if ($accion == 'crear_reserva') {
        $id_usuario = $_POST['id_usuario'];
        $id_pista = $_POST['id_pista'];
        $fecha = $_POST['fecha'];
        $hora_inicio = $_POST['hora_inicio'];
        $duracion_horas = $_POST['duracion'];
        $tipo_pago = $_POST['tipo_pago'];
        
        // Si se ha seleccionado alquiler de pelotas, agregar esa información a la reserva
        if (isset($_POST['pelotas'])){
            $alquiler_pelotas = 1;
        }else {
            $alquiler_pelotas = 0;
        }

        if (isset($_POST['raqueta'])){
            $alquiler_raqueta = 1;
        }else {
            $alquiler_raqueta = 0;
        }

        // Cogemos el precio por hora de esa pista de la base de datos
        $consulta = $pdo->query("SELECT precio_hora FROM pistas WHERE id = $id_pista");
        $precio_hora = $consulta->fetchColumn();
        // Calculamos el precio total de la reserva
        $precio_total = $precio_hora * $duracion_horas;

        $pdo->query("INSERT INTO reservas (id_usuario, fecha, hora_inicio, duracion_horas, alquiler_pelotas, alquiler_raqueta, tipo_pago, precio_total, id_pista) VALUES (LAST_INSERT_ID(), '$fecha', '$hora_inicio', $duracion_horas, $alquiler_pelotas, $alquiler_raqueta, '$tipo_pago', $precio_total, $id_pista)");
    }

    // Editar reserva existennte
    if ($accion == 'editar_reserva') {
        $id = $_POST['id_reserva'];
        $fecha = $_POST['fecha'];
        $hora_inicio = $_POST['hora_inicio'];
        $duracion_horas = $_POST['duracion'];
        $tipo_pago = $_POST['tipo_pago'];

        // Si se ha seleccionado alquiler de pelotas, agregar esa información a la reserva
        if (isset($_POST['pelotas'])){
            $alquiler_pelotas = 1;
        }else {
            $alquiler_pelotas = 0;
        }

        if (isset($_POST['raqueta'])){
            $alquiler_raqueta = 1;
        }else {
            $alquiler_raqueta = 0;
        }

        $pdo->query("UPDATE reservas SET duracion_horas = $duracion_horas, tipo_pago = '$tipo_pago', alquiler_pelotas = $alquiler_pelotas, alquiler_raqueta = $alquiler_raqueta WHERE id = $id");

    }

    // Eliminar reserva existente
    if ($accion == 'eliminar_reserva') {
        $id = $_POST['id_reserva'];
        $pdo->query("DELETE FROM reservas WHERE id = $id");
    }
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
                <a href="#mensajes">Mensajes</a>
                <a href="../logout.php" class="cerrar-admin">Cerrar Sesión</a>
            </nav>
        </aside>

        <main class="contenido-admin">

            <section id="dashboard" class="seccion-admin">
                <h1>Dashboard</h1>
                <?php
                $total_usuarios = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
                $total_reservas = $pdo->query("SELECT COUNT(*) FROM reservas WHERE fecha = CURDATE()")->fetchColumn();
                $total_pistas = $pdo->query("SELECT COUNT(*) FROM pistas WHERE estado = 'activa'")->fetchColumn();
                $total_mensajes = $pdo->query("SELECT COUNT(*) FROM mensajes_contacto")->fetchColumn();

                echo '<div class="bloque-admin"><h3>Usuarios registrados</h3><p>' . $total_usuarios . ' usuarios en total</p></div>';
                echo '<div class="bloque-admin"><h3>Reservas hoy</h3><p>' . $total_reservas . ' reservas hoy</p></div>';
                echo '<div class="bloque-admin"><h3>Pistas disponibles</h3><p>' . $total_pistas . ' pistas activas</p></div>';
                echo '<div class="bloque-admin"><h3>Mensajes de contacto</h3><p>' . $total_mensajes . ' mensajes recibidos</p></div>';
                ?>
            </section>

            <section id="pistas" class="seccion-admin">
                <h1>Gestión de Pistas</h1>
                <?php
                $pistas = $pdo->query("SELECT p.nombre, p.precio_hora, p.estado, d.nombre AS deporte FROM pistas p JOIN deportes d ON p.id_deporte = d.id")->fetchAll();
                foreach ($pistas as $p) {
                    echo '<div class="bloque-admin">';
                    echo '<h3>' . $p['nombre'] . '</h3>';
                    echo '<p>Deporte: ' . $p['deporte'] . '</p>';
                    echo '<p>Precio/hora: ' . $p['precio_hora'] . '€</p>';
                    echo '<p>Estado: ' . $p['estado'] . '</p>';
                    echo '</div>';
                }
                ?>
            </section>

            <section id="reservas" class="seccion-admin">
                <h1>Gestión de Reservas</h1>
                <?php
                $reservas = $pdo->query("SELECT r.fecha, r.hora_inicio, r.estado, u.nombre, u.apellidos, p.nombre AS nombre_pista FROM reservas r JOIN usuarios u ON r.id_usuario = u.id JOIN pistas p ON r.id_pista = p.id ORDER BY r.fecha DESC")->fetchAll();

                if (empty($reservas)) {
                    echo '<p>No hay reservas todavía.</p>';
                } else {
                    foreach ($reservas as $r) {
                        echo '<div class="bloque-admin">';
                        echo '<h3>' . $r['nombre'] . ' ' . $r['apellidos'] . '</h3>';
                        echo '<p>Pista: ' . $r['nombre_pista'] . '</p>';
                        echo '<p>Fecha: ' . $r['fecha'] . '</p>';
                        echo '<p>Hora: ' . $r['hora_inicio'] . '</p>';
                        echo '<p>Estado: ' . $r['estado'] . '</p>';
                        echo '</div>';
                    }
                }
                ?>
            </section>

            <section id="usuarios" class="seccion-admin">
                <h1>Gestión de Usuarios</h1>

                <h2>Crear usuario</h2>
                <form action="admin.php" method="post" class="form-crear">
                    <input type="hidden" name="accion" value="crear">
                    <input type="text" name="nombre" placeholder="Nombre" class="input-admin" required>
                    <input type="text" name="apellidos" placeholder="Apellidos" class="input-admin" required>
                    <input type="email" name="email" placeholder="Email" class="input-admin" required>
                    <input type="password" name="password" placeholder="Contraseña" class="input-admin" required>
                    <select name="rol" class="input-admin">
                        <option value="cliente">Cliente</option>
                        <option value="admin">Admin</option>
                    </select>
                    <button type="submit" class="boton-admin">Crear usuario</button>
                </form>

                <h2>Usuarios registrados</h2>
                <?php
                $usuarios = $pdo->query("SELECT id, nombre, apellidos, email, rol FROM usuarios")->fetchAll();
                foreach ($usuarios as $u) {
                    echo '<div class="bloque-admin">';
                    echo '<h3>' . $u['nombre'] . ' ' . $u['apellidos'] . '</h3>';
                    echo '<p>Email: ' . $u['email'] . '</p>';
                    echo '<p>Rol: ' . $u['rol'] . '</p>';

                    // Formulario editar
                    echo '<form action="admin.php" method="post">';
                    echo '<input type="hidden" name="accion" value="editar">';
                    echo '<input type="hidden" name="id_usuario" value="' . $u['id'] . '">';
                    echo '<input type="text" name="nombre" value="' . $u['nombre'] . '" class="input-admin" required>';
                    echo '<input type="text" name="apellidos" value="' . $u['apellidos'] . '" class="input-admin" required>';
                    echo '<input type="email" name="email" value="' . $u['email'] . '" class="input-admin" required>';
                    echo '<select name="rol" class="input-admin">';
                    echo '<option value="cliente"' . ($u['rol'] == 'cliente' ? ' selected' : '') . '>Cliente</option>';
                    echo '<option value="admin"' . ($u['rol'] == 'admin' ? ' selected' : '') . '>Admin</option>';
                    echo '</select>';
                    echo '<button type="submit" class="boton-admin">Guardar cambios</button>';
                    echo '</form>';

                    // Formulario eliminar
                    echo '<form action="admin.php" method="post">';
                    echo '<input type="hidden" name="accion" value="eliminar">';
                    echo '<input type="hidden" name="id_usuario" value="' . $u['id'] . '">';
                    echo '<button type="submit" class="boton-eliminar">Eliminar</button>';
                    echo '</form>';

                    echo '</div>';
                }
                ?>
            </section>

            <section id="mensajes" class="seccion-admin">
                <h1>Mensajes de Contacto</h1>
                <?php
                $mensajes = $pdo->query("SELECT nombre, email, asunto, mensaje, fecha_envio FROM mensajes_contacto ORDER BY fecha_envio DESC")->fetchAll();

                if (count($mensajes) == 0) {
                    echo '<p>No hay mensajes todavía.</p>';
                } else {
                    foreach ($mensajes as $m) {
                        echo '<div class="bloque-admin">';
                        echo '<h3>' . $m['nombre'] . '</h3>';
                        echo '<p>Email: ' . $m['email'] . '</p>';
                        echo '<p>Asunto: ' . $m['asunto'] . '</p>';
                        echo '<p>Mensaje: ' . $m['mensaje'] . '</p>';
                        echo '<p>Fecha: ' . $m['fecha_envio'] . '</p>';
                        echo '</div>';
                    }
                }
                ?>
            </section>

        </main>

    </div>

</body>
</html>