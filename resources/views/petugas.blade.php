<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Aktivitas Petugas — Respon Blora</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<style>
  .petugas-filter { display:flex; gap:10px; margin-bottom:18px; flex-wrap:wrap; align-items:center; }
  .petugas-grid {
    display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr));
    gap:16px; margin-bottom:22px;
  }
  .petugas-card {
    background:white; border:1px solid var(--gray-100); border-radius:var(--radius);
    padding:20px; box-shadow:var(--shadow-sm); transition:all var(--transition);
    position:relative; overflow:hidden;
  }
  .petugas-card:hover { transform:translateY(-2px); box-shadow:var(--shadow); }
  .petugas-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; }
  .petugas-card.good::before   { background:linear-gradient(90deg,var(--green),#34D399); }
  .petugas-card.medium::before { background:linear-gradient(90deg,var(--amber),#FBBF24); }
  .petugas-card.low::before    { background:linear-gradient(90deg,var(--gray-400),var(--gray-300)); }
  .petugas-top { display:flex; align-items:center; gap:12px; margin-bottom:16px; }
  .petugas-avatar {
    width:46px; height:46px; border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    font-size:16px; font-weight:700; color:white; flex-shrink:0;
  }
  .petugas-name    { font-size:15px; font-weight:700; color:var(--gray-900); line-height:1.3; }
  .petugas-wilayah { font-size:12px; color:var(--gray-400); margin-top:2px; }
  .petugas-stats-row { display:grid; grid-template-columns:repeat(4,1fr); gap:8px; margin-bottom:14px; }
  .p-stat { text-align:center; }
  .p-stat-num { font-size:18px; font-weight:800; line-height:1; margin-bottom:3px; }
  .p-stat-lbl { font-size:10px; color:var(--gray-400); font-weight:500; }
  .kinerja-label { display:flex; justify-content:space-between; font-size:12px; margin-bottom:6px; color:var(--gray-600); font-weight:500; }
  .kinerja-pct { font-weight:700; color:var(--navy); }
  .table-wrap { overflow-x:auto; -webkit-overflow-scrolling:touch; }
  .data-table { min-width:900px; }

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
          <h1>Aktivitas Petugas</h1>
          <p>Pantau kinerja dan rekap laporan masing-masing petugas lapangan</p>
        </div>
        <div class="page-header-right">
          <button class="btn btn-outline" onclick="loadPetugas()">🔄 Refresh</button>
        </div>
      </div>

      <div class="petugas-filter anim-1">
        <select class="filter-select" id="pf-kecamatan" onchange="loadPetugas()">
          <option value="">Semua Kecamatan</option>
          <option>Blora</option><option>Cepu</option><option>Jepon</option>
          <option>Randublatung</option><option>Kunduran</option>
        </select>
        <select class="filter-select" id="pf-sort" onchange="loadPetugas()">
          <option value="total">Urut: Total Laporan</option>
          <option value="selesai">Urut: Selesai Terbanyak</option>
          <option value="persen">Urut: % Kinerja</option>
        </select>
      </div>

      <div class="petugas-grid" id="petugas-grid">
        <div style="grid-column:1/-1;text-align:center;padding:60px;color:var(--gray-400)">Memuat data petugas...</div>
      </div>

      <div class="anim-4">
        <div class="card-header" style="display:flex;justify-content:space-between;align-items:center">
          <div class="card-title">📋 Tabel Rekap Kinerja Petugas</div>
          <select class="filter-select" id="pt-perpage" onchange="renderTablePage(1)" style="width:auto;font-size:12px">
            <option value="5">5 per halaman</option>
            <option value="10" selected>10 per halaman</option>
            <option value="25">25 per halaman</option>
          </select>
        </div>
        <div style="overflow-x:auto;-webkit-overflow-scrolling:touch;background:white;border-radius:var(--radius-sm);">
        <table class="data-table">
          <thead>
            <tr>
              <th>#</th><th>Nama Petugas</th><th>Wilayah Tugas</th>
              <th>Total</th><th>Menunggu</th><th>Diverifikasi</th>
              <th>Ditindaklanjuti</th><th>Selesai</th><th>Ditutup</th><th>% Kinerja</th>
            </tr>
          </thead>
          <tbody id="petugas-tbody"></tbody>
        </table>
        </div>
        <div class="pagination-wrap" id="petugas-pagination"></div>
      </div>

    </main>
  </div>
</div>

<script type="module">
  const user = Auth.check();
  let allData = [];

  if (user) {
    if (!['admin','koordinator','pimpinan'].includes(user.role)) {
      window.location.href = '/dashboard';
    }
    Sidebar.render('/petugas');
    buildTopbar('Aktivitas Petugas', 'Manajemen → Aktivitas Petugas');
    loadPetugas();
  }

  async function loadPetugas() {
    const kec  = document.getElementById('pf-kecamatan').value;
    const sort = document.getElementById('pf-sort').value;
    const qs   = kec ? `?kecamatan=${encodeURIComponent(kec)}` : '';
    const res  = await Api.get('/dashboard/aktivitas-petugas' + qs);
    if (!res?.ok) return;

    let data = res.data.data?.data || [];
    if (sort === 'selesai') data.sort((a,b) => (b.laporan_selesai_count||0) - (a.laporan_selesai_count||0));
    else if (sort === 'persen') data.sort((a,b) => calcPct(b) - calcPct(a));
    else data.sort((a,b) => (b.laporan_count||0) - (a.laporan_count||0));

    allData = data;
    renderCards(data);
    renderTablePage(1);
  }

  function calcPct(p) {
    const t = p.laporan_count || 0;
    return t ? Math.round(((p.laporan_selesai_count||0) / t) * 100) : 0;
  }

  function renderCards(data) {
    if (!data.length) {
      document.getElementById('petugas-grid').innerHTML = `<div style="grid-column:1/-1"><div class="empty-state"><div class="empty-icon">👤</div><div class="empty-title">Belum ada data petugas</div></div></div>`;
      return;
    }
    document.getElementById('petugas-grid').innerHTML = data.map((p, rank) => {
      const pct = calcPct(p);
      const tier = pct >= 60 ? 'good' : pct >= 30 ? 'medium' : 'low';
      const pctColor = pct >= 60 ? 'var(--green)' : pct >= 30 ? 'var(--amber)' : 'var(--gray-400)';
      return `
        <div class="petugas-card ${tier} anim-${Math.min(rank+1,6)}">
          <div class="petugas-top">
            <div class="petugas-avatar" style="background:${Fmt.avatarColor(p.role||'petugas')}">${Fmt.initials(p.name)}</div>
            <div style="flex:1;min-width:0">
              <div class="petugas-name">${p.name}</div>
              <div class="petugas-wilayah">📍 ${p.wilayah_tugas||'Tidak ditentukan'}</div>
            </div>
            <div style="font-size:18px;font-weight:800;color:${pctColor}">${pct}%</div>
          </div>
          <div class="petugas-stats-row">
            <div class="p-stat"><div class="p-stat-num" style="color:var(--navy)">${p.laporan_count||0}</div><div class="p-stat-lbl">Total</div></div>
            <div class="p-stat"><div class="p-stat-num" style="color:var(--amber)">${p.laporan_menunggu_count||0}</div><div class="p-stat-lbl">Menunggu</div></div>
            <div class="p-stat"><div class="p-stat-num" style="color:var(--green)">${p.laporan_selesai_count||0}</div><div class="p-stat-lbl">Selesai</div></div>
            <div class="p-stat"><div class="p-stat-num" style="color:var(--red)">${p.laporan_ditutup_count||0}</div><div class="p-stat-lbl">Ditutup</div></div>
          </div>
          <div class="kinerja-label"><span>Tingkat Penyelesaian</span><span class="kinerja-pct">${pct}%</span></div>
          <div class="progress-wrap"><div class="progress-fill" style="width:${pct}%;background:${pctColor}"></div></div>
        </div>`;
    }).join('');
  }

  function renderTablePage(page) {
    const perPage = parseInt(document.getElementById('pt-perpage').value);
    const total   = allData.length;
    const pages   = Math.max(1, Math.ceil(total / perPage));
    page = Math.min(Math.max(1, page), pages);
    const slice  = allData.slice((page-1)*perPage, page*perPage);
    const offset = (page-1)*perPage;

    document.getElementById('petugas-tbody').innerHTML = slice.length === 0
      ? `<tr><td colspan="10" style="text-align:center;padding:24px;color:var(--gray-400)">Tidak ada data.</td></tr>`
      : slice.map((p, i) => {
          const pct = calcPct(p);
          const pctColor = pct >= 60 ? 'var(--green)' : pct >= 30 ? 'var(--amber)' : 'var(--red)';
          return `<tr>
            <td><strong>${offset+i+1}</strong></td>
            <td><strong style="color:var(--gray-800)">${p.name}</strong></td>
            <td style="font-size:12px;color:var(--gray-500)">${p.wilayah_tugas||'—'}</td>
            <td><strong>${p.laporan_count||0}</strong></td>
            <td style="color:var(--amber);font-weight:600">${p.laporan_menunggu_count||0}</td>
            <td style="color:var(--blue);font-weight:600">${p.laporan_diverifikasi_count||0}</td>
            <td style="color:var(--purple);font-weight:600">${p.laporan_ditindaklanjuti_count||0}</td>
            <td style="color:var(--green);font-weight:600">${p.laporan_selesai_count||0}</td>
            <td style="color:var(--red);font-weight:600">${p.laporan_ditutup_count||0}</td>
            <td>
              <span style="font-weight:800;color:${pctColor}">${pct}%</span>
              <div class="progress-wrap" style="margin-top:4px;width:60px">
                <div class="progress-fill" style="width:${pct}%;background:${pctColor}"></div>
              </div>
            </td>
          </tr>`;
        }).join('');

    renderPagination('petugas-pagination', page, pages, total, perPage, 'renderTablePage');
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
      <div class="pagination-info">Menampilkan ${start}–${end} dari ${total} data</div>
      <div class="pagination-btns">
        <button class="pg-btn" ${page<=1?'disabled':''} onclick="${fnName}(${page-1})">‹</button>
        ${nums.map(n => n==='...'
          ? `<button class="pg-btn" disabled>…</button>`
          : `<button class="pg-btn ${n===page?'active':''}" onclick="${fnName}(${n})">${n}</button>`
        ).join('')}
        <button class="pg-btn" ${page>=pages?'disabled':''} onclick="${fnName}(${page+1})">›</button>
      </div>`;
  }

  window.loadPetugas     = loadPetugas;
  window.renderTablePage = renderTablePage;
</script>
</body>
</html>