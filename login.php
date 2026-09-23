<?php
session_start();
require __DIR__ . '/config/db.php';

// Si ya hay sesión activa, ir directo al panel
if (!empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Ingresa tu correo y tu contraseña.';
    } else {
        $stmt = $pdo->prepare('SELECT id, nombres, ap_paterno, email, password_hash, rol FROM usuarios WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($password, $usuario['password_hash'])) {
            $_SESSION['usuario_id']      = $usuario['id'];
            $_SESSION['usuario_nombres'] = $usuario['nombres'] . ' ' . $usuario['ap_paterno'];
            $_SESSION['usuario_rol']     = $usuario['rol'];
            $_SESSION['usuario_email']   = $usuario['email'];
            header('Location: index.php');
            exit;
        }

        $error = 'Correo o contraseña incorrectos.';
    }
}
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Iniciar sesión · Revista Digital NTEP</title><script>!function(){try{var t=localStorage.getItem("dash26-theme"),e=window.matchMedia("(prefers-color-scheme: dark)").matches;document.documentElement.setAttribute("data-theme",t||(e?"dark":"light"))}catch(t){document.documentElement.setAttribute("data-theme","light")}}()</script><link href="style.css" rel="stylesheet"></head><body>
<div class="auth-shell">
  <aside class="auth-aside">
    <div class="auth-brand">
      <div class="logo"><svg viewBox="0 0 36 36" xmlns="http://www.w3.org/2000/svg"><path fill="#fff" d="M14.747 9.125c.527-1.426 1.736-2.573 3.317-2.573c1.643 0 2.792 1.085 3.318 2.573l6.077 16.867c.186.496.248.931.248 1.147c0 1.209-.992 2.046-2.139 2.046c-1.303 0-1.954-.682-2.264-1.611l-.931-2.915h-8.62l-.93 2.884c-.31.961-.961 1.642-2.232 1.642c-1.24 0-2.294-.93-2.294-2.17c0-.496.155-.868.217-1.023l6.233-16.867zm.34 11.256h5.891l-2.883-8.992h-.062l-2.946 8.992z"/></svg></div>
      <div class="name">Revista Digital NTEP</div>
    </div>
    <div class="auth-aside-body">
      <span class="auth-aside-eyebrow">Panel administrativo</span>
      <h1>Gestiona reportajes, noticias y boletines desde un solo lugar.</h1>
      <p>Este panel se conecta directamente a la base de datos <code>revista_digital</code> del proyecto de tesis.</p>
    </div>
    <div class="auth-aside-footer"><span>Cusco, Perú</span></div>
  </aside>
  <main class="auth-main">
    <div class="auth-card">
      <h2>Bienvenido de nuevo</h2>
      <p class="sub">Ingresa con tu cuenta para administrar el contenido.</p>
      <?php if ($error): ?>
        <div style="background:#fdecea;color:#b3261e;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:13.5px;"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <form class="auth-form" method="post" action="login.php">
        <div class="field">
          <label class="field-label" for="email">Correo</label>
          <div class="input-icon">
            <span class="ico"><svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg></span>
            <input id="email" name="email" class="input" type="email" placeholder="admin@revista.pe" autocomplete="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
          </div>
        </div>
        <div class="field">
          <div class="field-row"><label class="field-label" for="password">Contraseña</label></div>
          <div class="input-icon">
            <span class="ico"><svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
            <input id="password" name="password" class="input" type="password" placeholder="••••••••" autocomplete="current-password" required>
          </div>
        </div>
        <button class="btn btn--primary auth-submit" type="submit">Ingresar <svg viewBox="0 0 24 24"><path d="M5 12h14M13 5l7 7-7 7"/></svg></button>
      </form>
      <div class="auth-divider">cuentas de prueba</div>
      <div style="font-size:12.5px;color:var(--t-muted);line-height:1.7;text-align:center;">
        admin@revista.pe / admin123 (admin)<br>
        editor@revista.pe / editor123 (editor)<br>
        redactor@revista.pe / redactor123 (redactor)
      </div>
    </div>
    <div class="auth-main-bottom">Proyecto de tesis — Revista Digital NTEP · UAC</div>
  </main>
</div>
</body></html>
