<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Buat Laporan — Respon Blora</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<style>
  .form-card {
    background: white;
    border-radius: var(--radius);
    border: 1px solid var(--gray-100);
    box-shadow: var(--shadow-sm);
    max-width: 860px;
    overflow: hidden;
  }

  .form-card-header {
    padding: 24px 28px;
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-light) 100%);
    display: flex;
    align-items: center;
    gap: 16px;
  }

  .form-card-icon {
    width: 50px; height: 50px;
    background: rgba(255,255,255,0.12);
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 24px; flex-shrink: 0;
  }

  .form-card-title    { font-size: 18px; font-weight: 800; color: white; }
  .form-card-subtitle { font-size: 13px; color: rgba(255,255,255,0.55); margin-top: 3px; }

  .form-card-body { padding: 28px; }

  .form-section       { margin-bottom: 30px; }
  .form-section-label {
    font-size: 11.5px; font-weight: 700; color: var(--gray-500);
    text-transform: uppercase; letter-spacing: 0.09em;
    margin-bottom: 16px; padding-bottom: 10px;
    border-bottom: 1px solid var(--gray-100);
    display: flex; align-items: center; gap: 7px;
  }

  /* Kendala Radio Grid */
  .kendala-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 10px; }

  .kendala-opt input[type="radio"] { position: absolute; opacity: 0; width: 0; height: 0; }

  .kendala-box {
    display: flex; flex-direction: column; align-items: center; text-align: center;
    padding: 16px 10px;
    border: 2px solid var(--gray-200);
    border-radius: var(--radius-sm);
    cursor: pointer;
    transition: all var(--transition);
    background: var(--gray-50);
    gap: 8px;
    user-select: none;
  }

  .kendala-box:hover { border-color: var(--blue-light); background: var(--blue-pale); }

  .kendala-opt input:checked + .kendala-box {
    border-color: var(--blue); background: var(--blue-pale);
  }

  .kendala-box-icon { font-size: 24px; }
  .kendala-box-text { font-size: 12px; font-weight: 600; color: var(--gray-700); line-height: 1.3; }
  .kendala-opt input:checked + .kendala-box .kendala-box-text { color: var(--blue); }

  /* GPS */
  .gps-row {
    display: flex; align-items: center; gap: 10px;
    padding: 11px 14px;
    border: 1.5px solid var(--gray-200);
    border-radius: var(--radius-sm);
    font-size: 13px; color: var(--gray-600);
    background: var(--gray-50);
    transition: border-color var(--transition);
  }

  .gps-row.detected { border-color: var(--green); background: var(--green-pale); }

  .gps-indicator {
    width: 8px; height: 8px; border-radius: 50%;
    background: var(--gray-300); flex-shrink: 0;
  }
  .gps-indicator.on { background: var(--green); animation: pulse-dot 2s infinite; }

  /* Upload */
  .upload-zone {
    border: 2px dashed var(--gray-300);
    border-radius: var(--radius-sm);
    padding: 36px 24px;
    text-align: center;
    cursor: pointer;
    transition: all var(--transition);
    background: var(--gray-50);
  }

  .upload-zone:hover, .upload-zone.drag { border-color: var(--blue); background: var(--blue-pale); }

  .upload-icon { font-size: 36px; margin-bottom: 8px; }
  .upload-text { font-size: 14px; font-weight: 600; color: var(--gray-600); margin-bottom: 4px; }
  .upload-sub  { font-size: 12px; color: var(--gray-400); }

  .foto-grid { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 12px; }

  .foto-item {
    position: relative; width: 88px; height: 88px;
    border-radius: var(--radius-sm); overflow: hidden;
    border: 2px solid var(--gray-200);
  }

  .foto-item img { width: 100%; height: 100%; object-fit: cover; }

  .foto-remove {
    position: absolute; top: 4px; right: 4px;
    width: 20px; height: 20px;
    background: rgba(0,0,0,0.55); border: none;
    border-radius: 50%; color: white; font-size: 11px;
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    transition: background var(--transition);
  }

  .foto-remove:hover { background: var(--red); }

  .form-card-footer {
    padding: 20px 28px;
    border-top: 1px solid var(--gray-100);
    display: flex; gap: 12px; justify-content: flex-end;
    background: var(--gray-50);
  }

  .char-count { font-size: 11.5px; color: var(--gray-400); text-align: right; margin-top: 4px; }
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
          <h1>Buat Laporan Baru</h1>
          <p>Isi form lengkap untuk melaporkan kendala yang dihadapi di lapangan</p>
        </div>
        <a href="/laporan" class="btn btn-outline">← Kembali ke Daftar</a>
      </div>

      <div class="form-card anim-2">
        <div class="form-card-header">
          <div class="form-card-icon">📝</div>
          <div>
            <div class="form-card-title">Form Laporan Kendala Pendataan</div>
            <div class="form-card-subtitle">Semua field bertanda * wajib diisi dengan lengkap dan benar</div>
          </div>
        </div>

        <div class="form-card-body">

          <!-- SEKSI 1: IDENTITAS -->
          <div class="form-section">
            <div class="form-section-label">👤 Identitas Petugas</div>
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Nama Petugas</label>
                <input type="text" class="form-control" id="f-nama-petugas" readonly>
              </div>
              <div class="form-group">
                <label class="form-label">Wilayah Tugas</label>
                <input type="text" class="form-control" id="f-wilayah" readonly>
              </div>
            </div>
          </div>

          <!-- SEKSI 2: DATA RESPONDEN -->
          <div class="form-section">
            <div class="form-section-label">🏢 Data Usaha / Responden</div>
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Nama Usaha / Perusahaan <span class="req">*</span></label>
                <input type="text" class="form-control" id="f-nama-usaha" placeholder="Contoh: PT Maju Jaya, Toko Berkah">
              </div>
              <div class="form-group">
                <label class="form-label">Nama Pemilik / Penanggung Jawab</label>
                <input type="text" class="form-control" id="f-nama-pemilik" placeholder="Nama pemilik atau PJ">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Kecamatan <span class="req">*</span></label>
                <select class="form-control" id="f-kecamatan" onchange="updateDesa()">
                  <option value="">-- Pilih Kecamatan --</option>
                  <option>Blora</option><option>Cepu</option><option>Jepon</option>
                  <option>Randublatung</option><option>Kunduran</option><option>Ngawen</option>
                  <option>Bogorejo</option><option>Todanan</option><option>Japah</option>
                  <option>Banjarejo</option><option>Jati</option><option>Jiken</option>
                  <option>Kedungtuban</option><option>Kradenan</option><option>Sambong</option>
                  <option>Tunjungan</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Desa / Kelurahan</label>
                <select class="form-control" id="f-desa" disabled>
                  <option value="">-- Pilih kecamatan dulu --</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Alamat Lengkap Usaha <span class="req">*</span></label>
              <textarea class="form-control" id="f-alamat" rows="2" placeholder="Jl. Pemuda No. 12, RT 02 RW 03"></textarea>
            </div>
          </div>

          <!-- SEKSI 3: GPS -->
          <div class="form-section">
            <div class="form-section-label">📍 Lokasi GPS</div>
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Deteksi Lokasi Otomatis</label>
                <div class="gps-row" id="gps-row">
                  <div class="gps-indicator" id="gps-dot"></div>
                  <span id="gps-text" style="flex:1">Klik tombol untuk deteksi lokasi Anda saat ini</span>
                  <button type="button" class="btn btn-primary btn-xs" onclick="getGPS()">📍 Deteksi GPS</button>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Koordinat (isi otomatis dari GPS)</label>
                <div style="display:flex;gap:8px">
                  <input type="text" class="form-control" id="f-lat" placeholder="Latitude" readonly style="flex:1">
                  <input type="text" class="form-control" id="f-lng" placeholder="Longitude" readonly style="flex:1">
                </div>
              </div>
            </div>
          </div>

          <!-- SEKSI 4: KENDALA -->
          <div class="form-section">
            <div class="form-section-label">⚠️ Jenis Kendala <span style="color:var(--red);font-size:14px">*</span></div>
            <div class="kendala-grid">
              <label class="kendala-opt">
                <input type="radio" name="kendala" value="menolak_diwawancara">
                <div class="kendala-box">
                  <div class="kendala-box-icon">🚫</div>
                  <div class="kendala-box-text">Menolak Diwawancara</div>
                </div>
              </label>
              <label class="kendala-opt">
                <input type="radio" name="kendala" value="tidak_ditemui">
                <div class="kendala-box">
                  <div class="kendala-box-icon">🔍</div>
                  <div class="kendala-box-text">Tidak Ditemui</div>
                </div>
              </label>
              <label class="kendala-opt">
                <input type="radio" name="kendala" value="lainnya">
                <div class="kendala-box">
                  <div class="kendala-box-icon">📌</div>
                  <div class="kendala-box-text">Lainnya</div>
                </div>
              </label>
            </div>
            <div id="kendala-lainnya-wrap" style="margin-top:10px;display:none">
              <input type="text" class="form-control" id="f-lainnya" placeholder="Jelaskan jenis kendala lainnya...">
            </div>
          </div>

          <!-- SEKSI 5: KRONOLOGI -->
          <div class="form-section">
            <div class="form-section-label">📖 Kronologi Kejadian</div>
            <div class="form-group">
              <label class="form-label">Uraian singkat kronologi di lapangan <span class="req">*</span></label>
              <textarea class="form-control" id="f-kronologi" rows="5"
                placeholder="Ceritakan secara singkat apa yang terjadi saat kunjungan. Misalnya: Saat saya datang pukul 09.00 WIB, pemilik usaha menolak dengan alasan..."></textarea>
              <div class="char-count"><span id="krono-count">0</span> / 2000 karakter (min. 20)</div>
            </div>
          </div>

          <!-- SEKSI 6: FOTO -->
          <div class="form-section">
            <div class="form-section-label">📸 Foto Dokumentasi</div>
            <div class="upload-zone" id="upload-zone" onclick="document.getElementById('f-foto').click()">
              <div class="upload-icon">📷</div>
              <div class="upload-text">Klik atau seret foto ke sini</div>
              <div class="upload-sub">JPG, PNG, WebP • Maksimal 5MB per foto • Maksimal 5 foto</div>
            </div>
            <input type="file" id="f-foto" accept="image/*" multiple style="display:none" onchange="handleFoto(event)">
            <div class="foto-grid" id="foto-grid"></div>
          </div>

        </div><!-- /form-card-body -->

        <div class="form-card-footer">
          <button class="btn btn-outline" onclick="resetForm()">🗑️ Reset Form</button>
          <button class="btn btn-teal" id="btn-submit" onclick="submitLaporan()">📤 Kirim Laporan</button>
        </div>
      </div>

    </main>
  </div>
</div>

<!-- Success Modal -->
<div class="modal-overlay" id="modal-sukses">
  <div class="modal" style="text-align:center">
    <div class="modal-icon success">✅</div>
    <div class="modal-title">Laporan Berhasil Dikirim!</div>
    <div class="modal-desc">Laporan Anda sedang menunggu verifikasi admin. Simpan nomor tiket ini.</div>
    <div style="background:var(--navy);color:white;font-family:'JetBrains Mono',monospace;font-size:22px;font-weight:700;padding:18px;border-radius:var(--radius-sm);letter-spacing:0.05em;margin-bottom:22px" id="modal-tiket">—</div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="resetForm();Modal.hide('modal-sukses')">Buat Laporan Lagi</button>
      <a href="/laporan" class="btn btn-primary">Lihat Daftar Laporan →</a>
    </div>
  </div>
</div>

<script type="module">
  const user = requireAuth();
  let fotoFiles = [];

  if (user) {
    if (!['petugas','koordinator','admin'].includes(user.role)) {
      window.location.href = '/dashboard';
    }
    Sidebar.render('/form-laporan');
    buildTopbar('Buat Laporan Baru', 'Laporan → Buat Baru');
    loadNotifikasi();
    Modal.init('modal-sukses');

    document.getElementById('f-nama-petugas').value = user.name;
    document.getElementById('f-wilayah').value = user.wilayah_tugas || '';
  }

  // Kronologi counter
  document.getElementById('f-kronologi').addEventListener('input', function() {
    document.getElementById('krono-count').textContent = this.value.length;
    const cnt = document.getElementById('krono-count');
    cnt.style.color = this.value.length < 20 ? 'var(--red)' : 'var(--gray-400)';
  });

  // Kendala radio
  document.querySelectorAll('input[name="kendala"]').forEach(r => {
    r.addEventListener('change', () => {
      document.getElementById('kendala-lainnya-wrap').style.display =
        r.value === 'lainnya' ? 'block' : 'none';
    });
  });

  // Drag & drop
  const zone = document.getElementById('upload-zone');
  zone.addEventListener('dragover',  e => { e.preventDefault(); zone.classList.add('drag'); });
  zone.addEventListener('dragleave', () => zone.classList.remove('drag'));
  zone.addEventListener('drop', e => {
    e.preventDefault(); zone.classList.remove('drag');
    addFoto(Array.from(e.dataTransfer.files));
  });

  function handleFoto(e) { addFoto(Array.from(e.target.files)); }

  function addFoto(files) {
    const remaining = 5 - fotoFiles.length;
    const toAdd = files.filter(f => f.type.startsWith('image/')).slice(0, remaining);
    if (toAdd.length === 0) { Toast.show('Maksimal 5 foto.', 'warning'); return; }
    fotoFiles = [...fotoFiles, ...toAdd];
    renderFotoPreview();
  }

  function renderFotoPreview() {
    const grid = document.getElementById('foto-grid');
    grid.innerHTML = fotoFiles.map((f,i) => `
      <div class="foto-item">
        <img src="${URL.createObjectURL(f)}" alt="foto ${i+1}">
        <button class="foto-remove" onclick="removeFoto(${i})">✕</button>
      </div>
    `).join('');
  }

  function removeFoto(i) {
    fotoFiles.splice(i, 1);
    renderFotoPreview();
  }

  function getGPS() {
    const dot  = document.getElementById('gps-dot');
    const text = document.getElementById('gps-text');
    const row  = document.getElementById('gps-row');
    text.textContent = '⏳ Mendeteksi lokasi...';

    if (!navigator.geolocation) {
      text.textContent = '❌ GPS tidak didukung di browser ini.';
      return;
    }

    navigator.geolocation.getCurrentPosition(pos => {
      const lat = pos.coords.latitude.toFixed(6);
      const lng = pos.coords.longitude.toFixed(6);
      document.getElementById('f-lat').value = lat;
      document.getElementById('f-lng').value = lng;
      dot.classList.add('on');
      row.classList.add('detected');
      text.textContent = `✅ Lokasi terdeteksi: ${lat}, ${lng}`;
      Toast.show('Lokasi GPS berhasil dideteksi!', 'success');
    }, () => {
      text.textContent = '⚠️ Gagal deteksi GPS. Isi manual atau coba lagi.';
      Toast.show('Tidak dapat mengakses GPS. Pastikan izin lokasi diaktifkan.', 'warning');
    }, { timeout: 10000 });
  }

  function resetForm() {
    ['f-nama-usaha','f-nama-pemilik','f-alamat','f-desa','f-kronologi','f-lainnya','f-lat','f-lng']
      .forEach(id => { const el = document.getElementById(id); if(el) el.value = ''; });
    document.getElementById('f-kecamatan').value = '';
    document.querySelectorAll('input[name="kendala"]').forEach(r => r.checked = false);
    document.getElementById('kendala-lainnya-wrap').style.display = 'none';
    document.getElementById('krono-count').textContent = '0';
    fotoFiles = [];
    renderFotoPreview();
    const dot = document.getElementById('gps-dot');
    dot.classList.remove('on');
    document.getElementById('gps-row').classList.remove('detected');
    document.getElementById('gps-text').textContent = 'Klik tombol untuk deteksi lokasi Anda saat ini';
    Toast.show('Form direset.', 'info');
  }

  const DESA_MAP = {
    'Blora':        ['Bangkle','Beran','Jetis','Karangjati','Kauman','Kedungjenar','Mlangsen','Ngadirejo','Purwosari','Sendangharjo','Tempelan','Tempuran','Wulung'],
    'Cepu':         ['Balun','Cabean','Cepu','Jipang','Jumbreng','Kapuan','Kentong','Mernung','Ngroto','Padangan','Pilangsari','Pohlandak','Sumberpitu','Tambakromo','Tegalrejo'],
    'Jepon':        ['Balong','Bleboh','Blungun','Geneng','Gersi','Jepon','Jomblang','Kemiri','Kemirirejo','Ketringan','Klopodhuwur','Ngampel','Nglebur','Pelem','Soko','Turirejo'],
    'Randublatung': ['Bekutuk','Bodeh','Boyolayar','Genjor','Kalirejo','Karanggeneng','Kalisari','Kutukan','Laju','Medalem','Mojowetan','Ngliron','Nginggil','Pilang','Randublatung','Sumberejo','Tanggel','Temulus','Wulung'],
    'Kunduran':     ['Balong','Bejirejo','Botoreco','Brangkal','Gagakan','Gandu','Gedangdowo','Jagong','Karanganyar','Karanggeneng','Kemiri','Kunduran','Kuwik','Mangunrejo','Ngilen','Soko','Sonokidul','Sulursari','Toroh'],
    'Ngawen':       ['Bandungrejo','Bogowanti','Bradag','Gedangan','Kalangan','Keser','Kowangan','Kropak','Kumurejo','Ngawen','Sendangrejo','Sendangwungu','Sumberagung','Talokwohmojo','Trembulrejo'],
    'Bogorejo':     ['Bogorejo','Harjowinangun','Kalangan','Kembang','Petak','Pojok','Sidomulyo','Soko','Sukorejo','Tambahrejo','Tempurrejo','Turi','Watutumpeng'],
    'Todanan':      ['Bedingin','Cokrowati','Dalangan','Gunungan','Kembang','Ketileng','Ledok','Nglengkir','Pengkol','Sambongrejo','Sendang','Singonegoro','Sonorejo','Todanan','Tunjungan','Wulung'],
    'Japah':        ['Balongsari','Gabusan','Harjodowo','Japah','Jiworejo','Karanganyar','Karangsari','Kawengan','Nglandeyan','Sumberejo','Sumur','Tempellemahbang','Tinapan'],
    'Banjarejo':    ['Banjarejo','Botorejo','Gandu','Gempolrejo','Jatisari','Kalitengah','Karanganyar','Kedungringin','Pelemsengir','Sidomulyo','Sidomukti','Sumberejo','Turi','Turi Lor','Wonosemi'],
    'Jati':         ['Gempol','Jiwan','Jati','Karangrowo','Kembang','Mojorembun','Ngapus','Ngraho','Pelem','Plosorejo','Sumurboto','Sumberjo','Tambakselo','Tembang'],
    'Jiken':        ['Bleboh','Cabak','Geneng','Jiwan','Jiken','Ketringan','Ledok','Lembupurwo','Nglebur','Ngraho','Pegalongan','Prigelan','Soko','Sumber','Wulung'],
    'Kedungtuban':  ['Bajo','Bekutuk','Galuk','Gadon','Kemiri','Kedungtuban','Ngraho','Nglanjuk','Nglandeyan','Nguluhan','Ngrubuhan','Ngraho','Ronggolawe','Sumber','Sumberagung','Tanjung','Wado'],
    'Kradenan':     ['Bapangan','Crewek','Gabusan','Gondel','Grabagan','Kalinanas','Kalisari','Katur','Kradenan','Kuwu','Medalem','Mendenrejo','Mulyorejo','Pakis','Patalan','Sambongrejo','Sumbersoko','Tanjungrejo','Tawangrejo','Tinapan','Trembes','Wado'],
    'Sambong':      ['Biting','Giyanti','Gombang','Gunungsari','Kalinanas','Ledok','Leran','Lumbungmas','Mendenrejo','Ngraho','Sambong','Sambongrejo','Sumber','Sumberejo'],
    'Tunjungan':    ['Adirejo','Beran','Gempolrejo','Kalangan','Kedungrejo','Keser','Krangganharjo','Ngraho','Sambongrejo','Soko','Sukorejo','Tambahrejo','Tunjungan','Turi'],
  };

  function updateDesa() {
    const kec   = document.getElementById('f-kecamatan').value;
    const sel   = document.getElementById('f-desa');
    const desas = DESA_MAP[kec] || [];

    sel.innerHTML = '';
    if (!kec || desas.length === 0) {
      sel.innerHTML = '<option value="">-- Pilih kecamatan dulu --</option>';
      sel.disabled  = true;
      return;
    }

    sel.innerHTML = '<option value="">-- Pilih Desa/Kelurahan --</option>';
    desas.forEach(d => {
      const opt = document.createElement('option');
      opt.value = d; opt.textContent = d;
      sel.appendChild(opt);
    });
    sel.disabled = false;
  }

  window.updateDesa = updateDesa;

  async function submitLaporan() {
    const namaUsaha  = document.getElementById('f-nama-usaha').value.trim();
    const alamat     = document.getElementById('f-alamat').value.trim();
    const kecamatan  = document.getElementById('f-kecamatan').value;
    const kendala    = document.querySelector('input[name="kendala"]:checked')?.value;
    const lainnya    = document.getElementById('f-lainnya').value.trim();
    const kronologi  = document.getElementById('f-kronologi').value.trim();
    const lat        = document.getElementById('f-lat').value;
    const lng        = document.getElementById('f-lng').value;

    if (!namaUsaha)          { Toast.show('Nama usaha wajib diisi.',          'error'); return; }
    if (!alamat)             { Toast.show('Alamat wajib diisi.',              'error'); return; }
    if (!kecamatan)          { Toast.show('Pilih kecamatan.',                 'error'); return; }
    if (!kendala)            { Toast.show('Pilih jenis kendala.',             'error'); return; }
    if (kendala==='lainnya' && !lainnya) { Toast.show('Jelaskan kendala lainnya.', 'error'); return; }
    if (kronologi.length < 20) { Toast.show('Kronologi minimal 20 karakter.', 'error'); return; }

    const btn = document.getElementById('btn-submit');
    btn.textContent = '⏳ Mengirim...';
    btn.disabled = true;

    const fd = new FormData();
    fd.append('nama_usaha',   namaUsaha);
    fd.append('nama_pemilik', document.getElementById('f-nama-pemilik').value.trim());
    fd.append('alamat_usaha', alamat);
    fd.append('kecamatan',    kecamatan);
    fd.append('desa_kelurahan', document.getElementById('f-desa').value.trim());
    fd.append('jenis_kendala', kendala);
    if (kendala === 'lainnya') fd.append('jenis_kendala_lainnya', lainnya);
    fd.append('kronologi', kronologi);
    if (lat) fd.append('latitude', lat);
    if (lng) fd.append('longitude', lng);
    fotoFiles.forEach((f, i) => fd.append(`foto[${i}]`, f));

    const res = await Api.upload('/laporan', fd);

    btn.textContent = '📤 Kirim Laporan';
    btn.disabled = false;

    if (!res?.ok) {
      const msg = res?.data?.message || 'Gagal mengirim laporan.';
      Toast.show(msg, 'error');
      return;
    }

    document.getElementById('modal-tiket').textContent = res.data.data.nomor_tiket;
    Modal.show('modal-sukses');
  }
  window.getGPS      = getGPS;
  window.handleFoto  = handleFoto;
  window.removeFoto  = removeFoto;
  window.resetForm   = resetForm;
  window.submitLaporan = submitLaporan;
</script>
</body>
</html>
