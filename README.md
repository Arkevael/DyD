# Panel administrativo — Revista Digital NTEP

Este proyecto conecta la plantilla **Adminator** con la base de datos
`revista_digital` de tu documento de tesis (`dyd-2.docx`). Incluye login real
contra la base de datos y **CRUD completo y funcional para las 7 tablas**
del modelo: usuarios, autores, reportajes, noticias, boletines, podcasts y
videos — con subida real de imágenes y PDFs donde el modelo lo contempla.

## 1. Requisitos

- XAMPP (o WAMP/Laragon) con **PHP 8.x** y **MySQL/MariaDB**.
- Un navegador.

## 2. Instalación (XAMPP)

1. Copia toda la carpeta `revista-admin-panel` dentro de `htdocs`, por ejemplo:
   `C:\xampp\htdocs\revista-admin-panel`
2. Abre el **Panel de control de XAMPP** e inicia los módulos **Apache** y
   **MySQL**.
3. Abre **phpMyAdmin** (`http://localhost/phpmyadmin`).
4. Entra a la base `revista_digital` (o créala si es la primera vez) y en la
   pestaña **Importar** selecciona `sql/revista_digital.sql`. Esto crea las
   8 tablas y los datos de prueba.
5. Opcional: importa también `sql/datos_adicionales.sql` para tener más
   contenido de ejemplo en cada tabla.
6. Abre en el navegador: `http://localhost/revista-admin-panel/login.php`

Si tu MySQL tiene usuario/contraseña distintos al típico de XAMPP
(`root` sin contraseña), edítalos en `config/db.php`.

**Importante:** la carpeta `uploads/` debe tener permisos de escritura para
que Apache pueda guardar las imágenes y PDFs que subas desde el panel. En
XAMPP normalmente esto ya funciona sin configuración adicional.

## 3. Credenciales de prueba

| Rol       | Correo               | Contraseña   |
|-----------|-----------------------|--------------|
| Admin     | admin@revista.pe       | admin123     |
| Editor    | editor@revista.pe      | editor123    |
| Redactor  | redactor@revista.pe    | redactor123  |

Las contraseñas están guardadas con `password_hash()` (bcrypt).

## 4. Módulos disponibles (todos conectados a la BD real)

| Módulo | Archivos | Qué permite |
|---|---|---|
| **Reportajes** | `reportajes.php`, `reportaje_form.php`, `reportaje_delete.php` | Listar, crear, editar, eliminar. Sube foto principal y PDF adjunto. |
| **Autores** | `autores.php`, `autor_form.php`, `autor_delete.php` | Listar, crear, editar, eliminar (bloqueado si el autor tiene reportajes). |
| **Noticias** | `noticias.php`, `noticia_form.php`, `noticia_delete.php` | Listar, crear, editar, eliminar. Sube foto. |
| **Boletines** | `boletines.php`, `boletin_form.php`, `boletin_delete.php` | Listar, crear, editar, eliminar. Sube portada (imagen) y PDF obligatorio. |
| **Podcasts** | `podcasts.php`, `podcast_form.php`, `podcast_delete.php` | Listar, crear, editar, eliminar. Enlace embebido (Spotify, etc.). |
| **Videos** | `videos.php`, `video_form.php`, `video_delete.php` | Listar, crear, editar, eliminar. Enlace embebido (YouTube, etc.). |
| **Usuarios** | `usuarios.php`, `usuario_form.php`, `usuario_delete.php` | Solo visible para el rol **admin**. Crear/editar/eliminar cuentas, asignar rol, resetear contraseña. No puedes eliminar tu propia cuenta ni un usuario con contenido publicado. |

Todos aparecen en el menú lateral, agrupados en **Contenido** y
**Administración**.

### Subida de imágenes y PDFs

- Los archivos se guardan físicamente en `uploads/reportajes/`,
  `uploads/noticias/`, `uploads/boletines/` y `uploads/pdfs/`.
- Al editar un registro y subir un archivo nuevo, el anterior se borra
  automáticamente del servidor.
- Formatos permitidos: imágenes `jpg, jpeg, png, webp` (máx. 8 MB), PDF
  `pdf`.
- La carpeta `uploads/` tiene un `.htaccess` que impide ejecutar PHP dentro
  de ella, por seguridad.

### Permisos por rol

- **admin**: acceso total, incluida la gestión de usuarios.
- **editor** y **redactor**: pueden gestionar reportajes, noticias,
  boletines, podcasts, videos y autores, pero no ven el módulo de Usuarios
  (si intentan entrar por URL directa, el sistema lo bloquea).

## 5. Estructura del proyecto

```
revista-admin-panel/
├── config/db.php              # Conexión PDO a MySQL
├── includes/
│   ├── auth.php                # Protección de sesión
│   └── upload.php              # Subida y borrado seguro de archivos
├── login.php / logout.php
├── index.php                   # Dashboard (datos reales en vivo)
├── reportajes.php / reportaje_form.php / reportaje_delete.php
├── autores.php / autor_form.php / autor_delete.php
├── noticias.php / noticia_form.php / noticia_delete.php
├── boletines.php / boletin_form.php / boletin_delete.php
├── podcasts.php / podcast_form.php / podcast_delete.php
├── videos.php / video_form.php / video_delete.php
├── usuarios.php / usuario_form.php / usuario_delete.php   (solo admin)
├── uploads/                    # Imágenes y PDFs subidos desde el panel
├── public/                     # ← SITIO PÚBLICO (sin login), ver sección 6
├── sql/
│   ├── revista_digital.sql      # Esquema completo + datos de prueba
│   └── datos_adicionales.sql    # Más datos de ejemplo (opcional)
└── (resto de archivos de la plantilla Adminator original)
```

## 6. Sitio público (`public/`)

Además del panel administrativo, el proyecto incluye un **sitio público sin
login**, inspirado en el diseño de un portal de noticias real, que muestra
en vivo el contenido que se publica desde el panel.

- **URL:** `http://localhost/revista-admin-panel/public/index.php`
- No requiere iniciar sesión — cualquier visitante puede verlo.
- Todo el contenido (reportajes, noticias, boletines, podcasts, videos) se
  lee directamente de la base de datos `revista_digital` con consultas de
  solo lectura.

| Página | Qué muestra |
|---|---|
| `public/index.php` | Inicio: reportaje destacado, últimos reportajes, últimas noticias y accesos a boletín/podcast/video más recientes. |
| `public/reportajes.php` | Listado de todos los reportajes. Admite `?mes=2026-08` para filtrar por el archivo mensual. |
| `public/reportaje.php?id=N` | Detalle de un reportaje: imagen, cita destacada (resumen corto), cuerpo del texto, galería (`reportajes_fotos`) y barra lateral con "Últimos reportajes" y "Archivo". |
| `public/noticias.php` | Listado de noticias con enlace externo. |
| `public/boletines.php` | Listado de boletines con descarga de PDF. |
| `public/podcasts.php` / `public/videos.php` | Listado con reproductor embebido (`iframe` del `url_embed`). |
| `public/contacto.php` | Página "Sobre NTEP" con datos de contacto y enlace de acceso al panel. |

Desde el panel admin, el botón **"Ver sitio público"** en el dashboard
(`index.php`) abre `public/index.php` en una pestaña nueva.

**Nota sobre el diseño:** esta versión usa los archivos reales de la
plantilla (logo, `style-starter.css`, tipografía Cabin, iconos Font
Awesome, jQuery/Bootstrap/Owl Carousel) que veníamos de tu copia del sitio,
ubicados en `public/assets/`. La estructura HTML (navbar, banda de título,
tarjetas `grids5-info`, artículo con cita destacada, sidebar "Últimos
reportajes"/"Archivos", footer) es la misma del sitio original — solo el
contenido (textos, imágenes, fechas) ahora sale de tu base de datos en vez
de estar escrito a mano. No se copiaron las fotos de los artículos reales
del sitio original (pesaban ~30 MB y no aplican a tu contenido); las
imágenes de tus reportajes/noticias/boletines son las que subas tú desde
el panel admin.
