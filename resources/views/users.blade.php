<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola User — Respon Blora</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<style>
  .user-stats {
    display:grid; grid-template-columns:repeat(4,1fr);
    gap:14px; margin-bottom:22px;
  }
  .u-stat-card {
    background:white; border:1px solid var(--gray-100);
    border-radius:var(--radius-sm); padding:16px;
    display:flex; align-items:center; gap:12px;
    box-shadow:var(--shadow-xs); transition:all var(--transition);
  }
  .u-stat-card:hover { box-shadow:var(--shadow); transform:translateY(-1px); }
  .u-stat-icon { width:42px; height:42px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0; }
  .u-stat-num { font-size:24px; font-weight:800; color:var(--navy); line-height:1; }
  .u-stat-lbl { font-size:11.5px; color:var(--gray-500); margin-top:2px; }

  .role-badge { display:inline-flex; align-items:center; gap:5px; font-size:11px; font-weight:700; padding:3px 10px; border-radius:100px; }
  .role-admin       { background:rgba(37,99,235,0.1);  color:#1D4ED8; }
  .role-koordinator { background:rgba(13,148,136,0.1); color:#0F766E; }
  .role-petugas     { background:rgba(16,185,129,0.1); color:#15803D; }
  .role-pimpinan    { background:rgba(124,58,237,0.1); color:#6D28D9; }

  .toolbar { display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; flex-wrap:wrap; gap:10px; }
  .toolbar-left { display:flex; gap:8px; flex-wrap:wrap; }

  .table-wrap { overflow-x:auto; -webkit-overflow-scrolling:touch; }
  .data-table { min-width:800px; }

  /* Pagination */
  .pagination-wrap {
    display:flex; align-items:center; justify-content:space-between;
    padding:14px 0 4px; flex-wrap:wrap; gap:10px;
  }
  .pagination-info { font-size:12.5px; color:var(--gray-500); }
  .pagination-btns { display:flex; gap:4px; flex-wrap:wrap; }
  .pg-btn {
    min-width:34px; height:34px; padding:0 10px;
    border:1px solid var(--gray-200); border-radius:7px;
    background:white; font-size:13px; font-weight:600;
    color:var(--gray-600); cursor:pointer; font-family:inherit;
    transition:all var(--transition);
    display:flex; align-items:center; justify-content:center;
  }
  .pg-btn:hover:not(:disabled) { border-color:var(--blue); color:var(--blue); background:rgba(37,99,235,0.05); }
  .pg-btn.active { background:var(--navy); color:white; border-color:var(--navy); }
  .pg-btn:disabled { opacity:0.35; cursor:not-allowed; }

  @media (max-width:600px) {
    .user-stats { grid-template-columns:repeat(2,1fr); }
    .toolbar { flex-direction:column; align-items:stretch; }
    .toolbar-left { flex-direction:column; }
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
          <h1>Kelola User</h1>
          <p>Manajemen akun petugas, koordinator, admin, dan pimpinan</p>
        </div>
        <div class="page-header-right">
          <button class="btn btn-primary" onclick="openAddUser()">➕ Tambah User Baru</button>
        </div>
      </div>

      <!-- Stat Cards -->
      <div class="user-stats anim-1">
        <div class="u-stat-card">
          <div class="u-stat-icon" style="background:rgba(37,99,235,0.08)">👥</div>
          <div><div class="u-stat-num" id="s-total">—</div><div class="u-stat-lbl">Total User</div></div>
        </div>
        <div class="u-stat-card">
          <div class="u-stat-icon" style="background:rgba(16,185,129,0.08)">✅</div>
          <div><div class="u-stat-num" id="s-aktif">—</div><div class="u-stat-lbl">User Aktif</div></div>
        </div>
        <div class="u-stat-card">
          <div class="u-stat-icon" style="background:rgba(13,148,136,0.08)">📝</div>
          <div><div class="u-stat-num" id="s-petugas">—</div><div class="u-stat-lbl">Petugas Lapangan</div></div>
        </div>
        <div class="u-stat-card">
          <div class="u-stat-icon" style="background:rgba(124,58,237,0.08)">🎖️</div>
          <div><div class="u-stat-num" id="s-staf">—</div><div class="u-stat-lbl">Admin/Koordinator</div></div>
        </div>
      </div>

      <!-- Toolbar -->
      <div class="toolbar anim-2">
        <div class="toolbar-left">
          <select class="filter-select" id="uf-role" onchange="loadUsers()">
            <option value="">Semua Role</option>
            <option value="petugas">Petugas</option>
            <option value="koordinator">Koordinator</option>
            <option value="admin">Admin</option>
            <option value="pimpinan">Pimpinan</option>
          </select>
          <select class="filter-select" id="uf-aktif" onchange="loadUsers()">
            <option value="">Semua Status</option>
            <option value="1">Aktif</option>
            <option value="0">Nonaktif</option>
          </select>
          <select class="filter-select" id="u-perpage" onchange="renderUsersPage(1)" style="width:auto">
            <option value="5">5 per halaman</option>
            <option value="10" selected>10 per halaman</option>
            <option value="25">25 per halaman</option>
          </select>
          <button class="btn btn-outline btn-sm" onclick="loadUsers()">🔄 Refresh</button>
        </div>
        <div style="font-size:13px;color:var(--gray-400)" id="user-count-label"></div>
      </div>

      <!-- Tabel -->
      <div class="anim-3">
        <div style="overflow-x:auto;-webkit-overflow-scrolling:touch;background:white;border-radius:var(--radius-sm);">
        <table class="data-table">
          <thead>
            <tr>
              <th>Nama</th><th>Username</th><th>NIP</th><th>Role</th>
              <th>Wilayah Tugas</th><th>Status</th><th>Login Terakhir</th><th>Aksi</th>
            </tr>
          </thead>
          <tbody id="users-tbody">
            <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--gray-400)">Memuat data...</td></tr>
          </tbody>
        </table>
        </div>
        <div class="pagination-wrap" id="users-pagination"></div>
      </div>

    </main>
  </div>
</div>

<!-- Modal Tambah/Edit User -->
<div class="modal-overlay" id="modal-user" style="align-items:flex-start;padding-top:40px;overflow-y:auto">
  <div class="modal" style="max-width:540px;max-height:90vh;overflow-y:auto">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
      <div style="font-size:18px;font-weight:800;color:var(--gray-900)" id="modal-user-title">Tambah User Baru</div>
      <button style="background:none;border:none;font-size:20px;cursor:pointer;color:var(--gray-400)" onclick="Modal.hide('modal-user')">✕</button>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Nama Lengkap <span style="color:var(--red)">*</span></label>
        <input type="text" class="form-control" id="u-name" placeholder="Nama lengkap">
      </div>
      <div class="form-group">
        <label class="form-label">Username <span style="color:var(--red)">*</span></label>
        <input type="text" class="form-control" id="u-username" placeholder="username_unik" autocomplete="off">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Password <span style="color:var(--red)" id="pass-req">*</span></label>
        <input type="password" class="form-control" id="u-password" placeholder="Min. 8 karakter" autocomplete="new-password">
        <div class="form-hint" id="pass-hint"></div>
      </div>
      <div class="form-group">
        <label class="form-label">Konfirmasi Password <span style="color:var(--red)" id="passconf-req">*</span></label>
        <input type="password" class="form-control" id="u-password-confirm" placeholder="Ulangi password" autocomplete="new-password">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Role <span style="color:var(--red)">*</span></label>
        <select class="form-control" id="u-role">
          <option value="petugas">Petugas</option>
          <option value="koordinator">Koordinator</option>
          <option value="admin">Admin</option>
          <option value="pimpinan">Pimpinan</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">NIP</label>
        <input type="text" class="form-control" id="u-nip" placeholder="Nomor Induk Pegawai">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Email</label>
        <input type="email" class="form-control" id="u-email" placeholder="email@bps.go.id">
      </div>
      <div class="form-group">
        <label class="form-label">No. HP</label>
        <input type="text" class="form-control" id="u-phone" placeholder="08xxxxxxxxxx">
      </div>
    </div>
    <div class="form-group">
      <label class="form-label">Wilayah Tugas</label>
      <input type="text" class="form-control" id="u-wilayah" placeholder="Contoh: Kecamatan Cepu">
    </div>
    <input type="hidden" id="u-id">
    <div style="display:flex;gap:10px;margin-top:16px">
      <button class="btn btn-outline" style="flex:1" onclick="Modal.hide('modal-user')">Batal</button>
      <button class="btn btn-primary" style="flex:1" id="btn-save-user" onclick="saveUser()">💾 Simpan User</button>
    </div>
  </div>
</div>

<!-- Modal Konfirmasi -->
<div class="modal-overlay" id="modal-confirm">
  <div class="modal" style="max-width:400px">
    <div class="modal-icon warning" id="confirm-icon">⚠️</div>
    <div class="modal-title" id="confirm-title">Konfirmasi</div>
    <div class="modal-desc" id="confirm-desc">Yakin melanjutkan?</div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="Modal.hide('modal-confirm')">Batal</button>
      <button class="btn btn-primary" id="btn-confirm-ok">Konfirmasi</button>
    </div>
  </div>
</div>

<script type="module">
  const user = Auth.check();
  let allUsers = [];

  if (user) {
    if (user.role !== 'admin') {
      window.location.href = '/dashboard';
    }
    Sidebar.render('/users');
    buildTopbar('Kelola User', 'Manajemen → Kelola User');
    loadNotifikasi();
    Modal.init('modal-user');
    Modal.init('modal-confirm');
    loadUsers();
  }

  async function loadUsers() {
    const role  = document.getElementById('uf-role').value;
    const aktif = document.getElementById('uf-aktif').value;
    let qs = '?per_page=999';
    if (role)         qs += `&role=${role}`;
    if (aktif !== '') qs += `&is_active=${aktif}`;

    const res = await Api.get('/users' + qs);
    if (!res?.ok) return;

    const raw = res.data.data;
    allUsers = Array.isArray(raw) ? raw : (raw.data || []);

    // Stat cards
    document.getElementById('s-total').textContent   = allUsers.length;
    document.getElementById('s-aktif').textContent   = allUsers.filter(u => u.is_active).length;
    document.getElementById('s-petugas').textContent = allUsers.filter(u => u.role === 'petugas').length;
    document.getElementById('s-staf').textContent    = allUsers.filter(u => ['admin','koordinator'].includes(u.role)).length;

    renderUsersPage(1);
  }

  function renderUsersPage(page) {
    const perPage = parseInt(document.getElementById('u-perpage').value);
    const total   = allUsers.length;
    const pages   = Math.max(1, Math.ceil(total / perPage));
    page = Math.min(Math.max(1, page), pages);
    const slice = allUsers.slice((page-1)*perPage, page*perPage);

    document.getElementById('user-count-label').textContent = `Total ${total} user`;

    if (allUsers.length === 0) {
      document.getElementById('users-tbody').innerHTML =
        `<tr><td colspan="8" style="text-align:center;padding:40px;color:var(--gray-400)">Tidak ada user ditemukan.</td></tr>`;
      document.getElementById('users-pagination').innerHTML = '';
      return;
    }

    document.getElementById('users-tbody').innerHTML = slice.map(u => `
      <tr>
        <td>
          <div style="display:flex;align-items:center;gap:10px">
            <div style="width:34px;height:34px;border-radius:50%;background:${Fmt.avatarColor(u.role)};display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:white;flex-shrink:0">${Fmt.initials(u.name)}</div>
            <div>
              <div style="font-weight:600;color:var(--gray-900)">${u.name}</div>
              <div style="font-size:11px;color:var(--gray-400)">${u.email||'—'}</div>
            </div>
          </div>
        </td>
        <td><span style="font-family:monospace;font-size:12px;color:var(--blue)">${u.username}</span></td>
        <td style="font-size:12px;color:var(--gray-500)">${u.nip||'—'}</td>
        <td><span class="role-badge role-${u.role}">${Fmt.roleLabel(u.role)}</span></td>
        <td style="font-size:12px">${u.wilayah_tugas||'—'}</td>
        <td><span class="pill ${u.is_active?'pill-selesai':'pill-ditutup'}">${u.is_active?'Aktif':'Nonaktif'}</span></td>
        <td style="font-size:12px;color:var(--gray-400)">${Fmt.date(u.last_login_at)}</td>
        <td>
          <div style="display:flex;gap:4px">
            <button class="action-btn" title="Edit" onclick="openEditUser(${u.id})">✏️</button>
            <button class="action-btn ${u.is_active?'danger':'success'}" title="${u.is_active?'Nonaktifkan':'Aktifkan'}"
              onclick="confirmToggle(${u.id},'${u.name.replace(/'/g,"\\'")}',${u.is_active})">
              ${u.is_active?'🔕':'🔔'}
            </button>
          </div>
        </td>
      </tr>`).join('');

    renderPagination('users-pagination', page, pages, total, perPage, 'renderUsersPage');
  }

  function renderPagination(containerId, page, pages, total, perPage, fnName) {
    const start = total === 0 ? 0 : (page-1)*perPage + 1;
    const end   = Math.min(page*perPage, total);
    const nums  = [];
    for (let p = 1; p <= pages; p++) {
      if (p === 1 || p === pages || (p >= page-1 && p <= page+1)) nums.push(p);
      else if (nums[nums.length-1] !== '...') nums.push('...');
    }
    document.getElementById(containerId).innerHTML = `
      <div class="pagination-info"> Menampilkan ${start}–${end} dari ${total} user</div>
      <div class="pagination-btns">
        <button class="pg-btn" ${page<=1?'disabled':''} onclick="${fnName}(${page-1})">‹</button>
        ${nums.map(n => n==='...'
          ? `<button class="pg-btn" disabled>…</button>`
          : `<button class="pg-btn ${n===page?'active':''}" onclick="${fnName}(${n})">${n}</button>`
        ).join('')}
        <button class="pg-btn" ${page>=pages?'disabled':''} onclick="${fnName}(${page+1})">›</button>
      </div>`;
  }

  function openAddUser() {
    document.getElementById('modal-user-title').textContent = '➕ Tambah User Baru';
    document.getElementById('btn-save-user').textContent    = '💾 Simpan User';
    ['u-name','u-username','u-password','u-password-confirm','u-nip','u-email','u-phone','u-wilayah'].forEach(id => {
      document.getElementById(id).value = '';
    });
    document.getElementById('u-role').value = 'petugas';
    document.getElementById('u-id').value   = '';
    document.getElementById('pass-hint').textContent       = '';
    document.getElementById('pass-req').style.display      = '';
    document.getElementById('passconf-req').style.display  = '';
    Modal.show('modal-user');
  }

  async function openEditUser(id) {
    const res = await Api.get('/users/' + id);
    if (!res?.ok) return;
    const u = res.data.data;
    document.getElementById('modal-user-title').textContent = '✏️ Edit: ' + u.name;
    document.getElementById('btn-save-user').textContent    = '💾 Update User';
    document.getElementById('u-id').value       = u.id;
    document.getElementById('u-name').value     = u.name;
    document.getElementById('u-username').value = u.username;
    document.getElementById('u-role').value     = u.role;
    document.getElementById('u-nip').value      = u.nip||'';
    document.getElementById('u-email').value    = u.email||'';
    document.getElementById('u-phone').value    = u.phone||'';
    document.getElementById('u-wilayah').value  = u.wilayah_tugas||'';
    document.getElementById('u-password').value         = '';
    document.getElementById('u-password-confirm').value = '';
    document.getElementById('pass-hint').textContent    = 'Kosongkan jika tidak ingin ganti password';
    document.getElementById('pass-req').style.display      = 'none';
    document.getElementById('passconf-req').style.display  = 'none';
    Modal.show('modal-user');
  }

  async function saveUser() {
    const id       = document.getElementById('u-id').value;
    const isEdit   = !!id;
    const name     = document.getElementById('u-name').value.trim();
    const username = document.getElementById('u-username').value.trim();
    const password = document.getElementById('u-password').value;
    const confirm  = document.getElementById('u-password-confirm').value;
    const role     = document.getElementById('u-role').value;

    if (!name)     { Toast.show('Nama wajib diisi.','error'); return; }
    if (!username) { Toast.show('Username wajib diisi.','error'); return; }
    if (!isEdit && !password) { Toast.show('Password wajib diisi untuk user baru.','error'); return; }
    if (password && password.length < 8) { Toast.show('Password minimal 8 karakter.','error'); return; }
    if (password && password !== confirm) { Toast.show('Konfirmasi password tidak cocok.','error'); return; }

    const payload = {
      name, username, role,
      nip:           document.getElementById('u-nip').value.trim()     || null,
      email:         document.getElementById('u-email').value.trim()   || null,
      phone:         document.getElementById('u-phone').value.trim()   || null,
      wilayah_tugas: document.getElementById('u-wilayah').value.trim() || null,
    };
    if (!isEdit || password) {
      payload.password = password;
      payload.password_confirmation = confirm;
    }

    const res = isEdit ? await Api.put('/users/'+id, payload) : await Api.post('/users', payload);
    if (!res?.ok) { Toast.show(res?.data?.message||'Gagal menyimpan user.','error'); return; }
    Modal.hide('modal-user');
    Toast.show(isEdit ? 'User berhasil diupdate!' : 'User baru berhasil dibuat!', 'success');
    loadUsers();
  }

  function confirmToggle(id, name, isActive) {
    const action = isActive ? 'nonaktifkan' : 'aktifkan';
    document.getElementById('confirm-title').textContent  = `${isActive?'Nonaktifkan':'Aktifkan'} User`;
    document.getElementById('confirm-desc').textContent   = `Yakin ingin ${action} akun "${name}"?`;
    document.getElementById('btn-confirm-ok').textContent = isActive ? '🔕 Nonaktifkan' : '✅ Aktifkan';
    document.getElementById('btn-confirm-ok').onclick = async () => {
      const res = await Api.patch(`/users/${id}/toggle-active`);
      if (!res?.ok) { Toast.show('Gagal mengubah status user.','error'); return; }
      Modal.hide('modal-confirm');
      Toast.show(`User berhasil di${action}kan.`,'success');
      loadUsers();
    };
    Modal.show('modal-confirm');
  }

  window.loadUsers       = loadUsers;
  window.renderUsersPage = renderUsersPage;
  window.openAddUser     = openAddUser;
  window.openEditUser    = openEditUser;
  window.saveUser        = saveUser;
  window.confirmToggle   = confirmToggle;
</script>
</body>
</html>