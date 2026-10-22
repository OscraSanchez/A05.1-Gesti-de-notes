DROP DATABASE IF EXISTS gestio_notes;
CREATE DATABASE gestio_notes CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gestio_notes;

-- -----------------------------------------------------
-- 1. ESTRUCTURA (simplificada: solo PK y FK imprescindibles)
-- -----------------------------------------------------

CREATE TABLE tb_roles (
    id_rol  INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre  VARCHAR(30)  NOT NULL,
    PRIMARY KEY (id_rol)
) ENGINE=InnoDB;

CREATE TABLE tb_usuarios (
    id_usuario  INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_rol      INT UNSIGNED NOT NULL,
    nombre      VARCHAR(60)  NOT NULL,
    apellidos   VARCHAR(100) NOT NULL,
    email       VARCHAR(120) NOT NULL,
    password    VARCHAR(255) NOT NULL,
    PRIMARY KEY (id_usuario),
    FOREIGN KEY (id_rol) REFERENCES tb_roles (id_rol)
) ENGINE=InnoDB;

-- Año académico (permite reutilizar la estructura cada año)
CREATE TABLE tb_grado_academico (
    id_grado      INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre        VARCHAR(9)   NOT NULL,        -- p.ej. 2025-2026
    fecha_inicio  DATE         NOT NULL,
    fecha_fin     DATE         NOT NULL,
    PRIMARY KEY (id_grado)
) ENGINE=InnoDB;

-- Clase / grupo (1r ESO A, 2n Batxillerat B...). Etapa y nivel permiten ampliar.
CREATE TABLE tb_curso (
    id_curso  INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_grado  INT UNSIGNED NOT NULL,
    nombre    VARCHAR(60)  NOT NULL,
    etapa     VARCHAR(40)  NOT NULL,            -- ESO, Batxillerat, FP...
    nivel     TINYINT UNSIGNED NOT NULL,
    grupo     CHAR(1)      NOT NULL DEFAULT 'A',
    PRIMARY KEY (id_curso),
    FOREIGN KEY (id_grado) REFERENCES tb_grado_academico (id_grado)
) ENGINE=InnoDB;

CREATE TABLE tb_alumnos (
    id_alumno         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_curso          INT UNSIGNED NOT NULL,
    dni               VARCHAR(9)   NOT NULL,
    fecha_nacimiento  DATE         NOT NULL,
    PRIMARY KEY (id_alumno),
    FOREIGN KEY (id_curso)   REFERENCES tb_curso (id_curso)
) ENGINE=InnoDB;

CREATE TABLE tb_asignatura (
    id_asignatura  INT UNSIGNED NOT NULL AUTO_INCREMENT,
    codigo         VARCHAR(10)  NOT NULL,
    nombre         VARCHAR(80)  NOT NULL,
    PRIMARY KEY (id_asignatura)
) ENGINE=InnoDB;

-- Qué asignaturas tiene cada curso y quién las imparte
CREATE TABLE tb_curso_asignatura (
    id_curso_asignatura  INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_curso             INT UNSIGNED NOT NULL,
    id_asignatura        INT UNSIGNED NOT NULL,
    id_profesor          INT UNSIGNED NOT NULL,
    PRIMARY KEY (id_curso_asignatura),
    FOREIGN KEY (id_curso)      REFERENCES tb_curso (id_curso),
    FOREIGN KEY (id_asignatura) REFERENCES tb_asignatura (id_asignatura),
    FOREIGN KEY (id_profesor)   REFERENCES tb_usuarios (id_usuario)
) ENGINE=InnoDB;

-- Notas: evaluacion = 1, 2, 3 (trimestre), sin tabla extra
CREATE TABLE tb_notas (
    id_nota              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_alumno            INT UNSIGNED NOT NULL,
    id_curso_asignatura  INT UNSIGNED NOT NULL,
    evaluacion           TINYINT UNSIGNED NOT NULL DEFAULT 1,
    nota                 DECIMAL(4,2) NOT NULL,
    observaciones        VARCHAR(255) NULL,
    PRIMARY KEY (id_nota),
    FOREIGN KEY (id_alumno)           REFERENCES tb_alumnos (id_alumno),
    FOREIGN KEY (id_curso_asignatura) REFERENCES tb_curso_asignatura (id_curso_asignatura)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- 2. DATOS
-- -----------------------------------------------------

INSERT INTO tb_roles (id_rol, nombre) VALUES
(1, 'administrador'),
(2, 'profesor'),
(3, 'alumno');

-- Contraseñas: hashes bcrypt de ejemplo (no son contraseñas reales)
INSERT INTO tb_usuarios (id_usuario, id_rol, nombre, apellidos, email, password) VALUES
(1,  1, 'Marta',    'Serra Ferrer',     'm.serra@iesexemple.cat',                '$2y$10$abcdefghijklmnopqrstuuAd01'),
(2,  2, 'Joan',     'Ferrer Bonet',     'j.ferrer@iesexemple.cat',               '$2y$10$abcdefghijklmnopqrstuuPr01'),
(3,  2, 'Catalina', 'Vidal Mas',        'c.vidal@iesexemple.cat',                '$2y$10$abcdefghijklmnopqrstuuPr02'),
(4,  2, 'David',    'Roca Pons',        'd.roca@iesexemple.cat',                 '$2y$10$abcdefghijklmnopqrstuuPr03'),
(5,  2, 'Laura',    'Oliver Campins',   'l.oliver@iesexemple.cat',               '$2y$10$abcdefghijklmnopqrstuuPr04'),
(6,  3, 'Pau',      'Garcia Moll',      'pau.garcia@alumnes.iesexemple.cat',     '$2y$10$abcdefghijklmnopqrstuuAl01'),
(7,  3, 'Aina',     'Martorell Sastre', 'aina.martorell@alumnes.iesexemple.cat', '$2y$10$abcdefghijklmnopqrstuuAl02'),
(8,  3, 'Biel',     'Ramis Torres',     'biel.ramis@alumnes.iesexemple.cat',     '$2y$10$abcdefghijklmnopqrstuuAl03'),
(9,  3, 'Júlia',    'Cànaves Ordines',  'julia.canaves@alumnes.iesexemple.cat',  '$2y$10$abcdefghijklmnopqrstuuAl04'),
(10, 3, 'Marc',     'Pons Salom',       'marc.pons@alumnes.iesexemple.cat',      '$2y$10$abcdefghijklmnopqrstuuAl05'),
(11, 3, 'Neus',     'Amengual Vich',    'neus.amengual@alumnes.iesexemple.cat',  '$2y$10$abcdefghijklmnopqrstuuAl06'),
(12, 3, 'Lluc',     'Mir Bauzà',        'lluc.mir@alumnes.iesexemple.cat',       '$2y$10$abcdefghijklmnopqrstuuAl07'),
(13, 3, 'Clàudia',  'Juan Rosselló',    'claudia.juan@alumnes.iesexemple.cat',   '$2y$10$abcdefghijklmnopqrstuuAl08'),
(14, 3, 'Arnau',    'Sureda Mateu',     'arnau.sureda@alumnes.iesexemple.cat',   '$2y$10$abcdefghijklmnopqrstuuAl09'),
(15, 3, 'Maria',    'Nadal Quetglas',   'maria.nadal@alumnes.iesexemple.cat',    '$2y$10$abcdefghijklmnopqrstuuAl10'),
(16, 3, 'Pol',      'Horrach Llabrés',  'pol.horrach@alumnes.iesexemple.cat',    '$2y$10$abcdefghijklmnopqrstuuAl11'),
(17, 3, 'Sara',     'Perelló Cardell',  'sara.perello@alumnes.iesexemple.cat',   '$2y$10$abcdefghijklmnopqrstuuAl12');

INSERT INTO tb_grado_academico (id_grado, nombre, fecha_inicio, fecha_fin) VALUES
(1, '2025-2026', '2025-09-10', '2026-06-19');

INSERT INTO tb_curso (id_curso, id_grado, nombre, etapa, nivel, grupo) VALUES
(1, 1, '1r ESO A', 'ESO', 1, 'A'),
(2, 1, '1r ESO B', 'ESO', 1, 'B'),
(3, 1, '2n ESO A', 'ESO', 2, 'A');

INSERT INTO tb_alumnos (id_alumno, id_usuario, id_curso, dni, fecha_nacimiento) VALUES
(1,  6,  1, '43012345K', '2013-02-14'),
(2,  7,  1, '43023456L', '2013-05-30'),
(3,  8,  1, '43034567M', '2013-09-08'),
(4,  9,  1, '43045678N', '2013-11-21'),
(5,  10, 2, '43056789P', '2013-01-17'),
(6,  11, 2, '43067890Q', '2013-04-03'),
(7,  12, 2, '43078901R', '2013-07-26'),
(8,  13, 2, '43089012S', '2013-12-12'),
(9,  14, 3, '42990123T', '2012-03-09'),
(10, 15, 3, '42901234V', '2012-06-18'),
(11, 16, 3, '42812345W', '2012-08-29'),
(12, 17, 3, '42723456X', '2012-10-05');

INSERT INTO tb_asignatura (id_asignatura, codigo, nombre) VALUES
(1, 'MAT', 'Matemàtiques'),
(2, 'CAT', 'Llengua Catalana i Literatura'),
(3, 'CAS', 'Llengua Castellana i Literatura'),
(4, 'ANG', 'Anglès'),
(5, 'BIO', 'Biologia i Geologia'),
(6, 'TEC', 'Tecnologia i Digitalització');

INSERT INTO tb_curso_asignatura (id_curso_asignatura, id_curso, id_asignatura, id_profesor) VALUES
(1,  1, 1, 2), (2,  1, 2, 3), (3,  1, 3, 3), (4,  1, 4, 4), (5,  1, 5, 5),
(6,  2, 1, 2), (7,  2, 2, 3), (8,  2, 3, 3), (9,  2, 4, 4), (10, 2, 6, 5),
(11, 3, 1, 2), (12, 3, 2, 3), (13, 3, 3, 3), (14, 3, 4, 4), (15, 3, 5, 5), (16, 3, 6, 5);

-- Notas de las 3 evaluaciones para cada alumno y asignatura de su curso.
-- Fórmula determinista para obtener valores variados (entre 4,0 y 9,5).
INSERT INTO tb_notas (id_alumno, id_curso_asignatura, evaluacion, nota)
SELECT a.id_alumno,
       ca.id_curso_asignatura,
       e.n,
       ROUND(4 + MOD(a.id_alumno * 7 + ca.id_curso_asignatura * 13 + e.n * 5, 56) / 10, 1)
FROM tb_alumnos a
JOIN tb_curso_asignatura ca ON ca.id_curso = a.id_curso
JOIN (SELECT 1 AS n UNION SELECT 2 UNION SELECT 3) e;