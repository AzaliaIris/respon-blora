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
      <div class="topbar-icon-btn" title="Notifikasi" style="position:relative">
        🔔<span class="notif-dot"></span>
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