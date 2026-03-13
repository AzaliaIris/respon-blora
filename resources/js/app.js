import './bootstrap';
/* ══════════════════════════════════════════
   RESPON BLORA — Global JS Utilities
   Shared across all pages
══════════════════════════════════════════ */

// ── Config ──
const API_BASE = '/api';

// ── Auth Helpers ──
const Auth = {
  save(user, token) {
    localStorage.setItem('rb_user',  JSON.stringify(user));
    localStorage.setItem('rb_token', token);
  },
  getUser()  { try { return JSON.parse(localStorage.getItem('rb_user')); } catch { return null; } },
  getToken() { return localStorage.getItem('rb_token'); },
  clear()    { localStorage.removeItem('rb_user'); localStorage.removeItem('rb_token'); },
  check()    {
    const user = this.getUser();
    const token = this.getToken();
    if (!user || !token) {
      window.location.href = '/login';
      return null;
    }
    // Cek expiry dari JWT payload (tanpa server call)
    try {
      const payload = JSON.parse(atob(token.split('.')[1]));
      if (payload.exp && payload.exp * 1000 < Date.now()) {
        Auth.logout();
        window.location.href = '/login';
        return null;
      }
    } catch(e) {}

    return user;
  },
  can(roles) {
    const user = this.getUser();
    if (!user) return false;
    return roles.includes(user.role);
  }
};

// ── API Helper ──
const Api = {
  async request(method, endpoint, body = null, isFormData = false) {
    const headers = { 'Authorization': `Bearer ${Auth.getToken()}` };
    if (!isFormData) headers['Content-Type'] = 'application/json';

    const opts = { method, headers };
    if (body) opts.body = isFormData ? body : JSON.stringify(body);

    try {
      const res  = await fetch(API_BASE + endpoint, opts);
      const data = await res.json();

      if (res.status === 401) {
        Auth.clear();
        window.location.href = '/login';
        return null;
      }
      return { ok: res.ok, status: res.status, data };
    } catch (err) {
      console.error('API Error:', err);
      Toast.show('Koneksi ke server gagal. Coba lagi.', 'error');
      return null;
    }
  },

  get(endpoint)             { return this.request('GET', endpoint); },
  post(endpoint, body)      { return this.request('POST', endpoint, body); },
  put(endpoint, body)       { return this.request('PUT', endpoint, body); },
  patch(endpoint, body)     { return this.request('PATCH', endpoint, body); },
  delete(endpoint)          { return this.request('DELETE', endpoint); },
  upload(endpoint, formData){ return this.request('POST', endpoint, formData, true); },
};

// ── Toast ──
const Toast = {
  show(msg, type = 'info', duration = 3500) {
    let container = document.getElementById('toast-container');
    if (!container) {
      container = document.createElement('div');
      container.id = 'toast-container';
      document.body.appendChild(container);
    }
    const icons = { success: '✅', error: '❌', info: 'ℹ️', warning: '⚠️' };
    const el = document.createElement('div');
    el.className = `toast ${type}`;
    el.innerHTML = `<span>${icons[type]||'•'}</span><span>${msg}</span>`;
    container.appendChild(el);
    requestAnimationFrame(() => el.classList.add('show'));
    setTimeout(() => {
      el.classList.remove('show');
      setTimeout(() => el.remove(), 350);
    }, duration);
  }
};

// ── Modal ──
const Modal = {
  show(id)  { document.getElementById(id)?.classList.add('show'); },
  hide(id)  { document.getElementById(id)?.classList.remove('show'); },
  init(id)  {
    const el = document.getElementById(id);
    if (!el) return;
    el.addEventListener('click', e => { if (e.target === el) el.classList.remove('show'); });
  }
};

// ── Format Helpers ──
const Fmt = {
  date(d) {
    if (!d) return '—';
    return new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
  },
  datetime(d) {
    if (!d) return '—';
    return new Date(d).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
  },
  statusLabel(s) {
    const m = { menunggu:'Menunggu', diverifikasi:'Diverifikasi', ditindaklanjuti:'Ditindaklanjuti', selesai:'Selesai', ditutup:'Ditutup' };
    return m[s] || s;
  },
  kendalaLabel(s) {
    const m = {
      menolak_diwawancara:'Menolak Diwawancara', tidak_ditemui:'Tidak Ditemui',
      alasan_privasi:'Alasan Privasi', usaha_tutup:'Usaha Tutup',
      responden_pindah:'Responden Pindah', tidak_ada_waktu:'Tidak Ada Waktu', lainnya:'Lainnya'
    };
    return m[s] || s;
  },
  arahanLabel(s) {
    const m = { ke_pml:'Ke PML', ke_taskforce:'Ke Taskforce', ke_subject_matter:'Ke Subject Matter' };
    return m[s] || (s || '—');
  },
  roleLabel(s) {
    const m = { petugas:'Petugas', koordinator:'Koordinator', admin:'Admin', pimpinan:'Pimpinan' };
    return m[s] || s;
  },
  initials(name) {
    return (name||'').split(' ').map(w=>w[0]).join('').substring(0,2).toUpperCase();
  },
  avatarColor(role) {
    const m = { admin:'#1E3E6E', koordinator:'#0D9488', petugas:'#059669', pimpinan:'#5B21B6' };
    return m[role] || '#1E3E6E';
  }
};

// ── Sidebar Builder ──
const Sidebar = {
  NAV: [
    {
      label: 'Menu Utama',
      items: [
        { icon:'📊', text:'Dashboard',        href:'/dashboard',  roles:['admin','koordinator','pimpinan','petugas'] },
        { icon:'📋', text:'Daftar Laporan',   href:'/laporan',    roles:['admin','koordinator','pimpinan','petugas'], badge: true },
        { icon:'➕', text:'Buat Laporan',     href:'/form-laporan', roles:['petugas','koordinator','admin'] },
        // { icon:'👤', text:'Profil Saya',      href:'/profil',       roles:['admin','koordinator','pimpinan','petugas'] },
      ]
    },
    {
      label: 'Manajemen',
      roles: ['admin','koordinator','pimpinan'],
      items: [
        { icon:'🗺️', text:'Monitoring Wilayah', href:'/monitoring',  roles:['admin','koordinator','pimpinan'] },
        { icon:'👥', text:'Aktivitas Petugas',  href:'/petugas',     roles:['admin','koordinator','pimpinan'] },
        { icon:'⚙️', text:'Kelola User',        href:'/users',       roles:['admin'] },
      ]
    }
  ],

  render(activeHref) {
    const user = Auth.getUser();
    if (!user) return;

    const sb = document.querySelector('.sidebar');
    if (!sb) return;

    // Brand
    sb.innerHTML = `
      <div class="sb-brand">
        <div class="sb-brand-icon">RB</div>
        <div class="sb-brand-text">
          <div class="sb-brand-name">Respon Blora</div>
          <div class="sb-brand-sub">BPS Kab. Blora</div>
        </div>
      </div>
      <div class="sb-user">
        <div class="sb-avatar" style="background:${Fmt.avatarColor(user.role)}">${Fmt.initials(user.name)}</div>
        <div>
          <div class="sb-user-name">${user.name}</div>
          <div class="sb-user-role">${Fmt.roleLabel(user.role)}</div>
        </div>
      </div>
      <nav class="sb-nav" id="sb-nav"></nav>
      <div class="sb-footer">
        <button class="sb-logout" onclick="doLogout()">🚪 Keluar dari Sistem</button>
      </div>
    `;

    const nav = document.getElementById('sb-nav');
    const menungguCount = parseInt(sessionStorage.getItem('rb_menunggu') || '0');

    this.NAV.forEach(section => {
      if (section.roles && !section.roles.includes(user.role)) return;

      const items = section.items.filter(item => item.roles.includes(user.role));
      if (items.length === 0) return;

      const sec = document.createElement('div');
      sec.className = 'sb-nav-section';
      sec.innerHTML = `<div class="sb-nav-label">${section.label}</div>`;

      items.forEach(item => {
        const isActive = activeHref && window.location.pathname.endsWith(item.href);
        const badge = item.badge && menungguCount > 0
          ? `<span class="sb-badge">${menungguCount}</span>` : '';
        const a = document.createElement('a');
        a.href = item.href;
        a.className = `sb-nav-item${isActive ? ' active' : ''}`;
        a.innerHTML = `<span class="sb-nav-icon">${item.icon}</span><span class="sb-nav-text">${item.text}</span>${badge}`;
        sec.appendChild(a);
      });

      nav.appendChild(sec);
    });
  }
};

// ── Topbar Builder ──
function buildTopbar(title, crumb) {
  const tb = document.querySelector('.topbar');
  if (!tb) return;
  tb.innerHTML = `
    <div style="display:flex;align-items:center;gap:12px">
      <button class="hamburger" id="hamburger-btn" title="Menu">☰</button>
      <div class="topbar-left">
        <div class="topbar-title">${title}</div>
        <div class="topbar-crumb">Respon Blora · ${crumb}</div>
      </div>
    </div>
    <div class="topbar-right">
      <div class="topbar-icon-btn" id="btn-refresh" title="Refresh">🔄</div>
      <div class="topbar-icon-btn" title="Notifikasi" style="position:relative" id="btn-notif" onclick="toggleNotifPanel()">
        🔔<span class="notif-dot" id="notif-dot" style="display:none"></span>
      </div>
      <a href="/profil" title="Profil Saya: ${Auth.getUser()?.name||''}" style="text-decoration:none">
        <div style="width:34px;height:34px;border-radius:50%;background:${Fmt.avatarColor(Auth.getUser()?.role)};display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:white;cursor:pointer;transition:opacity 0.15s" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">${Fmt.initials(Auth.getUser()?.name||'')}</div>
      </a>
    </div>
  `;

  // Pasang event listener langsung — tidak pakai onclick string
  document.getElementById('hamburger-btn')?.addEventListener('click', toggleSidebar);
  document.getElementById('btn-refresh')?.addEventListener('click', () => window.location.reload());

  // Overlay
  if (!document.getElementById('sidebar-overlay')) {
    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    overlay.id = 'sidebar-overlay';
    overlay.addEventListener('click', closeSidebar);
    document.body.appendChild(overlay);
  }
}

function toggleSidebar() {
  const sidebar = document.querySelector('.sidebar');
  const overlay = document.getElementById('sidebar-overlay');
  if (!sidebar) return;
  sidebar.classList.toggle('open');
  overlay?.classList.toggle('show');
}

function closeSidebar() {
  const sidebar = document.querySelector('.sidebar');
  const overlay = document.getElementById('sidebar-overlay');
  sidebar?.classList.remove('open');
  overlay?.classList.remove('show');
}

// ── Logout ──
function doLogout() {
  Api.post('/auth/logout').finally(() => {
    Auth.clear();
    window.location.href = '/login';
  });
}

// ── Toast container on load ──
document.addEventListener('DOMContentLoaded', () => {
  if (!document.getElementById('toast-container')) {
    const c = document.createElement('div');
    c.id = 'toast-container';
    document.body.appendChild(c);
  }
});

// ── Notifikasi ──
let notifData = [];

async function loadNotifikasi() {
  const res = await Api.get('/notifikasi');
  if (!res?.ok) return;
  notifData = res.data.data || [];
  const unread = res.data.unread || 0;

  // Update dot
  const dot = document.getElementById('notif-dot');
  if (dot) dot.style.display = unread > 0 ? 'block' : 'none';

  // Update badge di sidebar laporan
  sessionStorage.setItem('rb_menunggu', unread);
}

function toggleNotifPanel() {
  let panel = document.getElementById('notif-panel');
  if (panel) { panel.remove(); return; }

  const tipeIcon = { laporan_baru:'📋', diverifikasi:'✅', ditindaklanjuti:'🔄', selesai:'🏆', ditutup:'🔒' };
  const tipeColor = { laporan_baru:'var(--blue)', diverifikasi:'var(--teal)', ditindaklanjuti:'var(--purple)', selesai:'var(--green)', ditutup:'var(--red)' };

  panel = document.createElement('div');
  panel.id = 'notif-panel';
  const isMobile = window.innerWidth <= 600;
  panel.style.cssText = `
    position:fixed;
    top:${isMobile ? '0' : '56px'};
    right:${isMobile ? '0' : '16px'};
    left:${isMobile ? '0' : 'auto'};
    bottom:${isMobile ? '0' : 'auto'};
    width:${isMobile ? '100%' : '360px'};
    max-height:${isMobile ? '100dvh' : '480px'};
    background:white;
    border:${isMobile ? 'none' : '1px solid var(--gray-100)'};
    border-radius:${isMobile ? '0' : '12px'};
    box-shadow:0 8px 32px rgba(0,0,0,0.12);
    z-index:1000;
    overflow:hidden;
    display:flex;
    flex-direction:column;
  `;
  // panel.style.cssText = `
  //   position:fixed; top:56px; right:16px; width:360px; max-height:480px;
  //   background:white; border:1px solid var(--gray-100); border-radius:12px;
  //   box-shadow:0 8px 32px rgba(0,0,0,0.12); z-index:999; overflow:hidden;
  //   display:flex; flex-direction:column;
  // `;

  const unread = notifData.filter(n => !n.is_read).length;

  panel.innerHTML = `
    <div style="padding:14px 16px;border-bottom:1px solid var(--gray-100);display:flex;align-items:center;justify-content:space-between">
        <div style="display:flex;align-items:center;gap:10px">
          ${isMobile ? `<button onclick="document.getElementById('notif-panel').remove()" style="background:none;border:none;cursor:pointer;font-size:18px;color:var(--gray-500);padding:0;line-height:1">←</button>` : ''}
          <div style="font-weight:700;font-size:14px;color:var(--gray-900)">
            🔔 Notifikasi ${unread > 0 ? `<span style="background:var(--red);color:white;font-size:10px;padding:1px 6px;border-radius:100px;margin-left:4px">${unread}</span>` : ''}
          </div>
        </div>
        <button onclick="markAllRead()" style="font-size:11.5px;color:var(--blue);background:none;border:none;cursor:pointer;font-weight:600;font-family:inherit">
          Tandai semua dibaca
        </button>
      </div>
    <div style="overflow-y:auto;flex:1" id="notif-list">
      ${notifData.length === 0
        ? `<div style="text-align:center;padding:40px;color:var(--gray-400);font-size:13px">Belum ada notifikasi</div>`
        : notifData.map(n => `
          <div onclick="clickNotif(${n.id}, ${n.laporan_id})" style="
            padding:12px 16px; cursor:pointer; border-bottom:1px solid var(--gray-100);
            background:${n.is_read ? 'white' : 'rgba(37,99,235,0.04)'};
            display:flex; gap:10px; align-items:flex-start;
            transition:background 0.15s;
          " onmouseover="this.style.background='var(--gray-50)'" onmouseout="this.style.background='${n.is_read ? 'white' : 'rgba(37,99,235,0.04)'}'">
            <div style="width:34px;height:34px;border-radius:8px;background:${tipeColor[n.tipe]||'var(--blue)'}18;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0">
              ${tipeIcon[n.tipe]||'🔔'}
            </div>
            <div style="flex:1;min-width:0">
              <div style="font-size:13px;font-weight:${n.is_read?'500':'700'};color:var(--gray-900);margin-bottom:2px">${n.judul}</div>
              <div style="font-size:12px;color:var(--gray-500);line-height:1.4;margin-bottom:4px">${n.pesan}</div>
              <div style="font-size:11px;color:var(--gray-400)">${Fmt.datetime(n.created_at)}</div>
            </div>
            ${!n.is_read ? '<div style="width:7px;height:7px;border-radius:50%;background:var(--blue);flex-shrink:0;margin-top:4px"></div>' : ''}
          </div>`
        ).join('')}
    </div>
  `;

  document.body.appendChild(panel);

  // Tutup kalau klik di luar
  setTimeout(() => {
    document.addEventListener('click', function closePanel(e) {
      if (!panel.contains(e.target) && e.target.id !== 'btn-notif') {
        panel.remove();
        document.removeEventListener('click', closePanel);
      }
    });
  }, 100);
}

async function clickNotif(id, laporanId) {
  await Api.patch(`/notifikasi/${id}/read`);
  document.getElementById('notif-panel')?.remove();
  if (laporanId) window.location.href = `/laporan?id=${laporanId}`;
  await loadNotifikasi();
}

async function markAllRead() {
  await Api.patch('/notifikasi/read-all');
  document.getElementById('notif-panel')?.remove();
  await loadNotifikasi();
}

// Expose ke global scope agar bisa diakses dari script inline Blade
window.Auth   = Auth;
window.Api    = Api;
window.Toast  = Toast;
window.Modal  = Modal;
window.Fmt    = Fmt;
window.Sidebar = Sidebar;
window.buildTopbar = buildTopbar;
window.doLogout    = doLogout;
// window.doLogin   = doLogin;
// window.demo      = demo;
// window.toggleEye = toggleEye;
window.toggleSidebar = toggleSidebar;
window.closeSidebar  = closeSidebar;
window.toggleNotifPanel = toggleNotifPanel;
window.markAllRead      = markAllRead;
window.clickNotif       = clickNotif;
window.loadNotifikasi   = loadNotifikasi;