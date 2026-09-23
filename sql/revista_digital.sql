-- ============================================================
-- Script de creación de base de datos
-- Motor: MySQL 8.0+
-- ============================================================

CREATE DATABASE IF NOT EXISTS revista_digital
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE revista_digital;

SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- Tabla: usuarios
-- ------------------------------------------------------------
CREATE TABLE usuarios (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombres         VARCHAR(100) NOT NULL,
    ap_paterno      VARCHAR(100) NOT NULL,
    ap_materno      VARCHAR(100) NULL,
    email           VARCHAR(150) NOT NULL,
    password_hash   VARCHAR(255) NOT NULL,
    rol             ENUM('admin', 'editor', 'redactor') NOT NULL DEFAULT 'redactor',
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uq_usuarios_email UNIQUE (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabla: autores
-- ------------------------------------------------------------
CREATE TABLE autores (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombres         VARCHAR(100) NOT NULL,
    ap_paterno      VARCHAR(100) NULL,
    ap_materno      VARCHAR(100) NULL,
    nickname        VARCHAR(100) NULL,
    es_nickname     TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabla: reportajes
-- ------------------------------------------------------------
CREATE TABLE reportajes (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titulo              VARCHAR(255) NOT NULL,
    resumen_corto       VARCHAR(500) NULL,
    desarrollo          LONGTEXT NOT NULL,
    foto_principal      VARCHAR(255) NULL,
    pdf_adjunto         VARCHAR(255) NULL,
    fecha_publicacion   DATE NOT NULL,
    es_destacado        TINYINT(1) NOT NULL DEFAULT 0,
    autor_id            INT UNSIGNED NOT NULL,
    usuario_id          INT UNSIGNED NOT NULL,
    created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_reportajes_autor
        FOREIGN KEY (autor_id) REFERENCES autores(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_reportajes_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX idx_reportajes_fecha (fecha_publicacion),
    INDEX idx_reportajes_destacado (es_destacado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabla: reportajes_fotos
-- ------------------------------------------------------------
CREATE TABLE reportajes_fotos (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reportaje_id    INT UNSIGNED NOT NULL,
    url_foto        VARCHAR(255) NOT NULL,
    orden           SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    descripcion     VARCHAR(255) NULL,
    CONSTRAINT fk_reportajes_fotos_reportaje
        FOREIGN KEY (reportaje_id) REFERENCES reportajes(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    INDEX idx_reportajes_fotos_reportaje (reportaje_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabla: noticias
-- ------------------------------------------------------------
CREATE TABLE noticias (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titulo              VARCHAR(255) NOT NULL,
    foto                VARCHAR(255) NULL,
    link_externo        VARCHAR(500) NULL,
    fecha_publicacion   DATE NOT NULL,
    usuario_id          INT UNSIGNED NOT NULL,
    CONSTRAINT fk_noticias_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX idx_noticias_fecha (fecha_publicacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabla: boletines
-- ------------------------------------------------------------
CREATE TABLE boletines (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    numero_boletin      VARCHAR(50) NOT NULL,
    resumen             VARCHAR(500) NULL,
    foto_portada        VARCHAR(255) NULL,
    archivo_pdf         VARCHAR(255) NOT NULL,
    fecha_publicacion   DATE NOT NULL,
    usuario_id          INT UNSIGNED NOT NULL,
    CONSTRAINT uq_boletines_numero UNIQUE (numero_boletin),
    CONSTRAINT fk_boletines_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX idx_boletines_fecha (fecha_publicacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabla: podcasts
-- ------------------------------------------------------------
CREATE TABLE podcasts (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titulo              VARCHAR(255) NOT NULL,
    url_embed           VARCHAR(500) NOT NULL,
    fecha_publicacion   DATE NOT NULL,
    usuario_id          INT UNSIGNED NOT NULL,
    CONSTRAINT fk_podcasts_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX idx_podcasts_fecha (fecha_publicacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabla: videos
-- ------------------------------------------------------------
CREATE TABLE videos (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titulo              VARCHAR(255) NOT NULL,
    url_embed           VARCHAR(500) NOT NULL,
    fecha_publicacion   DATE NOT NULL,
    usuario_id          INT UNSIGNED NOT NULL,
    CONSTRAINT fk_videos_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX idx_videos_fecha (fecha_publicacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;-- ============================================================
-- Datos de prueba (seed) para revista_digital
-- Contraseñas en texto plano (solo para referencia en README):
--   admin@revista.pe     -> admin123
--   editor@revista.pe    -> editor123
--   redactor@revista.pe  -> redactor123
-- ============================================================

INSERT INTO usuarios (nombres, ap_paterno, ap_materno, email, password_hash, rol) VALUES
('Arnold', 'Quispe', 'Mamani', 'admin@revista.pe', '$2y$10$ZPWfQxV/J7c2pu1O6wvwTemMwsZOfc1mjZoFU5upHo93UeQ0jHNKC', 'admin'),
('Milagros', 'Huamán', 'Ccorimanya', 'editor@revista.pe', '$2y$10$LaBt/dLU9ZogcRHs0pdMDu092FbzcRdfEbbtuE5VgDBE6enrST3QG', 'editor'),
('Renzo', 'Apaza', 'Ttito', 'redactor@revista.pe', '$2y$10$9A92B71euJ83p7s0gRUPM.LOnmdvi7De3Pm9ah0TLG6jGz9UPMLRy', 'redactor');

INSERT INTO autores (nombres, ap_paterno, ap_materno, nickname, es_nickname) VALUES
('Redacción', NULL, NULL, 'Redacción NTEP', 1),
('Fiorella', 'Condori', 'Salas', NULL, 0),
('Jhon', 'Mamani', 'Quispe', 'J. Mamani', 1);

INSERT INTO reportajes (titulo, resumen_corto, desarrollo, foto_principal, pdf_adjunto, fecha_publicacion, es_destacado, autor_id, usuario_id) VALUES
('Cusco impulsa el turismo sostenible en Machu Picchu', 'Nuevas medidas buscan reducir el impacto ambiental en la ciudadela inca.', 'Cuerpo completo del reportaje sobre las medidas de turismo sostenible implementadas en Machu Picchu durante 2026, incluyendo cupos diarios, rutas alternas y programas de educación ambiental para visitantes y operadores turísticos.', NULL, NULL, '2026-08-15', 1, 2, 1),
('La UAC presenta avances en proyectos de ingeniería de sistemas', 'Estudiantes exponen soluciones tecnológicas aplicadas a problemas regionales.', 'Cuerpo completo del reportaje sobre la feria de proyectos de la Escuela Profesional de Ingeniería de Sistemas, con énfasis en soluciones de software orientadas a la región Cusco.', NULL, NULL, '2026-08-28', 0, 3, 1),
('Emprendimientos gastronómicos crecen en Aguas Calientes', 'Negocios locales apuestan por la cocina andina fusión para atraer turistas.', 'Cuerpo completo del reportaje sobre el crecimiento de emprendimientos gastronómicos en el distrito de Machupicchu Pueblo (Aguas Calientes) y su relación con el flujo turístico hacia la ciudadela.', NULL, NULL, '2026-09-01', 1, 1, 2);

INSERT INTO noticias (titulo, foto, link_externo, fecha_publicacion, usuario_id) VALUES
('Municipalidad de Cusco anuncia mejoras viales', NULL, 'https://example.com/noticia-vial', '2026-09-02', 1),
('Feria artesanal se realizará este fin de semana', NULL, 'https://example.com/feria-artesanal', '2026-09-04', 2);

INSERT INTO boletines (numero_boletin, resumen, foto_portada, archivo_pdf, fecha_publicacion, usuario_id) VALUES
('NTEP-2026-08', 'Boletín mensual con resumen de actividades académicas y regionales de agosto.', NULL, 'boletin-2026-08.pdf', '2026-08-31', 1);

INSERT INTO podcasts (titulo, url_embed, fecha_publicacion, usuario_id) VALUES
('Voces de Cusco: Episodio 12 - Turismo responsable', 'https://open.spotify.com/embed/episode/example12', '2026-08-20', 2);

INSERT INTO videos (titulo, url_embed, fecha_publicacion, usuario_id) VALUES
('Recorrido virtual por el Valle Sagrado', 'https://www.youtube.com/embed/example-valle-sagrado', '2026-08-10', 1);
