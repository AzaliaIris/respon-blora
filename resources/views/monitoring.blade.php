<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Monitoring Wilayah — Respon Blora</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<style>
  .monitoring-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
    margin-bottom: 22px;
  }

  /* Kecamatan progress cards */
  .kec-list { display: flex; flex-direction: column; gap: 10px; }

  .kec-item {
    display: flex; flex-direction: column; gap: 6px;
    padding: 14px 16px;
    border: 1px solid var(--gray-100);
    border-radius: var(--radius-sm);
    transition: all var(--transition);
    cursor: default;
  }
  .kec-item:hover { border-color: var(--blue-light); background: #FAFBFF; }

  .kec-header { display: flex; justify-content: space-between; align-items: center; }
  .kec-name   { font-size: 14px; font-weight: 700; color: var(--gray-800); }
  .kec-total  { font-size: 12px; color: var(--gray-400); font-weight: 500; }

  .kec-stats  { display: flex; gap: 8px; flex-wrap: wrap; }
  .kec-stat   { font-size: 11px; padding: 2px 8px; border-radius: 100px; font-weight: 600; }

  .kec-progress-row { display: flex; align-items: center; gap: 8px; }
  .kec-pct { font-size: 12px; font-weight: 700; min-width: 38px; text-align: right; color: var(--navy); }

  /* Bar chart horizontal */
  .hbar-list { display: flex; flex-direction: column; gap: 12px; }
  .hbar-item { display: flex; flex-direction: column; gap: 5px; }
  .hbar-label { display: flex; justify-content: space-between; font-size: 12px; }
  .hbar-name  { color: var(--gray-700); font-weight: 500; }
  .hbar-val   { color: var(--navy); font-weight: 700; }
  .hbar-track { background: var(--gray-100); border-radius: 100px; height: 10px; overflow: hidden; }
  .hbar-fill  { height: 100%; border-radius: 100px; transition: width 0.7s cubic-bezier(0.4,0,0.2,1); }

  /* Arahan breakdown */
  .arahan-grid {
    display: grid;
    grid-template-columns: repeat(3,1fr);
    gap: 14px;
  }

  .arahan-card {
    text-align: center;
    padding: 20px 14px;
    border: 1px solid var(--gray-100);
    border-radius: var(--radius-sm);
    transition: all var(--transition);
  }
  .arahan-card:hover { transform: translateY(-2px); box-shadow: var(--shadow); }

  .arahan-icon   { font-size: 28px; margin-bottom: 8px; }
  .arahan-count  { font-size: 32px; font-weight: 800; color: var(--navy); margin-bottom: 4px; line-height: 1; }
  .arahan-label  { font-size: 12px; color: var(--gray-500); font-weight: 500; margin-bottom: 8px; }
  .arahan-selesai{ font-size: 11px; color: var(--green); font-weight: 600; }

  /* Trend mini chart */
  .sparkline { display: flex; align-items: flex-end; gap: 4px; height: 44px; }
  .spark-bar {
    flex: 1; border-radius: 3px 3px 0 0; min-width: 8px;
    transition: height 0.5s cubic-bezier(0.4,0,0.2,1);
    position: relative;
  }
  .spark-bar:hover::after {
    content: attr(data-tip);
    position: absolute; bottom: 105%; left: 50%;
    transform: translateX(-50%);
    background: var(--gray-900); color: white;
    font-size: 10px; padding: 3px 7px; border-radius: 4px;
    white-space: nowrap; z-index: 10;
    font-family: 'Plus Jakarta Sans', sans-serif;
  }

  /* Filter tabs */
  .tab-bar {
    display: flex; gap: 6px;
    background: var(--gray-100);
    border-radius: var(--radius-sm);
    padding: 4px;
    display: inline-flex;
  }

  .tab-btn {
    padding: 6px 16px;
    border-radius: 6px;
    border: none;
    font-family: inherit;
    font-size: 12.5px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition);
    color: var(--gray-500);
    background: transparent;
  }
  .tab-btn.active {
    background: white;
    color: var(--navy);
    box-shadow: var(--shadow-xs);
  }
  .stat-grid-4 { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; }
  @media (max-width:900px) { .stat-grid-4 { grid-template-columns:repeat(2,1fr); } }
  @media (max-width:480px) { .stat-grid-4 { grid-template-columns:1fr; } }
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
          <h1>Monitoring Wilayah</h1>
          <p>Sebaran dan progress penyelesaian laporan per kecamatan</p>
        </div>
        <div class="page-header-right">
          <div class="tab-bar">
            <button class="tab-btn active" onclick="setFilter('semua',this)">Semua Waktu</button>
            <button class="tab-btn" onclick="setFilter('bulan',this)">Bulan Ini</button>
            <button class="tab-btn" onclick="setFilter('minggu',this)">Minggu Ini</button>
          </div>
          <button class="btn btn-outline" onclick="loadAll()">🔄 Refresh</button>
        </div>
      </div>

      <!-- Top stat cards -->
      <div class="stat-grid-4" style="margin-bottom:22px">
        <div class="stat-card blue anim-1">
          <div class="stat-header"><div class="stat-icon blue">🗺️</div></div>
          <div class="stat-number" id="m-kec">—</div>
          <div class="stat-label">Kecamatan Aktif</div>
        </div>
        <div class="stat-card amber anim-2">
          <div class="stat-header"><div class="stat-icon amber">⚠️</div></div>
          <div class="stat-number" id="m-tertinggi-nama" style="font-size:18px;margin-top:4px">—</div>
          <div class="stat-label">Kecamatan Terbanyak Laporan</div>
        </div>
        <div class="stat-card teal anim-3">
          <div class="stat-header"><div class="stat-icon teal">🏆</div></div>
          <div class="stat-number" id="m-tertinggi-pct" style="font-size:28px">—%</div>
          <div class="stat-label">Kecamatan Terbaik (% Selesai)</div>
        </div>
        <div class="stat-card red anim-4">
          <div class="stat-header"><div class="stat-icon red">⏳</div></div>
          <div class="stat-number" id="m-pending">—</div>
          <div class="stat-label">Total Belum Diselesaikan</div>
        </div>
      </div>

      <!-- Main Grid -->
      <div class="monitoring-grid">

        <!-- Progress per Kecamatan -->
        <div class="card anim-2">
          <div class="card-header">
            <div>
              <div class="card-title">📊 Progress per Kecamatan</div>
              <div class="card-subtitle">Persentase laporan yang selesai</div>
            </div>
          </div>
          <div class="card-body">
            <div class="kec-list" id="kec-list">
              <div style="text-align:center;padding:40px;color:var(--gray-400)">Memuat data...</div>
            </div>
          </div>
        </div>

        <!-- Kendala terbanyak -->
        <div class="card anim-3">
          <div class="card-header">
            <div>
              <div class="card-title">⚠️ Ranking Jenis Kendala</div>
              <div class="card-subtitle">Urutan dari yang paling sering terjadi</div>
            </div>
          </div>
          <div class="card-body">
            <div class="hbar-list" id="kendala-hbar">
              <div style="text-align:center;padding:40px;color:var(--gray-400)">Memuat data...</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Arahan Tindak Lanjut Breakdown -->
      <div class="card anim-4" style="margin-bottom:22px">
        <div class="card-header">
          <div>
            <div class="card-title">🎯 Rekap Arahan Tindak Lanjut</div>
            <div class="card-subtitle">Distribusi laporan berdasarkan arahan verifikasi admin</div>
          </div>
        </div>
        <div class="card-body">
          <div class="arahan-grid" id="arahan-grid">
            <div style="text-align:center;padding:40px;color:var(--gray-400);grid-column:1/-1">Memuat data...</div>
          </div>
        </div>
      </div>

      <!-- Tren 6 Bulan -->
      <div class="card anim-5">
        <div class="card-header">
          <div>
            <div class="card-title">📈 Tren Bulanan per Kecamatan</div>
            <div class="card-subtitle">Perbandingan volume laporan 6 bulan terakhir</div>
          </div>
        </div>
        <div class="card-body">
          <div id="tren-bulan-wrap">
            <div style="text-align:center;padding:40px;color:var(--gray-400)">Memuat data...</div>
          </div>
        </div>
      </div>

    </main>
  </div>
</div>

<script type="module">
  const user = requireAuth();
  if (user) {
    if (!['admin','koordinator','pimpinan'].includes(user.role)) {
      window.location.href = '/dashboard';
    }
    Sidebar.render('/monitoring');
    buildTopbar('Monitoring Wilayah', 'Manajemen → Monitoring Wilayah');
    loadNotifikasi();
    setTimeout(() => loadAll(), 100);
  }

  let filterMode = 'semua';

  function setFilter(mode, btn) {
    filterMode = mode;
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    loadAll();
  }

  async function loadAll() {
    await Promise.all([loadKecamatan(), loadKendala(), loadArahan()]);
  }

  async function loadKecamatan() {
    const now = new Date();
    let qs = '';
    if (filterMode === 'bulan') {
        qs = `?tahun=${now.getFullYear()}&bulan=${now.getMonth()+1}`;
    } else if (filterMode === 'minggu') {
        const start = new Date(now); start.setDate(now.getDate() - 7);
        qs = `?tanggal_dari=${start.toISOString().slice(0,10)}&tanggal_sampai=${now.toISOString().slice(0,10)}`;
    }

    const res = await Api.get('/dashboard/per-kecamatan' + qs);
    if (!res?.ok) return;

    // ← FIX: response adalah {data: [...], total: N}
    const raw  = res.data.data;
    const data = Array.isArray(raw) ? raw : (raw.data || []);

    if (!data || data.length === 0) {
        document.getElementById('kec-list').innerHTML = '<div class="empty-state"><div class="empty-icon">🗺️</div><div class="empty-title">Belum ada data kecamatan</div></div>';
        ['m-kec','m-tertinggi-nama','m-tertinggi-pct','m-pending'].forEach(id => {
            document.getElementById(id).textContent = '0';
        });
        return;
    }

    const totalPending = data.reduce((a, d) => 
      a + Number(d.menunggu||0) + Number(d.diverifikasi||0) + Number(d.ditindaklanjuti||0), 0);
    const terbanyak    = data.reduce((a,b) => Number(b.total||0) > Number(a.total||0) ? b : a, data[0]);
    const terbaik      = data.reduce((a,b) => Number(b.persen_selesai||0) > Number(a.persen_selesai||0) ? b : a, data[0]);

    document.getElementById('m-kec').textContent            = data.length;
    document.getElementById('m-tertinggi-nama').textContent = terbanyak.kecamatan || '—';
    document.getElementById('m-tertinggi-pct').textContent  = (terbaik.persen_selesai||0) + '%';
    document.getElementById('m-pending').textContent        = totalPending;

    const barColors = ['#0B1F3A','#2563EB','#0D9488','#F59E0B','#7C3AED','#10B981','#EF4444','#EC4899'];

    document.getElementById('kec-list').innerHTML = data.map((d, i) => {
      const pct    = d.persen_selesai || 0;
      const color  = barColors[i % barColors.length];
      const statuses = [
        ['menunggu', '#CA8A04', d.menunggu||0],
        ['diverifikasi', '#2563EB', d.diverifikasi||0],
        ['ditindaklanjuti', '#7C3AED', d.ditindaklanjuti||0],
        ['selesai', '#10B981', d.selesai||0],
        ['ditutup', '#EF4444', d.ditutup||0],
      ].filter(s => s[2] > 0);

      return `
        <div class="kec-item">
          <div class="kec-header">
            <div class="kec-name">📍 ${d.kecamatan}</div>
            <div class="kec-total">${d.total} laporan</div>
          </div>
          <div class="kec-stats">
            ${statuses.map(s => `<span class="kec-stat" style="background:${s[1]}18;color:${s[1]}">${s[2]} ${s[0]}</span>`).join('')}
          </div>
          <div class="kec-progress-row">
            <div class="progress-wrap" style="flex:1">
              <div class="progress-fill" style="width:${pct}%;background:${color}"></div>
            </div>
            <span class="kec-pct">${pct}%</span>
          </div>
        </div>
      `;
    }).join('');
  }

  async function loadKendala() {
    const res = await Api.get('/dashboard/per-kendala');
    if (!res?.ok) return;
    const data   = res.data.data.data || [];
    const maxVal = Math.max(...data.map(d => d.total), 1);
    const colors = ['#0B1F3A','#2563EB','#0D9488','#F59E0B','#7C3AED','#10B981','#EF4444'];

    document.getElementById('kendala-hbar').innerHTML = data.map((d, i) => `
      <div class="hbar-item">
        <div class="hbar-label">
          <span class="hbar-name">${d.label}</span>
          <span class="hbar-val">${d.total} kasus</span>
        </div>
        <div class="hbar-track">
          <div class="hbar-fill" style="width:${(d.total/maxVal*100).toFixed(1)}%;background:${colors[i%colors.length]}"></div>
        </div>
        <div style="font-size:11px;color:var(--gray-400);margin-top:2px">
          ${d.persen_berhasil||0}% berhasil didata &nbsp;·&nbsp; ${d.persen_dari_total||0}% dari semua kasus
        </div>
      </div>
    `).join('');
  }

  async function loadArahan() {
      const res = await Api.get('/dashboard/tingkat-selesai');
      if (!res?.ok) return;
      const resData = res.data.data;

      // per_arahan adalah array, ubah jadi object by key
      const arahanMap = {};
      (resData.per_arahan || []).forEach(a => {
          arahanMap[a.arahan_tindak_lanjut] = {
              total   : a.total,
              selesai : a.selesai,
              persen  : a.persen_selesai || 0,
          };
      });

      const cfg = [
          { key: 'ke_pml',            icon: '👤', label: 'Ke PML',            color: 'var(--blue)'   },
          { key: 'ke_taskforce',      icon: '🛡️', label: 'Ke Taskforce',      color: 'var(--teal)'   },
          { key: 'ke_subject_matter', icon: '📚', label: 'Ke Subject Matter',  color: 'var(--purple)' },
      ];

      document.getElementById('arahan-grid').innerHTML = cfg.map(c => {
          const d = arahanMap[c.key] || { total: 0, selesai: 0, persen: 0 };
          return `
              <div class="arahan-card">
                  <div class="arahan-icon">${c.icon}</div>
                  <div class="arahan-count" style="color:${c.color}">${d.total}</div>
                  <div class="arahan-label">${c.label}</div>
                  <div class="progress-wrap" style="margin-bottom:8px">
                      <div class="progress-fill" style="width:${d.persen}%;background:${c.color}"></div>
                  </div>
                  <div class="arahan-selesai">${d.persen}% selesai (${d.selesai} kasus)</div>
              </div>
          `;
      }).join('');

      renderTrenBulan(resData.tren_bulanan || []);
  }

  function renderTrenBulan(tren) {
    if (!tren || tren.length === 0) {
      document.getElementById('tren-bulan-wrap').innerHTML = '<div class="empty-state"><div class="empty-icon">📊</div><div class="empty-title">Belum ada data tren</div></div>';
      return;
    }

    const maxV = Math.max(...tren.map(t => t.total), 1);
    const colors = { selesai: '#10B981', ditutup: '#EF4444', proses: '#2563EB' };

    document.getElementById('tren-bulan-wrap').innerHTML = `
      <div style="display:flex;gap:24px;align-items:flex-end;height:160px;padding-bottom:36px;position:relative">
        <div style="position:absolute;bottom:36px;left:0;right:0;height:1px;background:var(--gray-100)"></div>
        ${tren.map(t => {
          const totalH = (t.total / maxV) * 130;
          const selesaiH = (t.selesai / maxV) * 130;
          const ditutupH = ((t.ditutup||0) / maxV) * 130;
          return `
            <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:0">
              <div style="flex:1;display:flex;align-items:flex-end;width:100%;gap:2px">
                <div style="flex:1;height:${selesaiH}px;background:${colors.selesai};border-radius:3px 3px 0 0;opacity:0.85" title="Selesai: ${t.selesai}"></div>
                <div style="flex:1;height:${ditutupH}px;background:${colors.ditutup};border-radius:3px 3px 0 0;opacity:0.7" title="Ditutup: ${t.ditutup||0}"></div>
                <div style="flex:1;height:${Math.max(totalH - selesaiH - ditutupH, 2)}px;background:${colors.proses};border-radius:3px 3px 0 0;opacity:0.5" title="Proses: ${t.total-t.selesai-(t.ditutup||0)}"></div>
              </div>
              <div style="font-size:10px;color:var(--gray-400);margin-top:7px;text-align:center">${t.bulan}</div>
              <div style="font-size:10px;font-weight:700;color:var(--navy)">${t.total}</div>
            </div>
          `;
        }).join('')}
      </div>
      <div style="display:flex;gap:20px;justify-content:center;flex-wrap:wrap;margin-top:4px">
        <div style="display:flex;align-items:center;gap:6px;font-size:11.5px;color:var(--gray-500)"><div style="width:10px;height:10px;border-radius:2px;background:${colors.selesai}"></div>Selesai</div>
        <div style="display:flex;align-items:center;gap:6px;font-size:11.5px;color:var(--gray-500)"><div style="width:10px;height:10px;border-radius:2px;background:${colors.ditutup}"></div>Ditutup</div>
        <div style="display:flex;align-items:center;gap:6px;font-size:11.5px;color:var(--gray-500)"><div style="width:10px;height:10px;border-radius:2px;background:${colors.proses}"></div>Sedang Diproses</div>
      </div>
    `;
  }
  window.setFilter = setFilter;
  window.loadAll   = loadAll;
</script>
</body>
</html>
