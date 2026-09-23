<!-- footer block -->
<section class="w3l-footer-29-main py-5" id="footer">
  <div class="footer-29 py-md-3">
    <div class="container">
      <div class="row footer-top-29">
        <div class="col-lg-6 col-md-6 footer-list-29 footer-1">
          <h6 class="footer-title-29">Quiénes Somos</h6>
          <p>Revista Digital NTEP es un espacio de periodismo universitario de la Escuela Profesional de Ingeniería de Sistemas de la UAC, que documenta reportajes, noticias y boletines de interés regional para Cusco, Perú.</p>
          <div class="main-social-footer-29">
            <a target="_blank" href="#" class="facebook"><span class="fa fa-facebook-square"></span></a>
            <a target="_blank" href="#" class="twitter"><img src="assets/images/tiktokp.png" alt="TikTok"></a>
            <a target="_blank" href="#" class="instagram"><span class="fa fa-instagram"></span></a>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 footer-list-29 footer-2 mt-md-0 mt-5">
          <ul>
            <h6 class="footer-title-29">Contenido</h6>
            <li><a href="noticias.php">Noticias</a></li>
            <li><a href="videos.php">Videos</a></li>
            <li><a href="podcasts.php">Podcast</a></li>
            <li><a href="boletines.php">Boletín NTEP</a></li>
          </ul>
        </div>
        <div class="col-lg-3 col-md-6 mt-lg-0 mt-5 footer-list-29 footer-3">
          <div class="properties">
            <h6 class="footer-title-29">Contacto</h6>
            <ul>
              <li><a href="mailto:info@revistantep.pe">info@revistantep.pe</a></li>
              <li><a href="../login.php">Ingresar al panel</a></li>
            </ul>
          </div>
        </div>
      </div>
      <div class="bottom-copies text-center">
        <p class="copy-footer-29">© <?= date('Y') ?> Revista Digital NTEP — Universidad Andina del Cusco. Proyecto de tesis, Ingeniería de Sistemas.</p>
      </div>
    </div>
  </div>
  <!-- move top -->
  <button onclick="topFunction()" id="movetop" title="Go to top">
    <span class="fa fa-angle-up"></span>
  </button>
  <script>
    window.onscroll = function () { scrollFunction() };
    function scrollFunction() {
      if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        document.getElementById("movetop").style.display = "block";
      } else {
        document.getElementById("movetop").style.display = "none";
      }
    }
    function topFunction() {
      document.body.scrollTop = 0;
      document.documentElement.scrollTop = 0;
    }
  </script>
</section>
<!-- //footer block -->

<script src="assets/js/jquery-3.3.1.min.js"></script>
<script src="assets/js/theme-change.js"></script>
<script src="assets/js/easyResponsiveTabs.js"></script>
<script src="assets/js/owl.carousel.js"></script>
<script src="assets/js/jquery.magnific-popup.min.js"></script>
<script>
  $(function () { $('.navbar-toggler').click(function () { $('body').toggleClass('noscroll'); }); });
  $(window).on("scroll", function () {
    var scroll = $(window).scrollTop();
    if (scroll >= 80) { $("#site-header").addClass("nav-fixed"); } else { $("#site-header").removeClass("nav-fixed"); }
  });
  $(".navbar-toggler").on("click", function () { $("header").toggleClass("active"); });
  $(document).on("ready", function () {
    if ($(window).width() > 991) { $("header").removeClass("active"); }
    $(window).on("resize", function () { if ($(window).width() > 991) { $("header").removeClass("active"); } });
  });
</script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
