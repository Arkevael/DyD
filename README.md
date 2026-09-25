
[ DyD — Panel administrativo + sitio público
 README técnico del proyecto (léeme antes de tocar el código)


Este documento explica CÓMO ESTÁ ARMADO todo el proyecto, para que
cualquiera que quiera agregar o cambiar algo entienda rápido dónde
tiene que meter mano, sin tener que adivinar.


----------------------------------------------------------------
1. QUÉ ES ESTE PROYECTO
----------------------------------------------------------------

Es un sistema hecho en PHP + MySQL con dos partes:

  A) UN PANEL DE ADMINISTRACIÓN (requiere iniciar sesión)
     Ahí se crean, editan y borran reportajes, noticias, boletines,
     podcasts, videos, autores y usuarios. Está construido sobre la
     plantilla visual "Adminator".

  B) UN SITIO PÚBLICO (public/, sin necesidad de iniciar sesión)
     Es lo que ve cualquier visitante: muestra en tiempo real todo
     lo que se publicó desde el panel. Está construido con el
     diseño real del sitio "Diálogo y Desarrollo Perú" (maquetación
     Bootstrap 4 + Font Awesome).

Los dos lados leen y escriben en LA MISMA base de datos MySQL,
llamada `revista_digital`. No hay API intermedia: el PHP se conecta
directo a la base con PDO.

No usa ningún framework (no Laravel, no Symfony). Es PHP "plano",
página por página. Cada archivo .php de la raíz es una página.


----------------------------------------------------------------
2. TECNOLOGÍAS USADAS
----------------------------------------------------------------

- PHP 8.x (con PDO para la base de datos)
- MySQL / MariaDB
- HTML + CSS + JavaScript "vanilla" (sin React, sin Vue, sin build
  step — no hay que compilar nada, los archivos se suben tal cual)
- Panel admin: plantilla "Adminator" (style.css, 2026.js, runtime.js)
- Sitio público: plantilla real de noticias (Bootstrap 4, jQuery,
  Font Awesome, tipografía Cabin) en public/assets/


----------------------------------------------------------------
3. CÓMO ESTÁ ORGANIZADA LA CARPETA DEL PROYECTO
----------------------------------------------------------------

revista-admin-panel/
│
├── config/
│   └── db.php                 → ÚNICO lugar donde se configura la
│                                 conexión a MySQL (host, usuario,
│                                 contraseña, nombre de la base).
│                                 Todas las páginas lo incluyen con
│                                 require.
│
├── includes/                  → Funciones/lógica que se reutiliza
│   ├── auth.php                 Protege una página: si no hay sesión
│   │                             iniciada, redirige a login.php.
│   │                             Deja disponible $usuarioActual con
│   │                             id, nombres, rol y email.
│   ├── upload.php                subirArchivo() y borrarArchivo():
│   │                             suben imágenes/PDFs a uploads/ con
│   │                             límite de tamaño y extensión, y
│   │                             borran el archivo anterior al
│   │                             reemplazarlo.
│   ├── sanitize.php              sanitizarHtmlBasico(): limpia el
│   │                             HTML que llega del editor de texto
│   │                             enriquecido (quita <script>,
│   │                             onclick, etc.) antes de guardar.
│   └── embed_helper.php          normalizarUrlEmbed(): convierte un
│                                 link normal de YouTube/Spotify (el
│                                 que la gente copia del navegador)
│                                 a su versión /embed/, que es la
│                                 única que se puede meter en un
│                                 <iframe>.
│
├── uploads/                   → Aquí caen los archivos que suben los
│   ├── reportajes/               usuarios desde el panel (fotos,
│   ├── noticias/                 PDFs). Cada tabla tiene su propia
│   ├── boletines/                subcarpeta. Tiene un .htaccess que
│   └── pdfs/                     impide ejecutar PHP ahí, por
│                                 seguridad.
│
├── sql/
│   ├── revista_digital.sql       Esquema completo (CREATE TABLE de
│   │                             las 8 tablas) + datos de prueba
│   │                             (3 usuarios, autores, reportajes...).
│   │                             Esto es lo primero que se importa
│   │                             en phpMyAdmin al instalar el
│   │                             proyecto en un servidor nuevo.
│   └── datos_adicionales.sql     Más datos de ejemplo (opcional).
│
├── (raíz del proyecto)        → Páginas del PANEL DE ADMINISTRACIÓN.
│   login.php / logout.php        Cada tabla del modelo tiene 3
│   index.php                     archivos: listar / formulario
│   reportajes.php                (crear y editar en uno solo) /
│   reportaje_form.php            eliminar. Ejemplo con "reportaje":
│   reportaje_delete.php            reportajes.php       → listado
│   noticias.php / noticia_form.php / noticia_delete.php  reportaje_form.php   → crear/editar
│   boletines.php / boletin_form.php / boletin_delete.php reportaje_delete.php → eliminar
│   podcasts.php / podcast_form.php / podcast_delete.php
│   videos.php / video_form.php / video_delete.php
│   autores.php / autor_form.php / autor_delete.php
│   usuarios.php / usuario_form.php / usuario_delete.php (solo admin)
│
│   rich-editor.js               Editor de texto enriquecido (negrita,
│                                 listas, citas, enlaces) para el campo
│                                 "Desarrollo" de Reportajes.
│   autosave-draft.js            Guarda el formulario como borrador en
│                                 el navegador (localStorage) mientras
│                                 se escribe, por si se cierra la
│                                 sesión o la pestaña sin guardar.
│   file-validate.js             Avisa en el navegador si una imagen o
│                                 PDF pesa más de lo permitido, ANTES
│                                 de intentar subirlo.
│   admin-extra.css              Estilos del editor de texto y del
│                                 aviso de "se encontró un borrador".
│
│   2026.js, runtime.js,         Archivos propios de la plantilla
│   style.css, vendors.js, etc.  Adminator. El menú lateral (qué
│                                 aparece y en qué orden) se define
│                                 dentro de 2026.js, buscando la
│                                 variable que arranca con
│                                 label:"Workspace".
│
│   email.html, chat.html,       Páginas de DEMOSTRACIÓN que trae la
│   calendar.html, charts.html,  plantilla Adminator de fábrica. NO
│   etc.                          están conectadas a la base de datos.
│                                 Se pueden borrar sin que nada se
│                                 rompa, o usarlas de inspiración si
│                                 se quiere agregar una sección nueva.
│
└── public/                    → SITIO PÚBLICO (sin login).
    ├── includes/
    │   ├── db_publico.php        Conecta a la base (solo lectura) y
    │   │                          trae funciones de ayuda: nombreAutor(),
    │   │                          fechaBonita(), resumirTexto(),
    │   │                          paginaActual(), renderPaginacion().
    │   ├── header.php             Navbar + banda de título de cada
    │   │                          página pública.
    │   ├── footer.php             Pie de página + scripts.
    │   └── sidebar.php            Barra lateral de "Últimos reportajes"
    │                              + "Archivo por mes" (se usa en la
    │                              página de detalle de un reportaje).
    │
    ├── index.php                  Inicio: reportaje destacado grande,
    │                               reportajes recientes, noticias
    │                               recientes, boletines, podcasts y
    │                               videos recientes. (No pagina.)
    ├── reportajes.php              Listado de TODOS los reportajes,
    │                               con paginación (9 por página) y
    │                               filtro por mes (?mes=2026-08).
    ├── reportaje.php?id=N          Detalle de un reportaje: imagen,
    │                               cita destacada, cuerpo (admite el
    │                               HTML del editor enriquecido),
    │                               galería de fotos, sidebar.
    ├── noticias.php                Listado de noticias, con paginación.
    ├── boletines.php               Listado de boletines (descarga PDF).
    ├── podcasts.php / videos.php   Listado con reproductor embebido.
    ├── contacto.php                Página "Sobre DyD".
    └── assets/
        ├── css/style-starter.css     CSS real de la plantilla del
        │                              sitio (no tocar salvo que sepas
        │                              lo que haces, es muy grande).
        ├── css/custom.css             Overrides propios: tarjetas de
        │                              tamaño uniforme, efecto de
        │                              "recorte" en las imágenes,
        │                              estilo del contenido enriquecido.
        │                              ESTE es el archivo a editar si
        │                              se quiere cambiar el look.
        ├── js/                        jQuery, Bootstrap, Owl Carousel.
        ├── fonts/                     Font Awesome.
        └── images/logo.png            Logo del sitio.


----------------------------------------------------------------
4. BASE DE DATOS: LAS 8 TABLAS
----------------------------------------------------------------

Base de datos: revista_digital

  usuarios
    id, nombres, ap_paterno, ap_materno, email, password_hash,
    rol (admin | editor | redactor), created_at
    → Cuentas para entrar al panel. password_hash se genera con
      password_hash() de PHP (bcrypt), NUNCA se guarda en texto plano.

  autores
    id, nombres, ap_paterno, ap_materno, nickname, es_nickname
    → Quién firma un reportaje. Puede mostrarse con su nombre real
      o con un "nickname" (ej. "Redacción DyD") si es_nickname = 1.

  reportajes
    id, titulo, resumen_corto, desarrollo (HTML del editor),
    foto_principal, pdf_adjunto, fecha_publicacion, es_destacado,
    autor_id (FK → autores), usuario_id (FK → usuarios),
    created_at, updated_at

  reportajes_fotos
    id, reportaje_id (FK → reportajes, se borra en cascada),
    url_foto, orden, descripcion
    → Galería de fotos extra de un reportaje (aún sin pantalla propia
      en el panel para administrarlas una por una — ver sección 6).

  noticias
    id, titulo, foto, link_externo, fecha_publicacion,
    usuario_id (FK → usuarios)

  boletines
    id, numero_boletin (único), resumen, foto_portada,
    archivo_pdf (obligatorio), fecha_publicacion,
    usuario_id (FK → usuarios)

  podcasts
    id, titulo, url_embed, fecha_publicacion,
    usuario_id (FK → usuarios)

  videos
    id, titulo, url_embed, fecha_publicacion,
    usuario_id (FK → usuarios)

Reglas importantes:
  - No se puede borrar un autor que tenga reportajes (autor_delete.php
    lo bloquea y avisa).
  - No se puede borrar un usuario que tenga contenido publicado a su
    nombre, ni eliminarse a sí mismo (usuario_delete.php).
  - Todo lo demás en cascada normal (reportajes_fotos se borra solo
    si se borra el reportaje).


----------------------------------------------------------------
5. CÓMO FUNCIONA CADA PARTE (para no perderse)
----------------------------------------------------------------

LOGIN Y SESIONES
  login.php valida el correo/contraseña contra la tabla `usuarios`
  con password_verify(), y si es correcto guarda en $_SESSION:
  usuario_id, usuario_nombres, usuario_rol, usuario_email.
  Toda página del panel empieza con:
      require __DIR__ . '/includes/auth.php';
  eso ya se encarga de mandar a login.php si no hay sesión, y deja
  lista la variable $usuarioActual.

ROLES
  Solo existe una restricción real por rol: usuarios.php (y sus
  archivos _form/_delete) verifican a mano que $usuarioActual['rol']
  === 'admin'. El resto de módulos (reportajes, noticias, etc.) los
  puede usar cualquier rol logueado (admin, editor o redactor).

SUBIDA DE ARCHIVOS
  Cada _form.php que tiene una imagen o PDF llama a:
      subirArchivo('nombre_del_input', 'subcarpeta', ['ext','ext'], maxBytes)
  Límites actuales: imágenes 5 MB, PDFs 15 MB. Si el campo va vacío
  (el usuario no seleccionó archivo nuevo), la función devuelve null
  y el _form.php conserva la imagen/PDF que ya había (no lo borra).
  Al subir uno nuevo, borrarArchivo() elimina el anterior del disco.

EDITOR DE TEXTO ENRIQUECIDO (solo en Reportajes → Desarrollo)
  rich-editor.js convierte el <textarea id="desarrollo"> en un editor
  visual (contenteditable) con una barra de herramientas. Cuando se
  envía el formulario, el HTML generado se sincroniza de vuelta al
  <textarea> y viaja en el POST tal cual. En el servidor,
  reportaje_form.php lo pasa por sanitizarHtmlBasico() ANTES de
  guardarlo (solo deja <p><br><b><strong><i><em><u><ul><ol><li>
  <blockquote><h3><a>, y limpia atributos on* y href peligrosos).
  En public/reportaje.php, si el campo `desarrollo` contiene alguna
  etiqueta HTML se imprime tal cual (ya viene limpio); si es texto
  plano antiguo (de antes de este editor), se sigue partiendo por
  párrafos como antes, por compatibilidad.

BORRADORES AUTOMÁTICOS
  autosave-draft.js guarda cada campo del formulario en
  localStorage cada vez que el usuario escribe (con un pequeño
  retraso). Si vuelve a abrir ese mismo formulario (nuevo o editando
  el mismo id) y hay un borrador más nuevo, se le ofrece recuperarlo
  o descartarlo. El borrador se borra solo al enviar el formulario
  con éxito. Está activo en Reportajes, Noticias y Boletines.

LINKS DE YOUTUBE / SPOTIFY (Podcasts y Videos)
  normalizarUrlEmbed() en includes/embed_helper.php convierte
  automáticamente un link normal (el que se copia del navegador) a
  su versión /embed/, tanto al guardar en el panel como al mostrarlo
  en el sitio público. Así el usuario puede pegar cualquier link de
  YouTube o Spotify sin preocuparse del formato exacto.

PAGINACIÓN (sitio público)
  paginaActual() lee ?pagina=N de la URL. renderPaginacion() imprime
  la barra de páginas con la maquetación real de la plantilla.
  Reportajes/Noticias: 9 por página. Podcasts/Videos: 8 por página.
  El Inicio (index.php) NO pagina a propósito.

TAMAÑO UNIFORME DE TARJETAS Y EFECTO DE IMAGEN
  Todo eso vive en public/assets/css/custom.css: las imágenes se
  recortan siempre al mismo alto (object-fit: cover), los títulos se
  cortan a 2 líneas, y hay un pequeño "mordisco" curvo en la esquina
  de la imagen como toque visual. Si se quiere cambiar el alto de
  las tarjetas, la sombra, o quitar el efecto de esquina, es ahí.


----------------------------------------------------------------
6. CÓMO AGREGAR UN MÓDULO NUEVO (ej. una tabla "eventos")
----------------------------------------------------------------

Este proyecto siempre repite el mismo patrón. Para agregar algo
nuevo, lo más rápido es copiar un módulo parecido y adaptarlo:

  1. Crear la tabla en MySQL (agregar su CREATE TABLE a un nuevo
     .sql, o directo en phpMyAdmin).
  2. En el panel: copiar los 3 archivos del módulo más parecido
     (por ejemplo, si el nuevo módulo NO lleva imagen, copiar
     podcasts.php / podcast_form.php / podcast_delete.php; si SÍ
     lleva imagen, copiar noticias.php / noticia_form.php /
     noticia_delete.php) y renombrarlos, cambiando el nombre de la
     tabla y las columnas en las consultas SQL.
  3. Agregar el nuevo módulo al menú lateral: abrir 2026.js, buscar
     el bloque que empieza con label:"Contenido" (o crear un grupo
     nuevo) y agregar un objeto {key:"...", text:"...", href:"....php",
     icon:'...'} siguiendo el mismo formato que los que ya existen.
  4. En el sitio público (si aplica): copiar noticias.php de
     public/ y adaptarlo igual, y agregarlo al menú de
     public/includes/header.php.
  5. Si el módulo tendrá muchos registros, copiar el patrón de
     paginación que ya usa public/reportajes.php (porPagina,
     paginaActual(), renderPaginacion()).

PENDIENTE CONOCIDO: la tabla `reportajes_fotos` (galería de fotos
extra por reportaje) todavía no tiene pantalla propia en el panel
para agregar/quitar fotos una por una — hoy solo se puede llenar a
mano desde phpMyAdmin. Sería un buen primer módulo para practicar
el patrón de arriba.


----------------------------------------------------------------
7. CREDENCIALES DE PRUEBA
----------------------------------------------------------------

  admin@revista.pe     / admin123     (rol: admin — ve todo, incluido
                                        el módulo Usuarios)
  editor@revista.pe    / editor123    (rol: editor)
  redactor@revista.pe  / redactor123  (rol: redactor)


----------------------------------------------------------------
8. INSTALAR EL PROYECTO DESDE CERO (XAMPP)
----------------------------------------------------------------

  1. Copiar la carpeta revista-admin-panel dentro de htdocs.
  2. Iniciar Apache y MySQL en el Panel de Control de XAMPP.
  3. Abrir phpMyAdmin, crear/entrar a la base y en la pestaña
     "Importar" subir sql/revista_digital.sql (y opcionalmente
     sql/datos_adicionales.sql).
  4. Si el usuario/contraseña de MySQL no son los de XAMPP por
     defecto (root sin contraseña), editarlos en config/db.php.
  5. Abrir http://localhost/revista-admin-panel/login.php

Para subirlo a un hosting real (ej. InfinityFree), la única
diferencia es que config/db.php se edita con el host, base, usuario
y contraseña que da el hosting (no localhost).


----------------------------------------------------------------
9. COSAS A LAS QUE HAY QUE TENER CUIDADO
----------------------------------------------------------------

  - config/db.php NUNCA debe subirse a un repositorio público con
    contraseñas reales adentro.
  - Cualquier consulta nueva a la base debe usar sentencias
    preparadas ($pdo->prepare() + execute([...])), tal como está en
    todo el proyecto. Nunca concatenar directamente lo que escribe
    el usuario dentro del SQL (evita inyección SQL).
  - Si se agrega un campo de texto largo que la gente pueda escribir
    con HTML (como se hizo con "Desarrollo"), hay que pasarlo por
    sanitizarHtmlBasico() antes de guardarlo — si no, es una puerta
    abierta a que alguien inyecte JavaScript malicioso (XSS).
  - Los archivos subidos (uploads/) tienen que mantener el
    .htaccess que impide ejecutar PHP ahí adentro. No borrarlo.
================================================================](https://github.com/Arkevael/DyD)
