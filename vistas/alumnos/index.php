<?php
declare(strict_types=1);

require_once __DIR__ . '/../../procesos/consultar_evaluacion_alumno.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis notas | Gnotas</title>
    <link rel="stylesheet" href="../css/alumnos.css">
</head>
<body>
<main>
    <header class="cabecera">
        <div>
            <p class="marca">Gnotas · Área personal</p>
            <h1>Mis notas</h1>
            <p class="bienvenida">
                Hola, <?= escapar($alumno['nombre'] . ' ' . $alumno['apellidos']) ?>.
                Aquí puedes consultar tu evaluación.
            </p>
        </div>
        <div class="cabecera-acciones">
            <form action="../../procesos/cerrar_sesion.php" method="post">
                <button class="boton-cerrar-sesion" type="submit">Cerrar sesión</button>
            </form>
            <?php if ($alumno['id_alumno'] !== null): ?>
                <section class="resumen" aria-label="Resumen del curso">
                    <span>Curso</span>
                    <strong><?= escapar($alumno['curso_nombre']) ?></strong>
                    <span>
                        <?= escapar($alumno['etapa']) ?> · Nivel <?= escapar($alumno['nivel']) ?>
                        · Grupo <?= escapar($alumno['grupo']) ?>
                    </span>
                    <span>Año académico <?= escapar($alumno['grado_academico']) ?></span>
                </section>
            <?php endif; ?>
        </div>
    </header>

    <?php if ($alumno['id_alumno'] === null): ?>
        <section class="aviso" role="status">
            Tu cuenta todavía no tiene una ficha académica asignada.
        </section>
    <?php elseif ($asignaturas === []): ?>
        <section class="aviso" role="status">
            Todavía no hay asignaturas asignadas a tu curso.
        </section>
    <?php else: ?>
        <section class="media-general" aria-label="Media general">
            <span>Media de las asignaturas con notas</span>
            <strong><?= $mediaGeneral === null ? '—' : escapar(mostrarNota($mediaGeneral)) ?></strong>
        </section>

        <section aria-labelledby="asignaturas-titulo">
            <h2 id="asignaturas-titulo">Asignaturas y evaluaciones</h2>
            <div class="tabla-contenedor">
                <table>
                    <thead>
                    <tr>
                        <th scope="col">Asignatura</th>
                        <th scope="col">1.ª evaluación</th>
                        <th scope="col">2.ª evaluación</th>
                        <th scope="col">3.ª evaluación</th>
                        <th scope="col">Media</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($asignaturas as $asignatura): ?>
                        <tr>
                            <th scope="row">
                                <span class="codigo"><?= escapar($asignatura['asignatura_codigo']) ?></span>
                                <?= escapar($asignatura['asignatura_nombre']) ?>
                            </th>
                            <td><?= escapar(mostrarNota($asignatura['evaluacion_1'])) ?></td>
                            <td><?= escapar(mostrarNota($asignatura['evaluacion_2'])) ?></td>
                            <td><?= escapar(mostrarNota($asignatura['evaluacion_3'])) ?></td>
                            <td class="nota-media">
                                <?= escapar(mostrarNota($asignatura['media_asignatura'])) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    <?php endif; ?>
</main>
</body>
</html>
