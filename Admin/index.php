<?php
session_start();
require '../config/db.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ../Login/index.php');
    exit();
}

// Si se envia el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accion = '';
    // Comprobamos si se ha enviado la acción desde el formulario
    if (isset($_POST['accion'])) {
        $accion = $_POST['accion'];
    }

    if ($accion == 'eliminar_reserva') {
        $id = $_POST['id_reserva'];
        $pdo->query("DELETE FROM reservas WHERE id = $id");
    }

    // Cancelar reserva
    if ($accion == 'cancelar_reserva') {
        $id = $_POST['id_reserva'];
        $pdo->query("UPDATE reservas SET estado = 'cancelada' WHERE id = $id");
    }
    
    if ($accion == 'guardar_reserva') {
        $id = $_POST['id_reserva'];
        $nombre = $_POST['nombre'];
        $email = $_POST['email'];
        $fecha = $_POST['fecha'];
        $id_pista = $_POST['id_pista'];
        $pdo->query("UPDATE reservas SET fecha = '$fecha', id_pista = $id_pista WHERE id = $id");
        $pdo->query("UPDATE usuarios SET nombre = '$nombre', email = '$email' WHERE id = (SELECT id_usuario FROM reservas WHERE id = $id)");
    }   
    // Crear usuario nuevo
    if ($accion == 'crear') {
        $nombre = $_POST['nombre'];
        $apellido_1 = $_POST['apellido_1'];
        $apellido_2 = $_POST['apellido_2'];
        $email = $_POST['email'];
        // Hasheamos la contraseña antes de guardarla en la base de datos
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $rol = $_POST['rol'];
        $pdo->query("INSERT INTO usuarios (nombre, apellido_1, apellido_2, email, password, rol) VALUES ('$nombre', '$apellido_1', '$apellido_2', '$email', '$password', '$rol')");
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
        $apellido_1 = $_POST['apellido_1'];
        $apellido_2 = $_POST['apellido_2'];
        $email = $_POST['email'];
        $rol = $_POST['rol'];
        $pdo->query("UPDATE usuarios SET nombre = '$nombre', apellido_1 = '$apellido_1', apellido_2 = '$apellido_2', email = '$email', rol = '$rol' WHERE id = $id");
    }

    //Crear reserva nueva
    if ($accion == 'crear_reserva') {
        $nombre = $_POST['nombre'];
        $email = $_POST['email'];
        $consulta_usuario = $pdo->query("SELECT id FROM usuarios WHERE email = '$email'");
        $id_usuario = $consulta_usuario->fetchColumn();
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

        $pdo->query("INSERT INTO reservas (id_usuario, fecha, hora_inicio, duracion_horas, alquiler_pelotas, alquiler_raqueta, tipo_pago, precio_total, id_pista) VALUES ($id_usuario, '$fecha', '$hora_inicio', $duracion_horas, $alquiler_pelotas, $alquiler_raqueta, '$tipo_pago', $precio_total, $id_pista)");
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
                <a href="index.php?seccion=dashboard">Dashboard</a>
                <a href="index.php?seccion=pistas">Pistas</a>
                <a href="index.php?seccion=reservas">Reservas</a>
                <a href="index.php?seccion=usuarios">Usuarios</a>
                <a href="index.php?seccion=mensajes">Mensajes</a>
            </nav>
        </aside>

        <main class="contenido-admin">

            <?php
            $seccion = isset($_GET['seccion']) ? $_GET['seccion'] : '';
                $seccion = $_GET['seccion'] ?? 'dashboard';
            ?>
            <?php 
            if ($seccion == 'dashboard'){ ?>
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
            <?php } ?>
            

            <?php
            if ($seccion == 'pistas'){ ?>
                <section id="pistas" class="seccion-admin">
                    <h1>Gestión de Pistas</h1>

                    <?php
                    // Filtro de deporte
                    $filtro_deporte = '';
                    // Si se ha enviado el formulario de filtro, actualizar la variable de filtro
                    if (isset($_POST['deporte']) && $_POST['deporte'] != 'Todas') {
                        $filtro_deporte = $_POST['deporte'];
                    }
                    ?>

                    <form method = "post" action ="index.php?seccion=pistas">
                        <input type="submit" name="deporte" value="Tenis" class="boton-admin">
                        <input type="submit" name="deporte" value="Padel" class="boton-admin">
                        <input type="submit" name="deporte" value="Futbol" class="boton-admin">
                        <input type="submit" name="deporte" value="Baloncesto" class="boton-admin">
                    </form>

                    <?php
                    $sql_pistas = "SELECT p.nombre, p.precio_hora, p.estado, d.nombre AS deporte FROM pistas p JOIN deportes d ON p.id_deporte = d.id WHERE true";
                    if ($filtro_deporte != '') {
                        $sql_pistas .= " AND d.nombre = '$filtro_deporte'";
                    }
                    $resultado_pistas = $pdo->query($sql_pistas)->fetchAll();


                    foreach ($resultado_pistas as $p) {
                        echo '<div class="bloque-admin">';
                        echo '<h3>' . $p['nombre'] . '</h3>';
                        echo '<p>Deporte: ' . $p['deporte'] . '</p>';
                        echo '<p>Precio/hora: ' . $p['precio_hora'] . '€</p>';
                        echo '</div>';
                    }
                    ?>
                </section>
            <?php } ?>
            
            <?php
            if ($seccion == 'reservas'){ ?>
                <section id="reservas" class="seccion-admin">
                    <h1>Gestión de Reservas</h1>

                    <?php
                    $buscar = '';
                    if (isset($_POST['buscar']) && !empty($_POST['buscar'])) {
                        $buscar = $_POST['buscar'];
                    }

                    $estado_filtro = '';
                    if (isset($_POST['estado']) && $_POST['estado'] != '') {
                        $estado_filtro = $_POST['estado'];
                    }
                    
                    // Ordenar por fecha
                    // Por defecto, ordenamos de forma ascendente
                    $ordenar = 'ASC';
                    if (isset($_POST['ordenar']) && $_POST['ordenar'] == 'DESC') {
                        $ordenar = 'DESC';
                    }

                    $por_pagina = 10;
                    $sql_count = "SELECT COUNT(*) FROM reservas r JOIN usuarios u ON r.id_usuario = u.id JOIN pistas p ON r.id_pista = p.id WHERE true";
                    if ($buscar != '') {
                        $sql_count .= " AND (u.nombre LIKE '%$buscar%' OR u.apellido_1 LIKE '%$buscar%' OR u.apellido_2 LIKE '%$buscar%' OR p.nombre LIKE '%$buscar%')";
                    }
                    if ($estado_filtro != '') {
                        $sql_count .= " AND r.estado = '$estado_filtro'";
                    }
                    $total = $pdo->query($sql_count)->fetchColumn();
                    $total_paginas = ceil($total / $por_pagina);

                    $pagina = 1;
                    if (isset($_POST['pagina'])){
                        $pagina = $_POST['pagina'];
                    }

                    if (isset($_POST['primera'])) {
                        $pagina = 1;
                    } elseif (isset($_POST['anterior'])) {
                        $pagina = max(1, $pagina - 1);
                    } elseif (isset($_POST['siguiente'])) {
                        $pagina = min($total_paginas, $pagina + 1);
                    } elseif (isset($_POST['ultima'])) {
                        $pagina = $total_paginas;
                    }

                    // Calculamos el inicio de la consulta para la paginación
                    $inicio = ($pagina - 1) * $por_pagina;
                    $sql = "SELECT r.id, r.fecha, r.hora_inicio, r.estado, u.nombre, u.apellido_1, u.email, p.id AS id_pista, p.nombre AS nombre_pista FROM reservas r JOIN usuarios u ON r.id_usuario = u.id JOIN pistas p ON r.id_pista = p.id WHERE true";
                    if ($buscar != '') {
                        $sql .= " AND u.nombre LIKE '%$buscar%'";
                    }
                    if ($estado_filtro != '') {
                        $sql .= " AND r.estado = '$estado_filtro'";
                    }
                    $sql .= " ORDER BY r.fecha $ordenar";
                    // Agregamos la cláusula LIMIT para la paginación
                    $sql .= " LIMIT $inicio, $por_pagina";

                    $lista_reservas = $pdo->query($sql)->fetchAll();


                    // Traemos todas las pistas para el desplegable del formulario de edicion
                    $todas_pistas = $pdo->query("SELECT id, nombre FROM pistas ORDER BY nombre ASC")->fetchAll();

                    $id_editando = '';
                    if (isset($_POST['accion']) && $_POST['accion'] == 'editar_reserva') {
                        $id_editando = $_POST['id_reserva'];
                    }
                    ?>

                    <form method="post" action="index.php?seccion=reservas" class="form-crear">
                        <h2>Crear nueva reserva</h2>
                        <input type="hidden" name="accion" value="crear_reserva">
                        <input type="text" name="nombre" placeholder="Nombre del usuario" class="input-admin" required>
                        <input type="email" name="email" placeholder="Email del usuario" class="input-admin" required>
                        <input type="date" name="fecha" class="input-admin" required>
                        <select name="id_pista" class="input-admin">
                            <?php foreach ($todas_pistas as $pista) { ?>
                                <option value="<?php echo $pista['id']; ?>"><?php echo $pista['nombre']; ?></option>
                            <?php } ?>
                        </select>
                        
                        <input type="time" name="hora_inicio" class="input-admin" required>
                        <input type="number" name="duracion" placeholder="Duración (horas)" class="input-admin" min="1" max="2.5" required>
                        <label><input type="checkbox" name="pelotas"> Alquiler de pelotas</label>
                        <label><input type="checkbox" name="raqueta"> Alquiler de raqueta</label>
                        <select name="tipo_pago" class="input-admin">
                            <option value="tarjeta">Tarjeta</option>
                            <option value="efectivo">Efectivo</option>
                        </select>
                        <button type="submit" class="boton-admin">Crear reserva</button>
                    </form>

                    <form method="post" action="index.php?seccion=reservas" class="filtro-admin">
                        <input type="text" name="buscar" placeholder="Buscar por nombre, apellido " value="<?php echo $buscar; ?>" class="input-admin">
                        <select name="estado" class="input-admin">
                            <option value="">Todos los estados</option>
                            <option value="activa" <?php if ($estado_filtro == 'activa') echo 'selected'; ?>>Activa</option>
                            <option value="cancelada" <?php if ($estado_filtro == 'cancelada') echo 'selected'; ?>>Cancelada</option>
                        </select>
                        <select name="ordenar" class="input-admin">
                            <option value="ASC" <?php if ($ordenar == 'ASC') echo 'selected'; ?>>Orden ascendente</option>
                            <option value="DESC" <?php if ($ordenar == 'DESC') echo 'selected'; ?>>Orden descendente</option>
                        </select>
                        <button type="submit" class="boton-admin">Filtrar</button>
                    </form>

                     <table class="tabla-admin">
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Pista</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                        <?php foreach ($lista_reservas as $r) { ?>
                            <tr>
                                <td><?php echo $r['id']; ?></td>
                                <td><?php echo $r['nombre'] . ' ' . $r['apellido_1']; ?></td>
                                <td><?php echo $r['nombre_pista']; ?></td>
                                <td><?php echo $r['fecha']; ?></td>
                                <td><?php echo $r['hora_inicio']; ?></td>
                                <td><?php echo $r['estado']; ?></td>
                                <td>
                                    <form method="post" action="index.php?seccion=reservas">
                                        <input type="hidden" name="accion" value="eliminar_reserva">
                                        <input type="hidden" name="id_reserva" value="<?php echo $r['id']; ?>">
                                        <button type="submit" class="boton-eliminar">Eliminar</button>
                                    </form>
                                    <form method="post" action="index.php?seccion=reservas">
                                        <input type="hidden" name="accion" value="cancelar_reserva">
                                        <input type="hidden" name="id_reserva" value="<?php echo $r['id']; ?>">
                                        <button type="submit" class="boton-eliminar">Cancelar</button>
                                    </form>
                                    <form method="post" action="index.php?seccion=reservas">
                                        <input type="hidden" name="accion" value="editar_reserva">
                                        <input type="hidden" name="id_reserva" value="<?php echo $r['id']; ?>">
                                        <input type="hidden" name="nombre" value="<?php echo $pagina; ?>">
                                        <input type="hidden" name="buscar" value="<?php echo $buscar; ?>">
                                        <input type="hidden" name="estado" value="<?php echo $estado_filtro; ?>">
                                        <input type="hidden" name="ordenar" value="<?php echo $ordenar; ?>">
                                        <button type="submit" class="boton-admin">Editar</button>
                                    </form>
                                </td>
                            </tr>
                            <?php if ($id_editando == $r['id']) { ?>
                            <tr>
                                <td colspan="7">
                                    <form method="post" action="index.php?seccion=reservas">
                                        <input type="hidden" name="accion" value="guardar_reserva">
                                        <input type="hidden" name="id_reserva" value="<?php echo $r['id']; ?>">
                                        <input type="text" name="nombre" value="<?php echo $r['nombre']; ?>" class="input-admin" placeholder="Nombre" required>
                                        <input type="email" name="email" value="<?php echo $r['email']; ?>" class="input-admin" placeholder="Email" required>
                                        <input type="date" name="fecha" value="<?php echo $r['fecha']; ?>" class="input-admin" required>
                                        <select name="id_pista" class="input-admin">
                                            <?php foreach ($todas_pistas as $pista) { ?>
                                                <option value="<?php echo $pista['id']; ?>" <?php if ($pista['id'] == $r['id_pista']) { echo 'selected'; } ?>><?php echo $pista['nombre']; ?></option>
                                            <?php } ?>
                                        </select>
                                        <button type="submit" class="boton-admin">Guardar</button>
                                    </form>
                                </td>
                            </tr>
                            <?php } ?>
                        <?php } ?>
                    </table>


                    <form method="post" action="index.php?seccion=reservas" class="paginacion-admin">
                        <input type="hidden" name="buscar" value="<?php echo $buscar; ?>">
                        <input type="hidden" name="estado" value="<?php echo $estado_filtro; ?>">
                        <input type="hidden" name="orden_fecha" value="<?php echo $orden; ?>">
                        <input type="submit" name="primera" value="<<" class="boton-admin">
                        <input type="submit" name="anterior" value="<" class="boton-admin">
                        <input type="number" name="pagina" value="<?php echo $pagina; ?>" min="1" max="<?php echo $total_paginas; ?>">
                        <input type="submit" name="ir" value="Ir" class="boton-admin">
                        <input type="submit" name="siguiente" value=">" class="boton-admin">
                        <input type="submit" name="ultima" value=">>" class="boton-admin">
                    </form>
                </section>
            <?php } ?>



            <?php
            if ($seccion == 'usuarios'){ ?>
                <section id="usuarios" class="seccion-admin">
                    <h1>Gestión de Usuarios</h1>

                    <h2>Crear usuario</h2>
                    <form action="index.php?seccion=usuarios" method="post" class="form-crear">
                        <input type="hidden" name="accion" value="crear">
                        <input type="text" name="nombre" placeholder="Nombre" class="input-admin" required>
                        <input type="text" name="apellido_1" placeholder="Primer apellido" class="input-admin" required>
                        <input type="text" name="apellido_2" placeholder="Segundo apellido" class="input-admin">
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
                    $buscar_usuarios = '';
                    if (isset($_POST['buscar']) && !empty($_POST['buscar'])) {
                        $buscar_usuarios = $_POST['buscar'];
                    }
                    ?>
                    <form method="post" action="index.php?seccion=usuarios" class="filtro-admin">
                        <input type="text" name="buscar" placeholder="Buscar por nombre, apellido o email" value="<?php echo $buscar_usuarios; ?>" class="input-admin">
                        <button type="submit" class="boton-admin">Buscar</button>
                    </form>
                    
                    <?php
                    $sql_usuarios = "SELECT id, nombre, apellido_1, apellido_2, email, rol FROM usuarios WHERE true";
                    if ($buscar_usuarios != ''){
                        $sql_usuarios .= " AND (CONCAT(nombre, ' ', apellido_1, ' ', apellido_2) LIKE '%$buscar_usuarios%' OR email LIKE '%$buscar_usuarios%')";
                    }

                    $por_pagina_usuarios = 10;
                    $sql_count = "SELECT COUNT(*) FROM usuarios WHERE true";
                    if ($buscar_usuarios != '') {
                        $sql_count .= " AND (CONCAT(nombre, ' ', apellido_1, ' ', apellido_2) LIKE '%$buscar_usuarios%' OR email LIKE '%$buscar_usuarios%')";
                    }
                    
                    $total_usuarios = $pdo->query($sql_count)->fetchColumn();
                    $total_paginas_usuarios = ceil($total_usuarios / $por_pagina_usuarios);

                    $pagina = 1;
                    if (isset($_POST['pagina'])){
                        $pagina = $_POST['pagina'];
                    }

                    if (isset($_POST['primera'])) {
                        $pagina = 1;
                    } elseif (isset($_POST['anterior'])) {
                        $pagina = max(1, $pagina - 1);
                    } elseif (isset($_POST['siguiente'])) {
                        $pagina = min($total_paginas_usuarios, $pagina + 1);
                    } elseif (isset($_POST['ultima'])) {
                        $pagina = $total_paginas_usuarios   ;
                    }

                    $inicio_usuarios = ($pagina - 1) * $por_pagina_usuarios;
                    $sql_usuarios .= " LIMIT $inicio_usuarios, $por_pagina_usuarios";
                    $usuarios = $pdo->query($sql_usuarios)->fetchAll();
                    foreach ($usuarios as $u) {
                        echo '<div class="bloque-admin">';
                        echo '<h3>' . $u['nombre'] . ' ' . $u['apellido_1'] . ' ' . $u['apellido_2'] . '</h3>';
                        echo '<p>Email: ' . $u['email'] . '</p>';
                        echo '<p>Rol: ' . $u['rol'] . '</p>';

                        // Formulario editar
                        echo '<form action="index.php?seccion=usuarios" method="post">';
                        echo '<input type="hidden" name="accion" value="editar">';
                        echo '<input type="hidden" name="id_usuario" value="' . $u['id'] . '">';
                        echo '<input type="text" name="nombre" value="' . $u['nombre'] . '" class="input-admin" required>';
                        echo '<input type="text" name="apellido_1" value="' . $u['apellido_1'] . '" class="input-admin" required>';
                        echo '<input type="text" name="apellido_2" value="' . $u['apellido_2'] . '" class="input-admin" required>';
                        echo '<input type="email" name="email" value="' . $u['email'] . '" class="input-admin" required>';
                        echo '<select name="rol" class="input-admin">';
                        echo '<option value="cliente"' . ($u['rol'] == 'cliente' ? ' selected' : '') . '>Cliente</option>';
                        echo '<option value="admin"' . ($u['rol'] == 'admin' ? ' selected' : '') . '>Admin</option>';
                        echo '</select>';
                        echo '<button type="submit" class="boton-admin">Guardar cambios</button>';
                        echo '</form>';

                        // Formulario eliminar
                        echo '<form action="index.php?seccion=usuarios" method="post">';
                        echo '<input type="hidden" name="accion" value="eliminar">';
                        echo '<input type="hidden" name="id_usuario" value="' . $u['id'] . '">';
                        echo '<button type="submit" class="boton-eliminar">Eliminar</button>';
                        echo '</form>';

                        echo '</div>';
                    }
                    ?>
                    <form method="post" action="index.php?seccion=usuarios" class="paginacion-admin">
                        <input type="hidden" name="buscar" value="<?php echo $buscar_usuarios; ?>">
                        <input type="submit" name="primera" value="<<" class="boton-admin">
                        <input type="submit" name="anterior" value="<" class="boton-admin">
                        <input type="number" name="pagina" value="<?php echo $pagina; ?>" min="1" max="<?php echo $total_paginas_usuarios; ?>">
                        <input type="submit" name="ir" value="Ir" class="boton-admin">
                        <input type="submit" name="siguiente" value=">" class="boton-admin">
                        <input type="submit" name="ultima" value=">>" class="boton-admin">
                    </form>
                </section>
            <?php } ?>


            <?php
            if ($seccion == 'mensajes'){ ?>
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
            <?php } ?>

        </main>

    </div>

</body>
</html>