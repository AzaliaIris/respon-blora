<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profil Saya — Respon Blora</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<style>
  .profil-layout {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 22px;
    align-items: start;
  }

  /* Avatar Card */
  .avatar-card {
    background: white;
    border: 1px solid var(--gray-100);
    border-radius: var(--radius);
    padding: 32px 24px;
    text-align: center;
    box-shadow: var(--shadow-sm);
    position: sticky;
    top: 24px;
  }

  .avatar-big {
    width: 96px; height: 96px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 36px; font-weight: 800; color: white;
    margin: 0 auto 16px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
  }

  .avatar-name {
    font-size: 18px; font-weight: 800;
    color: var(--gray-900); margin-bottom: 4px;
  }

  .avatar-username {
    font-size: 13px; color: var(--gray-400);
    font-family: monospace; margin-bottom: 12px;
  }

  .avatar-divider {
    border: none; border-top: 1px solid var(--gray-100);
    margin: 16px 0;
  }

  .avatar-info-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 8px 0;
    font-size: 13px;
  }
  .avatar-info-label { color: var(--gray-400); font-weight: 500; }
  .avatar-info-val   { color: var(--gray-700); font-weight: 600; text-align: right; max-width: 160px; word-break: break-word; }

  /* Info Card */
  .info-card {
    background: white;
    border: 1px solid var(--gray-100);
    border-radius: var(--radius);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    margin-bottom: 18px;
  }

  .info-card-header {
    padding: 18px 24px;
    border-bottom: 1px solid var(--gray-100);
    display: flex; align-items: center; justify-content: space-between;
  }

  .info-card-title {
    font-size: 15px; font-weight: 700; color: var(--gray-900);
    display: flex; align-items: center; gap: 8px;
  }

  .info-card-body { padding: 24px; }

  .info-row {
    display: grid;
    grid-template-columns: 160px 1fr;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid var(--gray-100);
    align-items: center;
  }
  .info-row:last-child { border-bottom: none; padding-bottom: 0; }

  .info-label {
    font-size: 12.5px; font-weight: 600;
    color: var(--gray-500); text-transform: uppercase; letter-spacing: 0.04em;
  }

  .info-val {
    font-size: 14px; font-weight: 500; color: var(--gray-800);
  }

  .info-val.mono { font-family: monospace; color: var(--blue); }
  .info-val.empty { color: var(--gray-300); font-style: italic; }

  /* Edit section */
  .edit-hint {
    font-size: 12px; color: var(--gray-400);
    background: rgba(37,99,235,0.05);
    border: 1px solid rgba(37,99,235,0.1);
    border-radius: 8px;
    padding: 10px 14px;
    margin-bottom: 20px;
    display: flex; align-items: center; gap: 8px;
  }

  .field-editable .form-control {
    background: white;
  }

  .field-readonly .form-control {
    background: var(--gray-100);
    color: var(--gray-500);
    cursor: not-allowed;
  }

  /* Password section */
  .pass-toggle {
    font-size: 12.5px; color: var(--blue);
    cursor: pointer; font-weight: 600;
    background: none; border: none; font-family: inherit;
    padding: 0;
  }
  .pass-toggle:hover { text-decoration: underline; }

  .pass-section { display: none; margin-top: 16px; }
  .pass-section.open { display: block; }

  @media (max-width: 900px) {
    .profil-layout { grid-template-columns: 1fr; }
    .avatar-card { position: static; }
    .info-row { grid-template-columns: 130px 1fr; }
  }

  @media (max-width: 480px) {
    .info-row { grid-template-columns: 1fr; gap: 4px; }
    .info-label { font-size: 11px; }
  }
</style>
</head>
<body>
<div class="app-layout">
  <aside class="sidebar"></aside>
  <div class="main-area">
    <header class="topbar"></header>
    <main class="content">

      <div class="page-header anim-1">
        <div class="page-header-left">
          <h1>Profil Saya</h1>
          <p>Informasi akun dan pengaturan kontak</p>
        </div>
      </div>

      <div class="profil-layout">

        <!-- Kolom kiri: Avatar + ringkasan -->
        <div>
          <div class="avatar-card anim-1">
            <div class="avatar-big" id="av-circle">—</div>
            <div class="avatar-name" id="av-name">—</div>
            <div class="avatar-username" id="av-username">—</div>

            <span class="role-pill" id="av-role-pill"></span>
            <span class="pill" id="av-status-pill" style="margin-left:4px"></span>

            <hr class="avatar-divider">

            <div class="avatar-info-row">
              <span class="avatar-info-label">NIP</span>
              <span class="avatar-info-val" id="av-nip">—</span>
            </div>
            <div class="avatar-info-row">
              <span class="avatar-info-label">Wilayah</span>
              <span class="avatar-info-val" id="av-wilayah">—</span>
            </div>
            <div class="avatar-info-row">
              <span class="avatar-info-label">Login Terakhir</span>
              <span class="avatar-info-val" id="av-login">—</span>
            </div>
          </div>
        </div>

        <!-- Kolom kanan -->
        <div>

          <!-- Informasi Akun (read-only) -->
          <div class="info-card anim-2">
            <div class="info-card-header">
              <div class="info-card-title">📄 Informasi Akun</div>
              <span style="font-size:11.5px;color:var(--gray-400)">Tidak dapat diubah</span>
            </div>
            <div class="info-card-body">
              <div class="info-row">
                <div class="info-label">Nama Lengkap</div>
                <div class="info-val" id="inf-name">—</div>
              </div>
              <div class="info-row">
                <div class="info-label">Username</div>
                <div class="info-val mono" id="inf-username">—</div>
              </div>
              <div class="info-row">
                <div class="info-label">Role</div>
                <div class="info-val" id="inf-role">—</div>
              </div>
              <div class="info-row">
                <div class="info-label">Wilayah Tugas</div>
                <div class="info-val" id="inf-wilayah">—</div>
              </div>
              <div class="info-row">
                <div class="info-label">Status Akun</div>
                <div class="info-val" id="inf-status">—</div>
              </div>
            </div>
          </div>

          <!-- Informasi Kontak (editable) -->
          <div class="info-card anim-3">
            <div class="info-card-header">
              <div class="info-card-title">📋 Informasi Kontak</div>
              <span style="font-size:11.5px;color:var(--blue);font-weight:600">Dapat diedit</span>
            </div>
            <div class="info-card-body">
              <div class="edit-hint">
                ℹ️ Anda dapat mengubah NIP, email, dan nomor HP. Perubahan disimpan langsung.
              </div>

              <div class="form-row">
                <div class="form-group field-editable">
                  <label class="form-label">NIP</label>
                  <input type="text" class="form-control" id="f-nip" placeholder="Nomor Induk Pegawai (opsional)">
                  <div class="form-hint">Kosongkan jika tidak memiliki NIP</div>
                </div>
                <div class="form-group field-editable">
                  <label class="form-label">Email</label>
                  <input type="email" class="form-control" id="f-email" placeholder="email@bps.go.id">
                </div>
              </div>

              <div class="form-group field-editable" style="max-width:300px">
                <label class="form-label">Nomor HP</label>
                <input type="text" class="form-control" id="f-phone" placeholder="08xxxxxxxxxx">
              </div>

              <div style="display:flex;gap:10px;margin-top:8px">
                <button class="btn btn-outline btn-sm" onclick="resetForm()">↩ Reset</button>
                <button class="btn btn-primary" onclick="saveProfile()" id="btn-save">
                  💾 Simpan Perubahan
                </button>
              </div>
            </div>
          </div>

          <!-- Ganti Password -->
          <div class="info-card anim-4">
            <div class="info-card-header">
              <div class="info-card-title">🔒 Keamanan</div>
              <button class="pass-toggle" onclick="togglePassSection()">Ganti Password</button>
            </div>
            <div class="info-card-body">
              <div id="pass-closed" style="font-size:13px;color:var(--gray-400)">
                Klik "Ganti Password" untuk mengubah kata sandi akun Anda.
              </div>
              <div class="pass-section" id="pass-section">
                <div class="form-group" style="max-width:340px">
                  <label class="form-label">Password Baru</label>
                  <input type="password" class="form-control" id="f-newpass" placeholder="Min. 8 karakter" autocomplete="new-password">
                </div>
                <div class="form-group" style="max-width:340px">
                  <label class="form-label">Konfirmasi Password Baru</label>
                  <input type="password" class="form-control" id="f-newpass-confirm" placeholder="Ulangi password baru" autocomplete="new-password">
                </div>
                <div style="display:flex;gap:10px;margin-top:4px">
                  <button class="btn btn-outline btn-sm" onclick="togglePassSection()">Batal</button>
                  <button class="btn btn-primary" onclick="savePassword()">🔑 Update Password</button>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

    </main>
  </div>
</div>

<script type="module">
  const user = Auth.check();
  let profileData = null;

  if (user) {
    Sidebar.render('/profil');
    buildTopbar('Profil Saya', 'Profil');
    loadProfile();
  }

  async function loadProfile() {
    const res = await Api.get('/auth/me');
    if (!res?.ok) return;
    profileData = res.data.data || res.data;
    renderProfile(profileData);
  }

  function renderProfile(p) {
    const roleLabel = Fmt.roleLabel(p.role);
    const color     = Fmt.avatarColor(p.role);
    const initials  = Fmt.initials(p.name);

    // Avatar card
    document.getElementById('av-circle').style.background = color;
    document.getElementById('av-circle').textContent      = initials;
    document.getElementById('av-name').textContent        = p.name;
    document.getElementById('av-username').textContent    = '@' + p.username;
    document.getElementById('av-nip').textContent         = p.nip || '—';
    document.getElementById('av-wilayah').textContent     = p.wilayah_tugas || '—';
    document.getElementById('av-login').textContent       = Fmt.date(p.last_login_at);

    document.getElementById('av-role-pill').innerHTML =
      `<span class="pill" style="background:${color}22;color:${color};font-weight:700">${roleLabel}</span>`;
    document.getElementById('av-status-pill').className =
      'pill ' + (p.is_active ? 'pill-selesai' : 'pill-ditutup');
    document.getElementById('av-status-pill').textContent = p.is_active ? 'Aktif' : 'Nonaktif';

    // Info akun (read-only)
    document.getElementById('inf-name').textContent     = p.name;
    document.getElementById('inf-username').textContent = p.username;
    document.getElementById('inf-role').innerHTML       = `<span class="pill" style="background:${color}22;color:${color};font-weight:700">${roleLabel}</span>`;
    document.getElementById('inf-wilayah').textContent  = p.wilayah_tugas || '—';
    document.getElementById('inf-status').innerHTML     =
      `<span class="pill ${p.is_active?'pill-selesai':'pill-ditutup'}">${p.is_active?'Aktif':'Nonaktif'}</span>`;

    // Form kontak
    document.getElementById('f-nip').value   = p.nip   || '';
    document.getElementById('f-email').value = p.email || '';
    document.getElementById('f-phone').value = p.phone || '';
  }

  function resetForm() {
    if (profileData) {
      document.getElementById('f-nip').value   = profileData.nip   || '';
      document.getElementById('f-email').value = profileData.email || '';
      document.getElementById('f-phone').value = profileData.phone || '';
    }
    Toast.show('Form direset.', 'info');
  }

  async function saveProfile() {
    const nip   = document.getElementById('f-nip').value.trim()   || null;
    const email = document.getElementById('f-email').value.trim() || null;
    const phone = document.getElementById('f-phone').value.trim() || null;

    if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      Toast.show('Format email tidak valid.', 'error'); return;
    }

    const btn = document.getElementById('btn-save');
    btn.disabled = true;
    btn.textContent = '⏳ Menyimpan...';

    const res = await Api.put('/profile', { nip, email, phone });

    btn.disabled = false;
    btn.textContent = '💾 Simpan Perubahan';

    if (!res?.ok) {
      Toast.show(res?.data?.message || 'Gagal menyimpan.', 'error'); return;
    }

    profileData = res.data.data || profileData;
    // Update avatar card side
    document.getElementById('av-nip').textContent = profileData.nip || '—';
    // Update localStorage user
    const stored = Auth.getUser();
    if (stored) {
      stored.nip   = profileData.nip;
      stored.email = profileData.email;
      stored.phone = profileData.phone;
      localStorage.setItem('rb_user', JSON.stringify(stored));
    }
    Toast.show('Profil berhasil diperbarui!', 'success');
  }

  function togglePassSection() {
    const sec    = document.getElementById('pass-section');
    const closed = document.getElementById('pass-closed');
    const isOpen = sec.classList.contains('open');
    sec.classList.toggle('open');
    closed.style.display = isOpen ? '' : 'none';
    if (isOpen) {
      document.getElementById('f-newpass').value         = '';
      document.getElementById('f-newpass-confirm').value = '';
    }
  }

  async function savePassword() {
    const newpass = document.getElementById('f-newpass').value;
    const confirm = document.getElementById('f-newpass-confirm').value;

    if (!newpass)              { Toast.show('Password baru wajib diisi.', 'error'); return; }
    if (newpass.length < 8)    { Toast.show('Password minimal 8 karakter.', 'error'); return; }
    if (newpass !== confirm)   { Toast.show('Konfirmasi password tidak cocok.', 'error'); return; }

    const res = await Api.put('/profile', { password: newpass, password_confirmation: confirm });
    if (!res?.ok) { Toast.show(res?.data?.message || 'Gagal update password.', 'error'); return; }

    Toast.show('Password berhasil diubah!', 'success');
    togglePassSection();
  }

  window.resetForm       = resetForm;
  window.saveProfile     = saveProfile;
  window.togglePassSection = togglePassSection;
  window.savePassword    = savePassword;
</script>
</body>
</html>