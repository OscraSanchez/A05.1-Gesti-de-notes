<?php
declare(strict_types=1);

require_once __DIR__ . '/../../procesos/consultar_notas_alumno_profesor.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluación del alumno | Gnotas</title>
    <link rel="stylesheet" href="../css/profesores.css">
</head>
<body>
<main>
    <nav class="barra-superior" aria-label="Navegación docente">
        <a class="volver" href="listado_alumnos.php">← Volver a mis alumnos</a>
        <div class="acciones-cabecera">
            <a class="boton-enlace boton-perfil" href="editar_perfil.php">Editar perfil</a>
            <form action="../../procesos/cerrar_sesion.php" method="post">
                <button class="boton-secundario" type="submit">Cerrar sesión</button>
            </form>
        </div>
    </nav>
    <header class="cabecera cabecera-ficha">
        <div>
            <p class="marca">Gnotas · Área docente</p>
            <h1><?= escaparFichaProfesor($alumno['nombre'] . ' ' . $alumno['apellidos']) ?></h1>
            <p class="subtitulo">
                <?= escaparFichaProfesor($alumno['curso']) ?>
                · Año académico <?= escaparFichaProfesor($alumno['grado_academico']) ?>
            </p>
        </div>
    </header>

    <?php if ($estado !== ''): ?>
        <?php
        $mensajes = [
            'creada' => 'La nota se ha añadido.',
            'actualizada' => 'La nota se ha actualizado.',
            'eliminada' => 'La nota se ha borrado.',
            'datos_invalidos' => 'Revisa la nota y las observaciones: los datos no son válidos.',
            'nota_existente' => 'Ya existe una nota para esa evaluación.',
        ];
        ?>
        <p class="mensaje <?= $estado === 'datos_invalidos' ? 'mensaje-error' : 'mensaje-exito' ?>" role="status">
            <?= escaparFichaProfesor($mensajes[$estado]) ?>
        </p>
    <?php endif; ?>

    <?php foreach ($asignaturas as $asignatura): ?>
        <section class="asignatura" aria-labelledby="asignatura-<?= (int) $asignatura['id_curso_asignatura'] ?>">
            <div class="asignatura-cabecera">
                <span class="codigo"><?= escaparFichaProfesor($asignatura['codigo']) ?></span>
                <h2 id="asignatura-<?= (int) $asignatura['id_curso_asignatura'] ?>">
                    <?= escaparFichaProfesor($asignatura['nombre']) ?>
                </h2>
            </div>
            <div class="evaluaciones">
                <?php for ($evaluacion = 1; $evaluacion <= 3; $evaluacion++): ?>
                    <?php $notaExistente = $asignatura['notas'][$evaluacion] ?? null; ?>
                    <article class="evaluacion">
                        <h3><?= $evaluacion ?>.ª evaluación</h3>
                        <?php if ($notaExistente !== null): ?>
                            <form action="../../procesos/actualizar_nota_profesor.php" method="post">
                                <input type="hidden" name="id_alumno" value="<?= (int) $alumno['id_alumno'] ?>">
                                <input type="hidden" name="id_nota" value="<?= $notaExistente['id_nota'] ?>">
                                <label for="nota-<?= $notaExistente['id_nota'] ?>">Nota (0–10)</label>
                                <input
                                    type="number"
                                    id="nota-<?= $notaExistente['id_nota'] ?>"
                                    name="nota"
                                    min="0"
                                    max="10"
                                    step="0.01"
                                    value="<?= escaparFichaProfesor($notaExistente['nota']) ?>"
                                    required
                                >
                                <label for="observaciones-<?= $notaExistente['id_nota'] ?>">Observaciones</label>
                                <textarea
                                    id="observaciones-<?= $notaExistente['id_nota'] ?>"
                                    name="observaciones"
                                    maxlength="255"
                                    rows="2"
                                ><?= escaparFichaProfesor($notaExistente['observaciones'] ?? '') ?></textarea>
                                <button type="submit">Guardar cambios</button>
                            </form>
                            <form
                                class="form-borrar"
                                action="../../procesos/eliminar_nota_profesor.php"
                                method="post"
                                onsubmit="return confirm('¿Quieres borrar esta nota?');"
                            >
                                <input type="hidden" name="id_alumno" value="<?= (int) $alumno['id_alumno'] ?>">
                                <input type="hidden" name="id_nota" value="<?= $notaExistente['id_nota'] ?>">
                                <button class="boton-borrar" type="submit">Borrar nota</button>
                            </form>
                        <?php else: ?>
                            <form action="../../procesos/crear_nota_profesor.php" method="post">
                                <input type="hidden" name="id_alumno" value="<?= (int) $alumno['id_alumno'] ?>">
                                <input
                                    type="hidden"
                                    name="id_curso_asignatura"
                                    value="<?= $asignatura['id_curso_asignatura'] ?>"
                                >
                                <input type="hidden" name="evaluacion" value="<?= $evaluacion ?>">
                                <label for="nota-nueva-<?= $asignatura['id_curso_asignatura'] ?>-<?= $evaluacion ?>">
                                    Añadir nota (0–10)
                                </label>
                                <input
                                    type="number"
                                    id="nota-nueva-<?= $asignatura['id_curso_asignatura'] ?>-<?= $evaluacion ?>"
                                    name="nota"
                                    min="0"
                                    max="10"
                                    step="0.01"
                                    required
                                >
                                <label for="observaciones-nuevas-<?= $asignatura['id_curso_asignatura'] ?>-<?= $evaluacion ?>">
                                    Observaciones
                                </label>
                                <textarea
                                    id="observaciones-nuevas-<?= $asignatura['id_curso_asignatura'] ?>-<?= $evaluacion ?>"
                                    name="observaciones"
                                    maxlength="255"
                                    rows="2"
                                ></textarea>
                                <button type="submit">Añadir nota</button>
                            </form>
                        <?php endif; ?>
                    </article>
                <?php endfor; ?>
            </div>
        </section>
    <?php endforeach; ?>
</main>
</body>
</html>
