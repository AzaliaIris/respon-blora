<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard — Respon Blora</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<style>
  .greeting-bar {
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-light) 60%, #1D4B90 100%);
    border-radius: var(--radius);
    padding: 26px 32px;
    color: white;
    margin-bottom: 22px;
    position: relative;
    overflow: hidden;
  }
  .greeting-bar::after {
    content: '📊';
    position: absolute; right: 28px; top: 50%;
    transform: translateY(-50%);
    font-size: 72px; opacity: 0.1;
  }
  .greeting-bar h2 { font-size: 21px; font-weight: 800; margin-bottom: 4px; }
  .greeting-bar p  { font-size: 13.5px; color: rgba(255,255,255,0.6); }

  .stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 22px;
  }

  .dash-grid {
    display: grid;
    grid-template-columns: 3fr 2fr;
    gap: 18px;
    margin-bottom: 22px;
  }

  /* Chart */
  .chart-wrap { padding: 6px 0 0; }
  .chart-bars {
    display: flex;
    align-items: flex-end;
    gap: 8px;
    height: 150px;
    padding-bottom: 28px;
    position: relative;
  }
  .chart-bars::after {
    content: '';
    position: absolute;
    bottom: 28px; left: 0; right: 0;
    height: 1px; background: var(--gray-100);
  }
  .bar-col { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 0; }
  .bar-stack { display: flex; flex-direction: column-reverse; align-items: center; width: 100%; flex: 1; justify-content: flex-start; }
  .bar-seg {
    width: 100%; border-radius: 4px 4px 0 0;
    transition: height 0.6s cubic-bezier(0.4,0,0.2,1);
    position: relative;
  }
  .bar-seg:hover::after {
    content: attr(data-tip);
    position: absolute; bottom: 105%; left: 50%;
    transform: translateX(-50%);
    background: var(--gray-800); color: white;
    font-size: 11px; font-weight: 600;
    padding: 3px 8px; border-radius: 4px;
    white-space: nowrap; z-index: 10;
  }
  .bar-label { font-size: 9.5px; color: var(--gray-400); font-weight: 500; margin-top: 6px; text-align: center; white-space: nowrap; }
  .chart-legend { display: flex; gap: 14px; justify-content: center; margin-top: 12px; flex-wrap: wrap; }
  .legend-item { display: flex; align-items: center; gap: 5px; font-size: 11px; color: var(--gray-500); }
  .legend-dot  { width: 9px; height: 9px; border-radius: 2px; }

  /* Donut */
  .donut-container { display: flex; align-items: center; gap: 20px; padding: 8px 0; }
  .donut-legend-list { flex: 1; }
  .donut-leg-item {
    display: flex; align-items: center; justify-content: space-between;
    font-size: 12px; padding: 6px 0;
    border-bottom: 1px solid var(--gray-50);
  }
  .donut-leg-item:last-child { border-bottom: none; }
  .donut-leg-left { display: flex; align-items: center; gap: 7px; color: var(--gray-600); }
  .donut-leg-dot  { width: 8px; height: 8px; border-radius: 2px; }
  .donut-leg-val  { font-weight: 700; color: var(--gray-800); }

  /* Recent table */
  .recent-wrap { overflow-x: auto; }
</style>
</head>
<body>
<div class="app-layout">
  <aside class="sidebar"></aside>
  <div class="main-area">
    <header class="topbar"></header>
    <main class="content">

      <!-- Greeting -->
      <div class="greeting-bar anim-1">
        <h2 id="greeting-title">Selamat datang! 👋</h2>
        <p id="greeting-desc">Memuat data...</p>
      </div>

      <!-- Stats -->
      <div class="stats-grid">
        <div class="stat-card blue anim-1">
          <div class="stat-header">
            <div class="stat-icon blue">📋</div>
            <span class="stat-trend neutral" id="trend-minggu">—</span>
          </div>
          <div class="stat-number" id="s-total">—</div>
          <div class="stat-label">Total Laporan</div>
        </div>
        <div class="stat-card amber anim-2">
          <div class="stat-header">
            <div class="stat-icon amber">⏳</div>
            <span class="stat-trend neutral">Perlu ditindak</span>
          </div>
          <div class="stat-number" id="s-menunggu">—</div>
          <div class="stat-label">Menunggu Verifikasi</div>
        </div>
        <div class="stat-card teal anim-3">
          <div class="stat-header">
            <div class="stat-icon teal">🔄</div>
            <span class="stat-trend neutral">Sedang berjalan</span>
          </div>
          <div class="stat-number" id="s-proses">—</div>
          <div class="stat-label">Sedang Diproses</div>
        </div>
        <div class="stat-card green anim-4">
          <div class="stat-header">
            <div class="stat-icon green">✅</div>
            <span class="stat-trend up" id="s-persen">—%</span>
          </div>
          <div class="stat-number" id="s-selesai">—</div>
          <div class="stat-label">Berhasil Diselesaikan</div>
        </div>
      </div>

      <!-- Charts Row -->
      <div class="dash-grid">
        <div class="card anim-2">
          <div class="card-header">
            <div>
              <div class="card-title">📈 Tren Laporan Mingguan</div>
              <div class="card-subtitle">8 minggu terakhir</div>
            </div>
          </div>
          <div class="card-body chart-wrap">
            <div class="chart-bars" id="chart-bars"></div>
            <div class="chart-legend">
              <div class="legend-item"><div class="legend-dot" style="background:var(--navy)"></div>Total</div>
              <div class="legend-item"><div class="legend-dot" style="background:var(--green)"></div>Selesai</div>
              <div class="legend-item"><div class="legend-dot" style="background:var(--amber)"></div>Menunggu</div>
            </div>
          </div>
        </div>

        <div class="card anim-3">
          <div class="card-header">
            <div>
              <div class="card-title">🥧 Per Jenis Kendala</div>
              <div class="card-subtitle">Distribusi semua kasus</div>
            </div>
          </div>
          <div class="card-body">
            <div class="donut-container">
              <svg width="120" height="120" viewBox="0 0 120 120" id="donut-svg" style="flex-shrink:0"></svg>
              <div class="donut-legend-list" id="donut-legend"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Kinerja Row -->
      <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:18px;margin-bottom:22px">
        <div class="card anim-3">
          <div class="card-body" style="text-align:center;padding:24px 20px">
            <div style="font-size:36px;font-weight:800;color:var(--navy);margin-bottom:4px" id="k-persen">—%</div>
            <div style="font-size:13px;color:var(--gray-500)">Tingkat Penyelesaian</div>
          </div>
        </div>
        <div class="card anim-4">
          <div class="card-body" style="text-align:center;padding:24px 20px">
            <div style="font-size:36px;font-weight:800;color:var(--teal);margin-bottom:4px" id="k-waktu">— hr</div>
            <div style="font-size:13px;color:var(--gray-500)">Rata-rata Waktu Selesai</div>
          </div>
        </div>
        <div class="card anim-5">
          <div class="card-body" style="text-align:center;padding:24px 20px">
            <div style="font-size:36px;font-weight:800;color:var(--red);margin-bottom:4px" id="k-lama">—</div>
            <div style="font-size:13px;color:var(--gray-500)">Kasus Menunggu &gt;3 Hari</div>
          </div>
        </div>
      </div>

      <!-- Recent Laporan -->
      <div class="card anim-5">
        <div class="card-header">
          <div>
            <div class="card-title">🕐 Laporan Terbaru</div>
            <div class="card-subtitle">5 laporan terakhir masuk</div>
          </div>
          <a href="/laporan" class="btn btn-outline btn-sm">Lihat Semua →</a>
        </div>
        <div class="recent-wrap">
          <table class="data-table">
            <thead>
              <tr>
                <th>Tiket</th>
                <th>Nama Usaha</th>
                <th>Jenis Kendala</th>
                <th>Kecamatan</th>
                <th>Status</th>
                <th>Tanggal</th>
              </tr>
            </thead>
            <tbody id="recent-tbody">
              <tr><td colspan="6" style="text-align:center;padding:32px;color:var(--gray-400)">Memuat data...</td></tr>
            </tbody>
          </table>
        </div>
      </div>

    </main>
  </div>
</div>

<script type="module">
  const user = Auth.check();
  if (user) {
    Sidebar.render('/dashboard');
    buildTopbar('Dashboard', 'Beranda');
    loadNotifikasi();

    const descs = {
      admin: 'Ringkasan laporan kendala pendataan. Segera verifikasi laporan yang masuk.',
      koordinator: 'Pantau perkembangan laporan di wilayah koordinasi Anda.',
      petugas: 'Laporan Anda membantu meningkatkan kualitas pendataan Kabupaten Blora.',
      pimpinan: 'Ringkasan kinerja tim pendataan Kabupaten Blora.',
    };
    document.getElementById('greeting-title').textContent = `Selamat datang, ${user.name.split(' ')[0]}! 👋`;
    document.getElementById('greeting-desc').textContent  = descs[user.role] || '';
  }

  async function loadDashboard() {
    const res = await Api.get('/dashboard/ringkasan');
    if (!res?.ok) return;
    const d = res.data.data;

    document.getElementById('s-total').textContent    = d.total.semua;
    document.getElementById('s-menunggu').textContent = d.total.menunggu;
    document.getElementById('s-proses').textContent   = d.total.diverifikasi + d.total.ditindaklanjuti;
    document.getElementById('s-selesai').textContent  = d.total.selesai;
    document.getElementById('s-persen').textContent   = d.kinerja.tingkat_penyelesaian_persen + '%';
    document.getElementById('k-persen').textContent   = d.kinerja.tingkat_penyelesaian_persen + '%';
    document.getElementById('k-waktu').textContent    = (d.kinerja.rata_waktu_selesai_hari || 0) + ' hr';
    document.getElementById('k-lama').textContent     = d.kinerja.kasus_menunggu_lama;
    document.getElementById('trend-minggu').textContent = '+' + d.periode.minggu_ini + ' minggu ini';

    sessionStorage.setItem('rb_menunggu', d.total.menunggu);
  }

  async function loadTren() {
    const res = await Api.get('/dashboard/tren-mingguan?minggu=8');
    if (!res?.ok) return;
    const data  = res.data.data;
    const maxV  = Math.max(...data.map(d => d.total), 1);
    const total = document.getElementById('chart-bars');

    total.innerHTML = data.map(w => `
      <div class="bar-col">
        <div class="bar-stack">
          <div class="bar-seg" data-tip="Menunggu: ${w.menunggu}"
            style="height:${Math.max((w.menunggu/maxV)*130,2)}px;background:var(--amber);opacity:0.8;border-radius:3px 3px 0 0"></div>
          <div class="bar-seg" data-tip="Proses: ${w.proses}"
            style="height:${Math.max((w.proses/maxV)*130,2)}px;background:var(--blue);opacity:0.7"></div>
          <div class="bar-seg" data-tip="Selesai: ${w.selesai}"
            style="height:${Math.max((w.selesai/maxV)*130,2)}px;background:var(--green)"></div>
        </div>
        <div class="bar-label">${w.label.replace('Minggu ','')}</div>
      </div>
    `).join('');
  }

  async function loadKendala() {
    const res = await Api.get('/dashboard/per-kendala');
    if (!res?.ok) return;
    const data = res.data.data.data.slice(0,5);
    const total = res.data.data.total;
    const colors = ['#0B1F3A','#2563EB','#0D9488','#F59E0B','#10B981'];
    const cx=60, cy=60, r=50, ri=32;
    let start = -Math.PI/2, paths = '';

    data.forEach((d,i) => {
      const angle = (d.total/total)*2*Math.PI;
      const x1=cx+r*Math.cos(start), y1=cy+r*Math.sin(start);
      const x2=cx+r*Math.cos(start+angle), y2=cy+r*Math.sin(start+angle);
      const xi1=cx+ri*Math.cos(start), yi1=cy+ri*Math.sin(start);
      const xi2=cx+ri*Math.cos(start+angle), yi2=cy+ri*Math.sin(start+angle);
      const large = angle>Math.PI?1:0;
      paths+=`<path d="M${xi1},${yi1} A${ri},${ri} 0 ${large},1 ${xi2},${yi2} L${x2},${y2} A${r},${r} 0 ${large},0 ${x1},${y1} Z" fill="${colors[i]}" opacity="0.9"/>`;
      start+=angle;
    });

    document.getElementById('donut-svg').innerHTML = paths +
      `<text x="60" y="56" text-anchor="middle" font-family="Plus Jakarta Sans" font-size="16" font-weight="800" fill="#0F172A">${total}</text>
       <text x="60" y="70" text-anchor="middle" font-family="Plus Jakarta Sans" font-size="9" fill="#94A3B8">Kasus</text>`;

    document.getElementById('donut-legend').innerHTML = data.map((d,i)=>`
      <div class="donut-leg-item">
        <div class="donut-leg-left"><div class="donut-leg-dot" style="background:${colors[i]}"></div>${d.label}</div>
        <span class="donut-leg-val">${d.total}</span>
      </div>
    `).join('');
  }

  async function loadRecent() {
    const res = await Api.get('/laporan?per_page=5');
    if (!res?.ok) return;
    const items = res.data.data?.data || [];
    document.getElementById('recent-tbody').innerHTML = items.length === 0
      ? `<tr><td colspan="6" style="text-align:center;padding:32px;color:var(--gray-400)">Belum ada laporan.</td></tr>`
      : items.map(l => `
        <tr>
          <td><span class="ticket-code">${l.nomor_tiket}</span></td>
          <td><strong style="color:var(--gray-800)">${l.nama_usaha}</strong></td>
          <td style="font-size:12px">${Fmt.kendalaLabel(l.jenis_kendala)}</td>
          <td>${l.kecamatan}</td>
          <td><span class="pill pill-${l.status}">${Fmt.statusLabel(l.status)}</span></td>
          <td style="font-size:12px;color:var(--gray-400)">${Fmt.date(l.tanggal_laporan)}</td>
        </tr>
      `).join('');
  }

  loadDashboard();
  loadTren();
  loadKendala();
  loadRecent();
</script>
</body>
</html>
