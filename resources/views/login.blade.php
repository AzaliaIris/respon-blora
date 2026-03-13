<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Masuk — Respon Blora</title>
<style>
  body { display: flex; min-height: 100vh; overflow: hidden; background: white; }

  /* Tambahkan di <style> login.blade.php */
  html, body {
    overflow: auto !important;
    height: auto !important;
  }

  .login-hero {
    flex: 1;
    background: linear-gradient(150deg, var(--navy) 0%, var(--navy-light) 55%, #1A4B9A 100%);
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 64px 60px;
    overflow: hidden;
  }

  .hero-pattern {
    position: absolute; inset: 0;
    background-image: radial-gradient(circle at 20% 80%, rgba(37,99,235,0.15) 0%, transparent 50%),
                      radial-gradient(circle at 80% 20%, rgba(13,148,136,0.1) 0%, transparent 50%);
    pointer-events: none;
  }

  .hero-dots {
    position: absolute; inset: 0;
    background-image: radial-gradient(rgba(255,255,255,0.04) 1px, transparent 1px);
    background-size: 28px 28px;
  }

  .hero-content { position: relative; z-index: 1; max-width: 440px; }

  .hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.14);
    border-radius: 100px;
    padding: 6px 14px;
    font-size: 11.5px;
    font-weight: 600;
    color: rgba(255,255,255,0.75);
    letter-spacing: 0.07em;
    text-transform: uppercase;
    margin-bottom: 36px;
  }

  .hero-badge-dot {
    width: 6px; height: 6px;
    background: var(--teal-light);
    border-radius: 50%;
    animation: pulse-dot 2s infinite;
  }

  .hero-title {
    font-size: 54px;
    font-weight: 800;
    color: white;
    line-height: 1.05;
    letter-spacing: -0.03em;
    margin-bottom: 18px;
  }

  .hero-title em { color: var(--teal-light); font-style: normal; }

  .hero-desc {
    font-size: 15.5px;
    color: rgba(255,255,255,0.58);
    line-height: 1.75;
    margin-bottom: 52px;
  }

  .hero-stats { display: flex; gap: 36px; }

  .hero-stat-num {
    font-size: 30px;
    font-weight: 800;
    color: white;
    display: block;
    line-height: 1;
    margin-bottom: 5px;
    letter-spacing: -0.02em;
  }

  .hero-stat-lbl { font-size: 12px; color: rgba(255,255,255,0.44); font-weight: 500; }

  /* ── Form Panel ── */
  .login-panel {
    width: 480px;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 52px 52px;
    background: white;
    overflow-y: auto;
  }

  .panel-logo {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 44px;
  }

  .panel-logo-icon {
    width: 44px; height: 44px;
    background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
    font-weight: 800;
  }

  .panel-logo-name { font-size: 14px; font-weight: 800; color: var(--navy); line-height: 1.3; }
  .panel-logo-sub  { font-size: 11px; color: var(--gray-400); font-weight: 500; }

  .panel-heading { font-size: 26px; font-weight: 800; color: var(--gray-900); margin-bottom: 6px; letter-spacing: -0.01em; }
  .panel-sub     { font-size: 14px; color: var(--gray-500); margin-bottom: 32px; }

  .input-icon-wrap { position: relative; }

  .input-icon-wrap .icon {
    position: absolute;
    left: 13px; top: 50%;
    transform: translateY(-50%);
    font-size: 15px;
    color: var(--gray-400);
    pointer-events: none;
    transition: color var(--transition);
  }

  .input-icon-wrap input {
    padding-left: 42px;
    height: 48px;
  }

  .input-icon-wrap input:focus + .icon { color: var(--blue); }

  .eye-toggle {
    position: absolute;
    right: 12px; top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    font-size: 16px;
    color: var(--gray-400);
    padding: 4px;
    transition: color var(--transition);
  }

  .eye-toggle:hover { color: var(--gray-600); }

  /* .demo-chips {
    background: var(--gray-50);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-sm);
    padding: 12px 14px;
    margin-bottom: 20px;
  }

  .demo-chips-title {
    font-size: 11.5px;
    font-weight: 700;
    color: var(--gray-500);
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.06em;
  }

  .demo-chips-row { display: flex; flex-wrap: wrap; gap: 6px; } */

  .chip {
    padding: 4px 12px;
    border-radius: 100px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition);
    border: none;
    color: white;
  }

  .chip:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }

  .btn-login {
    width: 100%;
    height: 50px;
    background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
    color: white;
    border: none;
    border-radius: var(--radius-sm);
    font-family: inherit;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    transition: all var(--transition);
    margin-top: 6px;
    letter-spacing: 0.01em;
  }

  .btn-login:hover   { transform: translateY(-1px); box-shadow: 0 8px 28px rgba(37,99,235,0.32); }
  .btn-login:active  { transform: translateY(0); }
  .btn-login.loading { opacity: 0.75; pointer-events: none; }

  .alert-box {
    display: none;
    align-items: center;
    gap: 10px;
    padding: 12px 14px;
    border-radius: var(--radius-sm);
    font-size: 13px;
    font-weight: 500;
    margin-bottom: 18px;
  }

  .alert-box.show  { display: flex; }
  .alert-error     { background: var(--red-pale); color: #B91C1C; border: 1px solid #FECACA; }
  .alert-success   { background: var(--green-pale); color: #15803D; border: 1px solid #BBF7D0; }

  .login-footer {
    margin-top: 32px;
    padding-top: 20px;
    border-top: 1px solid var(--gray-100);
    text-align: center;
    font-size: 12px;
    color: var(--gray-400);
    line-height: 1.7;
  }

  @media (max-width: 860px) {
    .login-hero { display: none; }
    .login-panel { width: 100%; padding: 40px 28px; }
  }
</style>
</head>
<body>

<!-- Hero -->
<div class="login-hero">
  <div class="hero-pattern"></div>
  <div class="hero-dots"></div>
  <div class="hero-content">
    <div class="hero-badge"><span class="hero-badge-dot"></span>BPS Kabupaten Blora — Aktif</div>
    <h1 class="hero-title">RESPON<br><em>BLORA</em></h1>
    <p class="hero-desc">Sistem pelaporan cepat bagi petugas sensus saat menghadapi penolakan responden dan kendala pendataan di Kabupaten Blora.</p>
    <div class="hero-stats">
      <div>
        <span class="hero-stat-num">21</span>
        <span class="hero-stat-lbl">Total Laporan</span>
      </div>
      <div>
        <span class="hero-stat-num">28.6%</span>
        <span class="hero-stat-lbl">Tingkat Selesai</span>
      </div>
      <div>
        <span class="hero-stat-num">4</span>
        <span class="hero-stat-lbl">Kecamatan Aktif</span>
      </div>
    </div>
  </div>
</div>

<!-- Form Panel -->
<div class="login-panel">
  <div class="panel-logo">
    <div class="panel-logo-icon">RB</div>
    <div>
      <div class="panel-logo-name">Respon Blora</div>
      <div class="panel-logo-sub">Sistem Pelaporan Kendala Pendataan</div>
    </div>
  </div>

  <h2 class="panel-heading">Masuk ke Sistem</h2>
  <p class="panel-sub">Gunakan kredensial yang diberikan oleh admin BPS Blora.</p>

  <div class="alert-box alert-error" id="alert-box">
    <span>⚠️</span><span id="alert-msg">Username atau password salah.</span>
  </div>

  <div class="form-group">
    <label class="form-label">Username</label>
    <div class="input-icon-wrap">
      <input type="text" class="form-control" id="inp-username" placeholder="Masukkan username Anda" autocomplete="username">
      <span class="icon">👤</span>
    </div>
  </div>

  <div class="form-group">
    <label class="form-label">Password</label>
    <div class="input-icon-wrap">
      <input type="password" class="form-control" id="inp-password" placeholder="••••••••" autocomplete="current-password">
      <span class="icon">🔐</span>
      <button type="button" class="eye-toggle" id="eye-toggle" onclick="toggleEye()">👁️</button>
    </div>
  </div>

  <!-- <div class="demo-chips">
    <div class="demo-chips-title">🧪 Demo — klik untuk isi otomatis</div>
    <div class="demo-chips-row">
      <button class="chip" style="background:#0B1F3A" onclick="demo('admin_blora')">admin_blora</button>
      <button class="chip" style="background:#5B21B6" onclick="demo('kepala_bps')">kepala_bps</button>
      <button class="chip" style="background:#0D9488" onclick="demo('koord_cepu')">koord_cepu</button>
      <button class="chip" style="background:#059669" onclick="demo('budi_ppl01')">budi_ppl01</button>
    </div>
  </div> -->

  <button class="btn-login" id="btn-login" onclick="doLogin()">
    <span id="btn-text">Masuk ke Sistem →</span>
  </button>

  <div class="login-footer">
    Respon Blora v1.0 &nbsp;·&nbsp; BPS Kabupaten Blora<br>
    Lupa password? Hubungi Admin Kabupaten
  </div>
</div>

@vite(['resources/css/app.css', 'resources/js/app.js'])
<script>
  // Redirect if already logged in
  if (Auth.getUser() && Auth.getToken()) {
    window.location.href = '/dashboard';
  }

  // Cek apakah ada pesan redirect
  const redirectMsg = sessionStorage.getItem('rb_redirect_msg');
  if (redirectMsg) {
    document.getElementById('alert-msg').textContent = redirectMsg;
    document.getElementById('alert-box').className = 'alert-box alert-error show';
    sessionStorage.removeItem('rb_redirect_msg');
  }

  // function demo(username) {
  //   document.getElementById('inp-username').value = username;
  //   document.getElementById('inp-password').value = 'Admin@1234';
  //   document.getElementById('alert-box').classList.remove('show');
  // }

  function toggleEye() {
    const inp = document.getElementById('inp-password');
    const btn = document.getElementById('eye-toggle');
    if (inp.type === 'password') { inp.type = 'text';     btn.textContent = '🙈'; }
    else                         { inp.type = 'password'; btn.textContent = '👁️'; }
  }

  async function doLogin() {
    const username = document.getElementById('inp-username').value.trim();
    const password = document.getElementById('inp-password').value;
    const alertBox = document.getElementById('alert-box');
    const alertMsg = document.getElementById('alert-msg');
    const btnText  = document.getElementById('btn-text');
    const btn      = document.getElementById('btn-login');

    alertBox.classList.remove('show');

    if (!username || !password) {
      alertMsg.textContent = 'Username dan password wajib diisi.';
      alertBox.className = 'alert-box alert-error show';
      return;
    }

    btnText.textContent = '⏳ Memverifikasi...';
    btn.classList.add('loading');

    let res;
    try {
      const raw = await fetch('/api/auth/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username, password })
      });
      const data = await raw.json();
      res = { ok: raw.ok, status: raw.status, data };
    } catch(e) {
      btnText.textContent = 'Masuk ke Sistem →';
      btn.classList.remove('loading');
      alertMsg.textContent = 'Koneksi ke server gagal.';
      alertBox.className = 'alert-box alert-error show';
      return;
    }

    btnText.textContent = 'Masuk ke Sistem →';
    btn.classList.remove('loading');

    if (res.ok && res.data.success) {
      Auth.save(res.data.data.user, res.data.data.token);
      btnText.textContent = '✅ Berhasil! Mengalihkan...';
      btn.style.background = 'linear-gradient(135deg, #059669 0%, #10B981 100%)';
      setTimeout(() => { window.location.href = '/dashboard'; }, 600);
    } else {
      alertMsg.textContent = res.data.message || 'Login gagal.';
      alertBox.className = 'alert-box alert-error show';
    }
  }

  document.getElementById('inp-password').addEventListener('keydown', e => {
    if (e.key === 'Enter') doLogin();
  });

  window.doLogin   = doLogin;
  window.demo      = demo;
  window.toggleEye = toggleEye;
</script>
</body>
</html>
