<?php
require __DIR__ . '/includes/db_publico.php';

$tituloPagina = 'Sobre NTEP'; $activePage = 'contacto'; $bandaTitulo = 'Sobre NTEP';
$bandaMigas = [['texto' => 'Sobre NTEP']];
require __DIR__ . '/includes/header.php';
?>
<section class="w3l-blog py-5">
  <div class="container py-lg-3">
    <div class="row">
      <div class="col-lg-8">
        <h2 class="title-single mb-4">Quiénes somos</h2>
        <p align="justify" class="mb-4">
          Revista Digital NTEP es un proyecto de tesis de la Escuela Profesional de Ingeniería de
          Sistemas de la Universidad Andina del Cusco. Nace como un espacio de periodismo
          universitario que documenta, mediante reportajes, noticias, boletines, podcasts y
          videos, temas de interés regional y académico para la comunidad de Cusco, Perú.
        </p>
        <p align="justify" class="mb-4">
          Todo el contenido que ves en este sitio se gestiona desde un panel administrativo
          conectado en tiempo real a la base de datos <code>revista_digital</code>, construido
          como parte del desarrollo del sistema de tesis.
        </p>
      </div>
      <div class="col-lg-4 left-text-9 mt-lg-0 mt-5 pl-lg-4">
        <div class="left-top-9 mt-5 pt-sm-3">
          <h6 class="heading-small-text-9 mb-3">Contacto</h6>
          <p><span class="fa fa-envelope mr-2"></span> info@revistantep.pe</p>
          <p><span class="fa fa-map-marker mr-2"></span> Cusco, Perú</p>
          <a href="../login.php" class="btn mt-3 p-0">Ingresar al panel <span class="fa fa-arrow-right"></span></a>
        </div>
      </div>
    </div>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
