-- ============================================================
-- Datos de prueba ADICIONALES para revista_digital
-- Este script se importa DESPUÉS de sql/revista_digital.sql
-- (agrega más registros, no reemplaza los que ya tienes).
--
-- Usa USE revista_digital; antes si tu cliente no la selecciona sola.
-- ============================================================
USE revista_digital;

-- ------------------------------------------------------------
-- Más autores
-- ------------------------------------------------------------
INSERT INTO autores (nombres, ap_paterno, ap_materno, nickname, es_nickname) VALUES
('Yesenia', 'Farfán', 'Quispe', NULL, 0),
('Marco', 'Villafuerte', 'Cusi', 'M. Villafuerte', 1),
('Katherine', 'Loayza', 'Pumayalli', NULL, 0),
('Diego', 'Serrano', 'Huillca', NULL, 0),
('Colaborador Externo', NULL, NULL, 'Corresponsal Quillabamba', 1);

-- ------------------------------------------------------------
-- Más reportajes (usa subconsultas para no depender de IDs fijos)
-- ------------------------------------------------------------
INSERT INTO reportajes (titulo, resumen_corto, desarrollo, foto_principal, pdf_adjunto, fecha_publicacion, es_destacado, autor_id, usuario_id)
VALUES
('Feria del Chuño reúne a productores de las alturas de Cusco',
 'Comunidades altoandinas exponen técnicas ancestrales de conservación de la papa.',
 'Cuerpo completo del reportaje sobre la Feria del Chuño realizada en la provincia de Canchis, con testimonios de productores sobre las técnicas tradicionales de deshidratación de la papa y su importancia para la seguridad alimentaria regional.',
 NULL, NULL, '2026-06-10', 0,
 (SELECT id FROM autores WHERE nombres='Yesenia' AND ap_paterno='Farfán' LIMIT 1),
 (SELECT id FROM usuarios WHERE email='editor@revista.pe' LIMIT 1)),

('Tren a Machu Picchu incrementa frecuencias en temporada alta',
 'PeruRail y Inca Rail anuncian nuevas salidas para julio y agosto.',
 'Cuerpo completo sobre el incremento de frecuencias de trenes hacia Aguas Calientes durante la temporada alta de turismo, con datos de ocupación y recomendaciones para visitantes.',
 NULL, NULL, '2026-06-22', 1,
 (SELECT id FROM autores WHERE nickname='M. Villafuerte' LIMIT 1),
 (SELECT id FROM usuarios WHERE email='admin@revista.pe' LIMIT 1)),

('Artesanos de Chinchero exportan textiles a Europa',
 'Asociaciones de tejedoras logran contratos internacionales tras feria en Lima.',
 'Cuerpo completo sobre el crecimiento de las exportaciones de textiles tradicionales de Chinchero, con entrevistas a las asociaciones de tejedoras y detalles sobre el proceso de certificación de comercio justo.',
 NULL, NULL, '2026-07-03', 0,
 (SELECT id FROM autores WHERE nombres='Katherine' LIMIT 1),
 (SELECT id FROM usuarios WHERE email='editor@revista.pe' LIMIT 1)),

('UAC inaugura laboratorio de innovación tecnológica',
 'El nuevo espacio busca fortalecer proyectos de estudiantes de Ingeniería.',
 'Cuerpo completo sobre la inauguración del laboratorio de innovación de la Universidad Andina del Cusco, equipado para proyectos de IoT, robótica y desarrollo de software, incluyendo declaraciones de autoridades académicas.',
 NULL, NULL, '2026-07-15', 1,
 (SELECT id FROM autores WHERE nombres='Diego' LIMIT 1),
 (SELECT id FROM usuarios WHERE email='admin@revista.pe' LIMIT 1)),

('Quillabamba celebra su Festival de la Naranja',
 'El evento atrae a miles de visitantes de la selva y la sierra cusqueña.',
 'Cuerpo completo sobre el Festival de la Naranja en La Convención, con cobertura de las actividades culturales, la feria agropecuaria y el impacto económico local del evento.',
 NULL, NULL, '2026-07-20', 0,
 (SELECT id FROM autores WHERE nickname='Corresponsal Quillabamba' LIMIT 1),
 (SELECT id FROM usuarios WHERE email='redactor@revista.pe' LIMIT 1)),

('Cusco registra récord de llegada de turistas extranjeros',
 'Cifras de agosto superan las proyecciones del sector.',
 'Cuerpo completo sobre las cifras de turismo receptivo en la región Cusco durante agosto de 2026, con análisis de las principales nacionalidades de origen y su impacto en la economía local.',
 NULL, NULL, '2026-08-05', 1,
 (SELECT id FROM autores WHERE nickname='Redacción NTEP' LIMIT 1),
 (SELECT id FROM usuarios WHERE email='admin@revista.pe' LIMIT 1)),

('Jóvenes cusqueños desarrollan app para reportar baches',
 'El proyecto universitario ya se prueba en tres distritos de la ciudad.',
 'Cuerpo completo sobre una aplicación móvil desarrollada por estudiantes cusqueños para el reporte ciudadano de baches y daños en la vía pública, en coordinación con las municipalidades distritales.',
 NULL, NULL, '2026-08-30', 0,
 (SELECT id FROM autores WHERE nombres='Diego' LIMIT 1),
 (SELECT id FROM usuarios WHERE email='redactor@revista.pe' LIMIT 1));

-- ------------------------------------------------------------
-- Fotos adicionales para algunos reportajes
-- ------------------------------------------------------------
INSERT INTO reportajes_fotos (reportaje_id, url_foto, orden, descripcion)
SELECT id, 'fotos/tren-machu-picchu-1.jpg', 1, 'Tren saliendo de la estación de Poroy'
FROM reportajes WHERE titulo LIKE 'Tren a Machu Picchu%' LIMIT 1;

INSERT INTO reportajes_fotos (reportaje_id, url_foto, orden, descripcion)
SELECT id, 'fotos/tren-machu-picchu-2.jpg', 2, 'Vagón panorámico'
FROM reportajes WHERE titulo LIKE 'Tren a Machu Picchu%' LIMIT 1;

INSERT INTO reportajes_fotos (reportaje_id, url_foto, orden, descripcion)
SELECT id, 'fotos/laboratorio-uac-1.jpg', 1, 'Inauguración del laboratorio de innovación'
FROM reportajes WHERE titulo LIKE 'UAC inaugura laboratorio%' LIMIT 1;

-- ------------------------------------------------------------
-- Más noticias
-- ------------------------------------------------------------
INSERT INTO noticias (titulo, foto, link_externo, fecha_publicacion, usuario_id) VALUES
('Corte de agua programado en San Sebastián', NULL, 'https://example.com/corte-agua-sansebastian', '2026-09-05', (SELECT id FROM usuarios WHERE email='redactor@revista.pe' LIMIT 1)),
('Cusco será sede de congreso internacional de turismo', NULL, 'https://example.com/congreso-turismo-cusco', '2026-09-06', (SELECT id FROM usuarios WHERE email='editor@revista.pe' LIMIT 1)),
('Municipalidad lanza campaña de arborización urbana', NULL, 'https://example.com/arborizacion-cusco', '2026-09-06', (SELECT id FROM usuarios WHERE email='admin@revista.pe' LIMIT 1)),
('Aeropuerto de Chinchero: avance de obras al 60%', NULL, 'https://example.com/aeropuerto-chinchero-avance', '2026-09-07', (SELECT id FROM usuarios WHERE email='admin@revista.pe' LIMIT 1)),
('Alerta por lluvias intensas en la provincia de La Convención', NULL, 'https://example.com/alerta-lluvias-convencion', '2026-09-07', (SELECT id FROM usuarios WHERE email='redactor@revista.pe' LIMIT 1));

-- ------------------------------------------------------------
-- Más boletines
-- ------------------------------------------------------------
INSERT INTO boletines (numero_boletin, resumen, foto_portada, archivo_pdf, fecha_publicacion, usuario_id) VALUES
('NTEP-2026-06', 'Boletín mensual con resumen de actividades académicas y regionales de junio.', NULL, 'boletin-2026-06.pdf', '2026-06-30', (SELECT id FROM usuarios WHERE email='admin@revista.pe' LIMIT 1)),
('NTEP-2026-07', 'Boletín mensual con resumen de actividades académicas y regionales de julio.', NULL, 'boletin-2026-07.pdf', '2026-07-31', (SELECT id FROM usuarios WHERE email='editor@revista.pe' LIMIT 1)),
('NTEP-2026-09', 'Boletín mensual con resumen de actividades académicas y regionales de septiembre (edición preliminar).', NULL, 'boletin-2026-09.pdf', '2026-09-05', (SELECT id FROM usuarios WHERE email='admin@revista.pe' LIMIT 1));

-- ------------------------------------------------------------
-- Más podcasts
-- ------------------------------------------------------------
INSERT INTO podcasts (titulo, url_embed, fecha_publicacion, usuario_id) VALUES
('Voces de Cusco: Episodio 13 - Emprendimiento juvenil', 'https://open.spotify.com/embed/episode/example13', '2026-08-27', (SELECT id FROM usuarios WHERE email='editor@revista.pe' LIMIT 1)),
('Voces de Cusco: Episodio 14 - Patrimonio cultural inmaterial', 'https://open.spotify.com/embed/episode/example14', '2026-09-03', (SELECT id FROM usuarios WHERE email='admin@revista.pe' LIMIT 1)),
('Entrevista especial: rector de la UAC', 'https://open.spotify.com/embed/episode/example-rector', '2026-09-05', (SELECT id FROM usuarios WHERE email='admin@revista.pe' LIMIT 1));

-- ------------------------------------------------------------
-- Más videos
-- ------------------------------------------------------------
INSERT INTO videos (titulo, url_embed, fecha_publicacion, usuario_id) VALUES
('Así avanza la obra del aeropuerto de Chinchero', 'https://www.youtube.com/embed/example-chinchero', '2026-08-25', (SELECT id FROM usuarios WHERE email='redactor@revista.pe' LIMIT 1)),
('Feria del Chuño 2026: mira lo mejor del evento', 'https://www.youtube.com/embed/example-feria-chuno', '2026-06-12', (SELECT id FROM usuarios WHERE email='editor@revista.pe' LIMIT 1)),
('Tour virtual: laboratorio de innovación UAC', 'https://www.youtube.com/embed/example-lab-uac', '2026-07-16', (SELECT id FROM usuarios WHERE email='admin@revista.pe' LIMIT 1));
