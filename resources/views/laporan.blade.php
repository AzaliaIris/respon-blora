<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar Laporan — Respon Blora</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<style>
  .meta-text { font-size: 12px; color: var(--gray-400); }
  .nama-usaha-cell { font-weight: 600; color: var(--gray-800); max-width: 160px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .petugas-cell    { font-size: 12px; color: var(--gray-500); }

  /* Detail drawer */
  .drawer-overlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.4);
    backdrop-filter: blur(3px);
    z-index: 200;
    opacity: 0; pointer-events: none;
    transition: opacity var(--transition);
  }
  .drawer-overlay.open { opacity: 1; pointer-events: all; }

  .drawer {
    position: fixed; top: 0; right: 0; bottom: 0;
    width: 480px;
    background: white;
    box-shadow: var(--shadow-lg);
    z-index: 201;
    display: flex;
    flex-direction: column;
    transform: translateX(100%);
    transition: transform 0.3s cubic-bezier(0.4,0,0.2,1);
  }
  .drawer.open { transform: translateX(0); }

  .drawer-header {
    padding: 22px 24px;
    border-bottom: 1px solid var(--gray-100);
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .drawer-title { font-size: 16px; font-weight: 700; color: var(--gray-900); }
  .drawer-close {
    width: 32px; height: 32px;
    border-radius: var(--radius-xs); border: 1.5px solid var(--gray-200);
    background: white; cursor: pointer; font-size: 16px;
    display: flex; align-items: center; justify-content: center;
    color: var(--gray-500); transition: all var(--transition);
  }
  .drawer-close:hover { border-color: var(--red); color: var(--red); background: var(--red-pale); }

  .drawer-body { flex: 1; overflow-y: auto; padding: 20px 24px; }

  .detail-section { margin-bottom: 22px; }
  .detail-section-title {
    font-size: 11px; font-weight: 700; color: var(--gray-400);
    text-transform: uppercase; letter-spacing: 0.08em;
    margin-bottom: 10px; padding-bottom: 8px;
    border-bottom: 1px solid var(--gray-100);
  }
  .detail-row { display: flex; gap: 8px; margin-bottom: 8px; font-size: 13px; }
  .detail-key { color: var(--gray-500); font-weight: 500; min-width: 130px; flex-shrink: 0; }
  .detail-val { color: var(--gray-800); font-weight: 500; }

  .drawer-footer {
    padding: 16px 24px;
    border-top: 1px solid var(--gray-100);
    display: flex; gap: 10px;
    background: var(--gray-50);
  }

  /* Verifikasi form inside drawer */
  .verif-form {
    background: var(--blue-pale);
    border: 1px solid #BFDBFE;
    border-radius: var(--radius-sm);
    padding: 16px;
    margin-top: 16px;
  }
  .verif-form-title { font-size: 13px; font-weight: 700; color: #1D4ED8; margin-bottom: 12px; }
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
          <h1>Daftar Laporan</h1>
          <p>Semua laporan kendala pendataan yang masuk ke sistem</p>
        </div>
        <div class="page-header-right">
          <button class="btn btn-outline" onclick="exportCSV()">📥 Ekspor CSV</button>
          <a href="/form-laporan" class="btn btn-primary" id="btn-buat" style="display:none">➕ Buat Laporan</a>
        </div>
      </div>

      <div class="filter-bar anim-2">
        <select class="filter-select" id="f-status" onchange="loadLaporan()">
          <option value="">Semua Status</option>
          <option value="menunggu">Menunggu</option>
          <option value="diverifikasi">Diverifikasi</option>
          <option value="ditindaklanjuti">Ditindaklanjuti</option>
          <option value="selesai">Selesai</option>
          <option value="ditutup">Ditutup</option>
        </select>
        <select class="filter-select" id="f-kecamatan" onchange="loadLaporan()">
          <option value="">Semua Kecamatan</option>
          <option>Blora</option><option>Cepu</option><option>Jepon</option>
                  <option>Randublatung</option><option>Kunduran</option><option>Ngawen</option>
                  <option>Bogorejo</option><option>Todanan</option><option>Japah</option>
                  <option>Banjarejo</option><option>Jati</option><option>Jiken</option>
                  <option>Kedungtuban</option><option>Kradenan</option><option>Sambong</option>
                  <option>Tunjungan</option>
        </select>
        <select class="filter-select" id="f-kendala" onchange="loadLaporan()">
          <option value="">Semua Kendala</option>
          <option value="menolak_diwawancara">Menolak Diwawancara</option>
          <option value="tidak_ditemui">Tidak Ditemui</option>
          <option value="alasan_privasi">Alasan Privasi</option>
          <option value="usaha_tutup">Usaha Tutup</option>
          <option value="tidak_ada_waktu">Tidak Ada Waktu</option>
        </select>
        <input type="text" class="filter-input" id="f-search" placeholder="🔍  Cari nama usaha...">
        <button class="btn btn-outline btn-sm" onclick="loadLaporan()">Terapkan</button>
        <button class="btn btn-outline btn-sm" onclick="resetFilter()">Reset</button>
      </div>

      <div class="table-wrap anim-3">
        <table class="data-table">
          <thead>
            <tr>
              <th>Nomor Tiket</th>
              <th>Nama Usaha</th>
              <th>Petugas</th>
              <th>Kecamatan</th>
              <th>Jenis Kendala</th>
              <th>Status</th>
              <th>Tanggal</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody id="laporan-tbody">
            <tr><td colspan="8" style="text-align:center;padding:48px;color:var(--gray-400)">Memuat data...</td></tr>
          </tbody>
        </table>
      </div>

      <div style="display:flex;align-items:center;justify-content:space-between;margin-top:12px;font-size:12px;color:var(--gray-400)" id="pagination-bar">
        <span id="count-label"></span>
        <div id="pagination-btns" style="display:flex;gap:6px"></div>
      </div>

    </main>
  </div>
</div>

<!-- Detail Drawer -->
<div class="drawer-overlay" id="drawer-overlay" onclick="closeDrawer()"></div>
<div class="drawer" id="detail-drawer">
  <div class="drawer-header">
    <div>
      <div class="drawer-title" id="drawer-tiket">Detail Laporan</div>
    </div>
    <button class="drawer-close" onclick="closeDrawer()">✕</button>
  </div>
  <div class="drawer-body" id="drawer-body">Memuat...</div>
  <div class="drawer-footer" id="drawer-footer"></div>
</div>

<!-- Modal Verifikasi -->
<div class="modal-overlay" id="modal-verif">
  <div class="modal">
    <div class="modal-icon info">✅</div>
    <div class="modal-title">Verifikasi Laporan</div>
    <div class="modal-desc">Tentukan arahan tindak lanjut untuk laporan ini.</div>
    <div class="form-group">
      <label class="form-label">Arahan Tindak Lanjut <span class="req">*</span></label>
      <select class="form-control" id="inp-arahan" onchange="onArahanChange()">
        <option value="">-- Pilih Arahan --</option>
        <option value="ke_pml">Ke PML</option>
        <option value="ke_taskforce">Ke Taskforce (semua)</option>
        <option value="ke_subject_matter">Ke Subject Matter</option>
      </select>
    </div>
    <!-- Dropdown petugas — muncul kalau pilih PML atau Subject Matter -->
    <div class="form-group" id="wrap-ditugaskan" style="display:none">
      <label class="form-label">Pilih Petugas <span class="req">*</span></label>
      <select class="form-control" id="inp-ditugaskan">
        <option value="">-- Memuat daftar petugas... --</option>
      </select>
    </div>
    <!-- Info taskforce -->
    <div id="info-taskforce" style="display:none;background:#EFF6FF;border:1px solid #BFDBFE;border-radius:8px;padding:12px;font-size:13px;color:#1D4ED8;margin-bottom:12px">
      ℹ️ Semua petugas dengan posisi <strong>Taskforce</strong> akan mendapatkan tugas tindak lanjut ini.
    </div>
    <div class="form-group">
      <label class="form-label">Catatan Admin</label>
      <textarea class="form-control" id="inp-catatan" placeholder="Catatan atau instruksi untuk petugas..." rows="3"></textarea>
    </div>
    <input type="hidden" id="verif-id">
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="Modal.hide('modal-verif')">Batal</button>
      <button class="btn btn-primary" onclick="submitVerifikasi()">✅ Verifikasi</button>
    </div>
  </div>
</div>

<!-- Modal Tindak Lanjut -->
<div class="modal-overlay" id="modal-tl">
  <div class="modal">
    <div class="modal-icon warning">🔄</div>
    <div class="modal-title">Update Tindak Lanjut</div>
    <div class="modal-desc">Catat hasil kunjungan ulang ke responden.</div>
    <div class="form-group">
      <label class="form-label">Hasil Kunjungan <span class="req">*</span></label>
      <select class="form-control" id="inp-hasil">
        <option value="">-- Pilih Hasil --</option>
        <option value="berhasil_didata">Berhasil Didata</option>
        <option value="tetap_menolak">Tetap Menolak</option>
        <option value="akan_dikunjungi_ulang">Akan Dikunjungi Ulang</option>
      </select>
    </div>
    <div class="form-group">
      <label class="form-label">Keterangan <span class="req">*</span></label>
      <textarea class="form-control" id="inp-ket" placeholder="Jelaskan detail hasil kunjungan..." rows="3"></textarea>
    </div>
    <div class="form-group">
      <label class="form-label">Tanggal Kunjungan <span class="req">*</span></label>
      <input type="date" class="form-control" id="inp-tgl-kunjungan">
    </div>
    <input type="hidden" id="tl-id">
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="Modal.hide('modal-tl')">Batal</button>
      <button class="btn btn-teal" onclick="submitTindakLanjut()">💾 Simpan</button>
    </div>
  </div>
</div>

<script type="module">
  const user = requireAuth();
  let allData = [], currentPage = 1;

  if (user) {
    Sidebar.render('/laporan');
    buildTopbar('Daftar Laporan', 'Laporan');
    loadNotifikasi();
    Modal.init('modal-verif');
    Modal.init('modal-tl');

    const canCreate = ['petugas','koordinator','admin'].includes(user.role);
    if (canCreate) document.getElementById('btn-buat').style.display = 'flex';

    // Set today as default for tindak lanjut date
    document.getElementById('inp-tgl-kunjungan').value = new Date().toISOString().slice(0,10);

    loadLaporan();
  }

  async function loadLaporan(page = 1) {
    currentPage = page;
    const status   = document.getElementById('f-status').value;
    const kec      = document.getElementById('f-kecamatan').value;
    const kendala  = document.getElementById('f-kendala').value;
    const search  = document.getElementById('f-search').value.trim();

    let qs = `?per_page=15&page=${page}`;
    if (status)  qs += `&status=${status}`;
    if (kec)     qs += `&kecamatan=${encodeURIComponent(kec)}`;
    if (kendala) qs += `&jenis_kendala=${kendala}`;
    if (search)  qs += `&search=${encodeURIComponent(search)}`;

    const tbody = document.getElementById('laporan-tbody');
    tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;padding:48px;color:var(--gray-400)">Memuat data...</td></tr>`;

    const res = await Api.get('/laporan' + qs);
    if (!res?.ok) return;

    const paginated = res.data.data;
    allData = paginated.data || [];
    renderTable(allData);
    renderPagination(paginated);
    document.getElementById('count-label').textContent = `Menampilkan ${allData.length} dari ${paginated.total} laporan`;
  }

  function renderTable(data) {
    const tbody = document.getElementById('laporan-tbody');
    if (data.length === 0) {
      tbody.innerHTML = `
        <tr><td colspan="8">
          <div class="empty-state">
            <div class="empty-icon">📭</div>
            <div class="empty-title">Tidak ada laporan ditemukan</div>
            <div class="empty-desc">Coba ubah filter pencarian</div>
          </div>
        </td></tr>`;
      return;
    }

    const canVerif = ['admin','koordinator'].includes(user.role);
    const canTL    = ['admin','koordinator','petugas'].includes(user.role);

    tbody.innerHTML = data.map(l => {
      const verifBtn = canVerif && l.status === 'menunggu'
        ? `<button class="action-btn success" title="Verifikasi" onclick="openVerif(${l.id})">✅</button>` : '';
      const tlBtn = canTL && ['diverifikasi','ditindaklanjuti'].includes(l.status)
        ? `<button class="action-btn" title="Tindak Lanjut" onclick="openTL(${l.id})" style="font-size:14px">🔄</button>` : '';

      return `<tr>
        <td><span class="ticket-code">${l.nomor_tiket}</span></td>
        <td><div class="nama-usaha-cell" title="${l.nama_usaha}">${l.nama_usaha}</div></td>
        <td><div class="petugas-cell">${l.sumber === 'mitra' ? (l.nama_mitra || '—') : (l.petugas?.name || '—')}</div></td>
        <td>${l.kecamatan}</td>
        <td style="font-size:12px">${Fmt.kendalaLabel(l.jenis_kendala)}</td>
        <td><span class="pill pill-${l.status}">${Fmt.statusLabel(l.status)}</span></td>
        <td class="meta-text">${Fmt.date(l.tanggal_laporan)}</td>
        <td>
          <div style="display:flex;gap:4px">
            <button class="action-btn" title="Detail" onclick="openDetail(${l.id})">👁️</button>
            ${verifBtn}${tlBtn}
          </div>
        </td>
      </tr>`;
    }).join('');
  }

  function renderPagination(p) {
    const btns = document.getElementById('pagination-btns');
    if (p.last_page <= 1) { btns.innerHTML = ''; return; }
    let html = '';
    if (p.current_page > 1) html += `<button class="btn btn-outline btn-xs" onclick="loadLaporan(${p.current_page-1})">← Prev</button>`;
    for (let i = Math.max(1, p.current_page-2); i <= Math.min(p.last_page, p.current_page+2); i++) {
      html += `<button class="btn btn-xs ${i===p.current_page?'btn-primary':'btn-outline'}" onclick="loadLaporan(${i})">${i}</button>`;
    }
    if (p.current_page < p.last_page) html += `<button class="btn btn-outline btn-xs" onclick="loadLaporan(${p.current_page+1})">Next →</button>`;
    btns.innerHTML = html;
  }

  async function openDetail(id) {
    document.getElementById('drawer-body').innerHTML = '<div style="text-align:center;padding:40px;color:var(--gray-400)">Memuat...</div>';
    document.getElementById('drawer-footer').innerHTML = '';
    document.getElementById('detail-drawer').classList.add('open');
    document.getElementById('drawer-overlay').classList.add('open');

    const res = await Api.get('/laporan/' + id);
    if (!res?.ok) { document.getElementById('drawer-body').innerHTML = 'Gagal memuat.'; return; }
    const l = res.data.data;

    document.getElementById('drawer-tiket').textContent = l.nomor_tiket;

    document.getElementById('drawer-body').innerHTML = `
      <div class="detail-section">
        <div class="detail-section-title">📋 Status Laporan</div>
        <div style="margin-bottom:12px"><span class="pill pill-${l.status}" style="font-size:13px;padding:5px 14px">${Fmt.statusLabel(l.status)}</span></div>
        ${l.arahan_tindak_lanjut ? `<div class="detail-row"><span class="detail-key">Arahan:</span><span class="detail-val">${Fmt.arahanLabel(l.arahan_tindak_lanjut)}</span></div>` : ''}
        ${l.catatan_admin ? `<div class="detail-row"><span class="detail-key">Catatan Admin:</span><span class="detail-val" style="font-style:italic">${l.catatan_admin}</span></div>` : ''}
      </div>

      <div class="detail-section">
        <div class="detail-section-title">🏢 Data Usaha</div>
        <div class="detail-row"><span class="detail-key">Nama Usaha</span><span class="detail-val">${l.nama_usaha}</span></div>
        ${l.nama_pemilik ? `<div class="detail-row"><span class="detail-key">Nama Pemilik</span><span class="detail-val">${l.nama_pemilik}</span></div>` : ''}
        <div class="detail-row"><span class="detail-key">Alamat</span><span class="detail-val">${l.alamat_usaha}</span></div>
        <div class="detail-row"><span class="detail-key">Kecamatan</span><span class="detail-val">${l.kecamatan}${l.desa_kelurahan ? ', ' + l.desa_kelurahan : ''}</span></div>
        ${l.latitude ? `<div class="detail-row"><span class="detail-key">Koordinat GPS</span><span class="detail-val" style="font-family:'JetBrains Mono',monospace;font-size:12px">${l.latitude}, ${l.longitude}</span></div>` : ''}
      </div>

      <div class="detail-section">
        <div class="detail-section-title">⚠️ Kendala</div>
        <div class="detail-row"><span class="detail-key">Jenis Kendala</span><span class="detail-val">${Fmt.kendalaLabel(l.jenis_kendala)}</span></div>
        <div style="background:var(--gray-50);border-radius:var(--radius-sm);padding:12px;font-size:13px;color:var(--gray-700);line-height:1.7;margin-top:8px">${l.kronologi}</div>
      </div>

      <div class="detail-section">
        <div class="detail-section-title">👤 ${l.sumber === 'mitra' ? 'Identitas Mitra' : 'Petugas'}</div>
        ${l.sumber === 'mitra' ? `
          <div class="detail-row"><span class="detail-key">Nama Mitra</span><span class="detail-val">${l.nama_mitra || '—'}</span></div>
          <div class="detail-row"><span class="detail-key">ID Mitra</span><span class="detail-val">${l.id_mitra || '—'}</span></div>
          <div class="detail-row"><span class="detail-key">No. HP</span><span class="detail-val">${l.phone_mitra || '—'}</span></div>
          <div class="detail-row"><span class="detail-key">Ketua Tim</span><span class="detail-val">${l.ketua_tim || '—'}</span></div>
        ` : `
          <div class="detail-row"><span class="detail-key">Nama</span><span class="detail-val">${l.petugas?.name || '—'}</span></div>
          <div class="detail-row"><span class="detail-key">NIP</span><span class="detail-val">${l.petugas?.nip || '—'}</span></div>
          <div class="detail-row"><span class="detail-key">Wilayah Tugas</span><span class="detail-val">${l.petugas?.wilayah_tugas || '—'}</span></div>
        `}
        <div class="detail-row"><span class="detail-key">Tanggal Lapor</span><span class="detail-val">${Fmt.datetime(l.tanggal_laporan)}</span></div>
      </div>

      ${l.foto?.length > 0 ? `
        <div class="detail-section">
          <div class="detail-section-title">📸 Foto Dokumentasi</div>
          <div style="display:flex;flex-wrap:wrap;gap:8px">
            ${l.foto.map(f=>`<img src="${f.url_foto}" style="width:90px;height:90px;object-fit:cover;border-radius:8px;border:2px solid var(--gray-200);cursor:pointer" onclick="window.open('${f.url_foto}')">`).join('')}
          </div>
        </div>` : ''}

      ${l.tindak_lanjut?.length > 0 ? `
        <div class="detail-section">
          <div class="detail-section-title">🔄 Riwayat Tindak Lanjut</div>
          ${l.tindak_lanjut.map(t=>`
            <div style="background:var(--gray-50);border-radius:var(--radius-sm);padding:12px;margin-bottom:8px;font-size:13px">
              <div style="display:flex;justify-content:space-between;margin-bottom:5px">
                <span class="pill pill-${t.hasil==='berhasil_didata'?'selesai':t.hasil==='tetap_menolak'?'ditutup':'ditindaklanjuti'}">${t.hasil==='berhasil_didata'?'Berhasil Didata':t.hasil==='tetap_menolak'?'Tetap Menolak':'Kunjungan Ulang'}</span>
                <span style="color:var(--gray-400)">${Fmt.date(t.tanggal_kunjungan)}</span>
              </div>
              <div style="color:var(--gray-700)">${t.keterangan}</div>
            </div>
          `).join('')}
        </div>` : ''}
    `;

    // Footer actions
    const canVerif = ['admin','koordinator'].includes(user.role) && l.status === 'menunggu';
    const canTL    = ['admin','koordinator','petugas'].includes(user.role) && ['diverifikasi','ditindaklanjuti'].includes(l.status);
    let footerHtml = `<button class="btn btn-outline" style="flex:1" onclick="closeDrawer()">Tutup</button>`;
    if (canVerif) footerHtml += `<button class="btn btn-primary" style="flex:1" onclick="closeDrawer();openVerif(${l.id})">✅ Verifikasi</button>`;
    if (canTL)    footerHtml += `<button class="btn btn-teal" style="flex:1" onclick="closeDrawer();openTL(${l.id})">🔄 Tindak Lanjut</button>`;
    document.getElementById('drawer-footer').innerHTML = footerHtml;
  }

  function closeDrawer() {
    document.getElementById('detail-drawer').classList.remove('open');
    document.getElementById('drawer-overlay').classList.remove('open');
  }

  function openVerif(id) {
    document.getElementById('verif-id').value = id;
    document.getElementById('inp-arahan').value = '';
    document.getElementById('inp-catatan').value = '';
    document.getElementById('wrap-ditugaskan').style.display = 'none';
    document.getElementById('info-taskforce').style.display = 'none';
    Modal.show('modal-verif');
  }

  async function onArahanChange() {
    const arahan = document.getElementById('inp-arahan').value;
    const wrapDitugaskan = document.getElementById('wrap-ditugaskan');
    const infoTaskforce  = document.getElementById('info-taskforce');
    const selDitugaskan  = document.getElementById('inp-ditugaskan');

    wrapDitugaskan.style.display = 'none';
    infoTaskforce.style.display  = 'none';
    selDitugaskan.innerHTML = '<option value="">-- Memuat... --</option>';

    if (arahan === 'ke_taskforce') {
      infoTaskforce.style.display = 'block';
    } else if (arahan === 'ke_pml' || arahan === 'ke_subject_matter') {
      wrapDitugaskan.style.display = 'block';
      const posisi = arahan === 'ke_pml' ? 'pml' : 'subject_matter';
      const res = await fetch(`/api/publik/petugas-posisi?posisi=${posisi}`);
      const data = await res.json();
      selDitugaskan.innerHTML = '<option value="">-- Pilih Petugas --</option>';
      (data.data || []).forEach(p => {
        const o = document.createElement('option');
        o.value = p.id;
        o.textContent = p.name + (p.wilayah_tugas ? ` — ${p.wilayah_tugas}` : '');
        selDitugaskan.appendChild(o);
      });
    }
  }
  window.onArahanChange = onArahanChange;

  async function submitVerifikasi() {
    const id      = document.getElementById('verif-id').value;
    const arahan  = document.getElementById('inp-arahan').value;
    const catatan = document.getElementById('inp-catatan').value;
    const ditugaskan = document.getElementById('inp-ditugaskan')?.value || null;

    if (!arahan) { Toast.show('Pilih arahan tindak lanjut.', 'error'); return; }
    if ((arahan === 'ke_pml' || arahan === 'ke_subject_matter') && !ditugaskan) {
      Toast.show('Pilih petugas yang ditugaskan.', 'error'); return;
    }

    const payload = { arahan_tindak_lanjut: arahan, catatan_admin: catatan };
    if (ditugaskan) payload.ditugaskan_ke = parseInt(ditugaskan);

    const res = await Api.patch(`/laporan/${id}/verifikasi`, payload);
    if (!res?.ok) { Toast.show(res?.data?.message || 'Gagal verifikasi.', 'error'); return; }

    Modal.hide('modal-verif');
    Toast.show('Laporan berhasil diverifikasi!', 'success');
    loadLaporan(currentPage);
  }

  function openTL(id) {
    document.getElementById('tl-id').value = id;
    document.getElementById('inp-hasil').value = '';
    document.getElementById('inp-ket').value = '';
    document.getElementById('inp-tgl-kunjungan').value = new Date().toISOString().slice(0,10);
    Modal.show('modal-tl');
  }

  async function submitTindakLanjut() {
    const id   = document.getElementById('tl-id').value;
    const hasil = document.getElementById('inp-hasil').value;
    const ket   = document.getElementById('inp-ket').value.trim();
    const tgl   = document.getElementById('inp-tgl-kunjungan').value;
    if (!hasil)        { Toast.show('Pilih hasil kunjungan.', 'error'); return; }
    if (ket.length<10) { Toast.show('Keterangan minimal 10 karakter.', 'error'); return; }
    if (!tgl)          { Toast.show('Isi tanggal kunjungan.', 'error'); return; }

    const res = await Api.post(`/laporan/${id}/tindak-lanjut`, { hasil, keterangan: ket, tanggal_kunjungan: tgl });
    if (!res?.ok) { Toast.show(res?.data?.message || 'Gagal simpan.', 'error'); return; }

    Modal.hide('modal-tl');
    Toast.show('Tindak lanjut berhasil dicatat!', 'success');
    loadLaporan(currentPage);
  }

  function resetFilter() {
    ['f-status','f-kecamatan','f-kendala'].forEach(id => document.getElementById(id).value='');
    document.getElementById('f-search').value='';
    loadLaporan();
  }

  async function exportCSV() {
      window.open('/api/laporan/ekspor?token=' + Auth.getToken(), '_blank');
  }

  // Search on Enter
  document.getElementById('f-search')?.addEventListener('keydown', e => {
    if (e.key === 'Enter') loadLaporan();
  });
  window.openDetail      = openDetail;
  window.closeDrawer     = closeDrawer;
  window.openVerif       = openVerif;
  window.submitVerifikasi = submitVerifikasi;
  window.openTL          = openTL;
  window.submitTindakLanjut = submitTindakLanjut;
  window.resetFilter     = resetFilter;
  window.exportCSV       = exportCSV;
  window.loadLaporan     = loadLaporan;
</script>
</body>
</html>
