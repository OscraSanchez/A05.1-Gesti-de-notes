<?php
declare(strict_types=1);

require_once __DIR__ . '/../../procesos/listar_alumnos_profesor.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis alumnos | Gnotas</title>
    <link rel="stylesheet" href="../css/profesores.css">
</head>
<body>
<main>
    <header class="cabecera">
        <div>
            <p class="marca">Gnotas · Área docente</p>
            <h1>Mis alumnos</h1>
            <p class="subtitulo">Alumnado y asignaturas de todos los cursos.</p>
        </div>
        <nav class="acciones-cabecera" aria-label="Opciones de cuenta">
            <a class="boton-enlace boton-perfil" href="editar_perfil.php">Editar perfil</a>
            <form action="../../procesos/cerrar_sesion.php" method="post">
                <button class="boton-secundario" type="submit">Cerrar sesión</button>
            </form>
        </nav>
    </header>

    <form class="busqueda-listado" method="get" role="search">
        <label for="busqueda-alumnos">Buscar alumno, curso, año o asignatura</label>
        <div>
            <input id="busqueda-alumnos" type="search" name="busqueda" maxlength="100" value="<?= escaparListadoProfesor($busqueda) ?>" placeholder="Nombre, apellidos, curso..." autocomplete="off">
            <button type="submit">Buscar</button>
            <?php if ($busqueda !== ''): ?><a class="boton-secundario boton-enlace" href="listado_alumnos.php">Limpiar</a><?php endif; ?>
        </div>
    </form>

    <?php if ($alumnos === []): ?>
        <section class="aviso" role="status"><?= $busqueda === '' ? 'No tienes alumnos asignados actualmente.' : 'No se encontraron alumnos con esa búsqueda.' ?></section>
    <?php else: ?>
        <section class="tabla-contenedor" aria-label="Listado de alumnos">
            <table>
                <thead>
                <tr>
                    <th scope="col">Alumno</th>
                    <th scope="col">Curso</th>
                    <th scope="col">Año académico</th>
                    <th scope="col">Asignaturas del curso</th>
                    <th scope="col"><span class="solo-lectores">Acciones</span></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($alumnos as $alumno): ?>
                    <tr>
                        <th scope="row">
                            <?= escaparListadoProfesor($alumno['apellidos'] . ', ' . $alumno['nombre']) ?>
                        </th>
                        <td><?= escaparListadoProfesor($alumno['curso']) ?></td>
                        <td><?= escaparListadoProfesor($alumno['grado_academico']) ?></td>
                        <td><?= escaparListadoProfesor($alumno['asignaturas']) ?></td>
                        <td>
                            <a class="boton-enlace" href="alumno.php?id=<?= (int) $alumno['id_alumno'] ?>">
                                Ver notas
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    <?php endif; ?>
</main>
</body>
</html>
