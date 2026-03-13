<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>Laporan Kendala — Respon Blora</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="icon" type="image/x-icon" href="/favicon.ico">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --navy:       #0B1F3A;
    --navy-light: #1A3A6B;
    --blue:       #2563EB;
    --teal:       #0D9488;
    --green:      #059669;
    --red:        #EF4444;
    --yellow:     #F59E0B;
    --gray-50:    #F8FAFC;
    --gray-100:   #F1F5F9;
    --gray-200:   #E2E8F0;
    --gray-300:   #CBD5E1;
    --gray-400:   #94A3B8;
    --gray-500:   #64748B;
    --gray-600:   #475569;
    --gray-700:   #334155;
    --gray-800:   #1E293B;
    --gray-900:   #0F172A;
    --radius:     14px;
    --radius-sm:  8px;
    --shadow:     0 4px 24px rgba(0,0,0,0.08);
    --shadow-lg:  0 12px 48px rgba(0,0,0,0.12);
    --transition: 0.18s ease;
  }

  html { scroll-behavior: smooth; }

  body {
    font-family: 'Plus Jakarta Sans', sans-serif;
    background: var(--gray-50);
    color: var(--gray-800);
    min-height: 100vh;
  }

  /* ── Header ── */
  .pub-header {
    background: var(--navy);
    padding: 0 24px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: sticky;
    top: 0;
    z-index: 100;
    box-shadow: 0 2px 16px rgba(0,0,0,0.2);
  }

  .pub-brand {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .pub-brand-icon {
    width: 36px; height: 36px;
    background: linear-gradient(135deg, var(--blue), var(--teal));
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 800; color: white;
    letter-spacing: -0.5px;
  }

  .pub-brand-name { font-size: 14px; font-weight: 800; color: white; }
  .pub-brand-sub  { font-size: 11px; color: rgba(255,255,255,0.45); margin-top: 1px; }

  .pub-badge {
    font-size: 11px; font-weight: 700;
    color: rgba(255,255,255,0.6);
    background: rgba(255,255,255,0.07);
    border: 1px solid rgba(255,255,255,0.1);
    padding: 4px 12px;
    border-radius: 100px;
    letter-spacing: 0.05em;
  }

  /* ── Container ── */
  .pub-container {
    max-width: 760px;
    margin: 0 auto;
    padding: 32px 20px 60px;
  }

  /* ── Page Title ── */
  .pub-title-block {
    margin-bottom: 28px;
    animation: fadeUp 0.4s ease both;
  }

  .pub-title {
    font-size: 26px;
    font-weight: 800;
    color: var(--gray-900);
    letter-spacing: -0.02em;
    margin-bottom: 6px;
  }

  .pub-subtitle {
    font-size: 14px;
    color: var(--gray-500);
    line-height: 1.6;
  }

  /* ── Info Banner ── */
  .info-banner {
    background: linear-gradient(135deg, rgba(37,99,235,0.06), rgba(13,148,136,0.06));
    border: 1px solid rgba(37,99,235,0.15);
    border-radius: var(--radius-sm);
    padding: 14px 16px;
    display: flex;
    gap: 12px;
    align-items: flex-start;
    margin-bottom: 24px;
    font-size: 13px;
    color: var(--gray-600);
    line-height: 1.6;
    animation: fadeUp 0.4s 0.05s ease both;
  }

  .info-banner-icon { font-size: 18px; flex-shrink: 0; margin-top: 1px; }

  /* ── Form Card ── */
  .form-card {
    background: white;
    border-radius: var(--radius);
    border: 1px solid var(--gray-200);
    box-shadow: var(--shadow);
    overflow: hidden;
    animation: fadeUp 0.4s 0.1s ease both;
  }

  .form-section {
    padding: 24px 28px;
    border-bottom: 1px solid var(--gray-100);
  }

  .form-section:last-child { border-bottom: none; }

  .section-label {
    font-size: 11px;
    font-weight: 800;
    color: var(--gray-400);
    text-transform: uppercase;
    letter-spacing: 0.1em;
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    gap: 7px;
  }

  .section-label::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--gray-100);
  }

  /* ── Form Elements ── */
  .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
  .form-row.triple { grid-template-columns: 1fr 1fr 1fr; }

  .form-group { margin-bottom: 16px; }
  .form-group:last-child { margin-bottom: 0; }

  .form-label {
    display: block;
    font-size: 12.5px;
    font-weight: 700;
    color: var(--gray-700);
    margin-bottom: 6px;
    letter-spacing: 0.01em;
  }

  .req { color: var(--red); margin-left: 2px; }

  .form-control {
    width: 100%;
    padding: 10px 13px;
    border: 1.5px solid var(--gray-200);
    border-radius: var(--radius-sm);
    font-family: inherit;
    font-size: 14px;
    color: var(--gray-800);
    background: white;
    transition: border-color var(--transition), box-shadow var(--transition);
    outline: none;
    -webkit-appearance: none;
  }

  .form-control:focus {
    border-color: var(--blue);
    box-shadow: 0 0 0 3px rgba(37,99,235,0.08);
  }

  .form-control::placeholder { color: var(--gray-400); }
  .form-control:disabled { background: var(--gray-100); color: var(--gray-500); cursor: not-allowed; }

  textarea.form-control { resize: vertical; min-height: 90px; line-height: 1.6; }

  .form-hint {
    font-size: 11.5px;
    color: var(--gray-400);
    margin-top: 5px;
    line-height: 1.5;
  }

  .form-hint.error { color: var(--red); }

  /* ── Kendala Grid ── */
  .kendala-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
  }

  .kendala-opt { position: relative; }
  .kendala-opt input[type="radio"] { position: absolute; opacity: 0; width: 0; height: 0; }

  .kendala-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 14px 8px;
    border: 2px solid var(--gray-200);
    border-radius: var(--radius-sm);
    cursor: pointer;
    transition: all var(--transition);
    background: var(--gray-50);
    gap: 7px;
    user-select: none;
  }

  .kendala-box:hover { border-color: var(--blue); background: rgba(37,99,235,0.04); }
  .kendala-opt input:checked + .kendala-box {
    border-color: var(--blue);
    background: rgba(37,99,235,0.06);
  }

  .kendala-box-icon { font-size: 22px; }
  .kendala-box-text { font-size: 11.5px; font-weight: 600; color: var(--gray-600); line-height: 1.3; }
  .kendala-opt input:checked + .kendala-box .kendala-box-text { color: var(--blue); }

  /* ── GPS ── */
  .gps-row {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 13px;
    border: 1.5px solid var(--gray-200);
    border-radius: var(--radius-sm);
    font-size: 13px; color: var(--gray-600);
    background: var(--gray-50);
    transition: border-color var(--transition);
  }
  .gps-row.detected { border-color: var(--green); background: rgba(5,150,105,0.05); }
  .gps-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--gray-300); flex-shrink: 0; }
  .gps-dot.on { background: var(--green); animation: pulse 2s infinite; }

  /* ── Upload ── */
  .upload-zone {
    border: 2px dashed var(--gray-300);
    border-radius: var(--radius-sm);
    padding: 28px;
    text-align: center;
    cursor: pointer;
    transition: all var(--transition);
    background: var(--gray-50);
  }
  .upload-zone:hover, .upload-zone.drag { border-color: var(--blue); background: rgba(37,99,235,0.04); }

  .foto-grid { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 12px; }
  .foto-item { position: relative; width: 80px; height: 80px; border-radius: var(--radius-sm); overflow: hidden; border: 2px solid var(--gray-200); }
  .foto-item img { width: 100%; height: 100%; object-fit: cover; }
  .foto-remove {
    position: absolute; top: 3px; right: 3px;
    width: 18px; height: 18px;
    background: rgba(0,0,0,0.6); border: none; border-radius: 50%;
    color: white; font-size: 10px; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
  }
  .foto-remove:hover { background: var(--red); }

  /* ── Footer & Submit ── */
  .form-footer {
    padding: 20px 28px;
    background: var(--gray-50);
    border-top: 1px solid var(--gray-100);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
  }

  .form-footer-note { font-size: 12px; color: var(--gray-400); line-height: 1.5; }

  .btn {
    display: inline-flex; align-items: center; justify-content: center;
    gap: 6px;
    padding: 10px 22px;
    border-radius: var(--radius-sm);
    font-family: inherit;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    border: none;
    transition: all var(--transition);
    text-decoration: none;
    white-space: nowrap;
  }

  .btn-primary {
    background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
    color: white;
    box-shadow: 0 4px 16px rgba(37,99,235,0.25);
  }
  .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(37,99,235,0.35); }
  .btn-primary:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
  .btn-outline { background: white; color: var(--gray-600); border: 1.5px solid var(--gray-200); }
  .btn-outline:hover { border-color: var(--gray-400); color: var(--gray-800); }

  /* ── Char Counter ── */
  .char-count { font-size: 11.5px; color: var(--gray-400); text-align: right; margin-top: 4px; }

  /* ── Rate limit warning ── */
  .rate-warn {
    display: none;
    background: rgba(239,68,68,0.08);
    border: 1px solid rgba(239,68,68,0.2);
    border-radius: var(--radius-sm);
    padding: 12px 16px;
    font-size: 13px;
    color: #B91C1C;
    margin-bottom: 16px;
  }
  .rate-warn.show { display: block; }

  /* ── Success State ── */
  .success-screen {
    display: none;
    text-align: center;
    padding: 60px 32px;
    animation: fadeUp 0.4s ease both;
  }
  .success-screen.show { display: block; }
  .success-icon { font-size: 64px; margin-bottom: 20px; }
  .success-title { font-size: 22px; font-weight: 800; color: var(--gray-900); margin-bottom: 8px; }
  .success-desc { font-size: 14px; color: var(--gray-500); margin-bottom: 24px; line-height: 1.7; }
  .tiket-box {
    display: inline-block;
    background: var(--navy);
    color: white;
    font-family: 'JetBrains Mono', monospace;
    font-size: 20px;
    font-weight: 700;
    padding: 16px 32px;
    border-radius: var(--radius-sm);
    letter-spacing: 0.05em;
    margin-bottom: 28px;
  }

  /* ── Toast ── */
  #toast-pub {
    position: fixed;
    bottom: 24px; right: 24px;
    z-index: 9999;
    display: flex; flex-direction: column; gap: 8px;
    pointer-events: none;
  }
  .toast-item {
    padding: 12px 16px;
    border-radius: var(--radius-sm);
    font-size: 13px; font-weight: 600;
    box-shadow: var(--shadow-lg);
    display: flex; align-items: center; gap: 8px;
    opacity: 0; transform: translateY(8px);
    transition: all 0.25s ease;
    pointer-events: auto;
    max-width: 320px;
  }
  .toast-item.show { opacity: 1; transform: translateY(0); }
  .toast-item.success { background: #F0FDF4; color: #166534; border: 1px solid #BBF7D0; }
  .toast-item.error   { background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; }
  .toast-item.warning { background: #FFFBEB; color: #92400E; border: 1px solid #FDE68A; }

  /* ── Animations ── */
  @keyframes fadeUp {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
  }
  @keyframes pulse {
    0%, 100% { opacity: 1; }
    50%       { opacity: 0.4; }
  }

  /* ── Responsive ── */
  @media (max-width: 600px) {
    .form-row, .form-row.triple { grid-template-columns: 1fr; }
    .kendala-grid { grid-template-columns: repeat(2, 1fr); }
    .form-section { padding: 18px; }
    .pub-container { padding: 20px 14px 48px; }
    .form-footer { flex-direction: column; align-items: stretch; }
    .form-footer-note { text-align: center; }
    .btn { width: 100%; }
  }
</style>
</head>
<body>

<!-- Header -->
<header class="pub-header">
  <div class="pub-brand">
    <div class="pub-brand-icon">RB</div>
    <div>
      <div class="pub-brand-name">Respon Blora</div>
      <div class="pub-brand-sub">BPS Kabupaten Blora</div>
    </div>
  </div>
  <div class="pub-badge">Form Laporan Mitra</div>
</header>

<div class="pub-container">

  <!-- Title -->
  <div class="pub-title-block">
    <div class="pub-title">📋 Laporan Kendala Pendataan</div>
    <div class="pub-subtitle">Gunakan form ini untuk melaporkan kendala yang Anda hadapi saat melakukan pendataan di lapangan.</div>
  </div>

  <!-- Info Banner -->
  <div class="info-banner">
    <div class="info-banner-icon">ℹ️</div>
    <div>Data yang Anda kirimkan akan diterima langsung oleh tim BPS Kabupaten Blora untuk ditindaklanjuti. Pastikan semua informasi yang diisi sudah benar dan lengkap. Field bertanda <strong style="color:var(--red)">*</strong> wajib diisi.</div>
  </div>

  <!-- Rate limit warning -->
  <div class="rate-warn" id="rate-warn">
    ⚠️ Terlalu banyak percobaan. Silakan tunggu beberapa menit sebelum mengirim laporan lagi.
  </div>

  <!-- Form -->
  <div class="form-card" id="form-card">

    <!-- SEKSI 1: IDENTITAS MITRA -->
    <div class="form-section">
      <div class="section-label">👤 Identitas Mitra</div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Nama Lengkap <span class="req">*</span></label>
          <input type="text" class="form-control" id="f-nama" placeholder="Nama lengkap Anda"
            maxlength="100" autocomplete="name">
        </div>
        <div class="form-group">
          <label class="form-label">ID Mitra <span class="req">*</span></label>
          <input type="text" class="form-control" id="f-id-mitra" placeholder="Contoh: MTR-2024-001"
            maxlength="50">
          <div class="form-hint">ID mitra yang diberikan oleh BPS</div>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Nomor Handphone <span class="req">*</span></label>
          <input type="tel" class="form-control" id="f-phone" placeholder="08xxxxxxxxxx"
            maxlength="15" autocomplete="tel">
        </div>
        <div class="form-group">
          <label class="form-label">Nama Ketua Tim <span class="req">*</span></label>
          <select class="form-control" id="f-ketua-tim">
            <option value="">-- Pilih Ketua Tim --</option>
          </select>
          <div class="form-hint">Nama Subject Matter atau PML yang bertanggung jawab</div>
        </div>
      </div>
    </div>

    <!-- SEKSI 2: DATA RESPONDEN -->
    <div class="form-section">
      <div class="section-label">🏢 Data Usaha / Responden</div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Nama Usaha / Perusahaan <span class="req">*</span></label>
          <input type="text" class="form-control" id="f-nama-usaha" placeholder="PT Maju Jaya, Toko Berkah..." maxlength="200">
        </div>
        <div class="form-group">
          <label class="form-label">Nama Pemilik / Penanggung Jawab</label>
          <input type="text" class="form-control" id="f-nama-pemilik" placeholder="Nama pemilik atau PJ" maxlength="100">
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
        <textarea class="form-control" id="f-alamat" rows="2"
          placeholder="Jl. Pemuda No. 12, RT 02 RW 03..." maxlength="500"></textarea>
      </div>
    </div>

    <!-- SEKSI 3: GPS -->
    <div class="form-section">
      <div class="section-label">📍 Lokasi GPS <span style="font-size:10px;color:var(--gray-400);font-weight:500;text-transform:none;letter-spacing:0">(opsional)</span></div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Deteksi Lokasi Otomatis</label>
          <div class="gps-row" id="gps-row">
            <div class="gps-dot" id="gps-dot"></div>
            <span id="gps-text" style="flex:1;font-size:13px">Klik untuk deteksi lokasi</span>
            <button type="button" class="btn btn-outline" style="padding:6px 12px;font-size:12px" onclick="getGPS()">📍 Deteksi</button>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Koordinat</label>
          <div style="display:flex;gap:8px">
            <input type="text" class="form-control" id="f-lat" placeholder="Latitude" readonly>
            <input type="text" class="form-control" id="f-lng" placeholder="Longitude" readonly>
          </div>
        </div>
      </div>
    </div>

    <!-- SEKSI 4: KENDALA -->
    <div class="form-section">
      <div class="section-label">⚠️ Jenis Kendala <span style="color:var(--red)">*</span></div>
      <div class="kendala-grid">
        <label class="kendala-opt">
          <input type="radio" name="kendala" value="menolak_diwawancara">
          <div class="kendala-box"><div class="kendala-box-icon">🚫</div><div class="kendala-box-text">Menolak Diwawancara</div></div>
        </label>
        <label class="kendala-opt">
          <input type="radio" name="kendala" value="tidak_ditemui">
          <div class="kendala-box"><div class="kendala-box-icon">🔍</div><div class="kendala-box-text">Tidak Ditemui</div></div>
        </label>
        <label class="kendala-opt">
          <input type="radio" name="kendala" value="lainnya">
          <div class="kendala-box"><div class="kendala-box-icon">📌</div><div class="kendala-box-text">Lainnya</div></div>
        </label>
      </div>
      <div id="lainnya-wrap" style="margin-top:10px;display:none">
        <input type="text" class="form-control" id="f-lainnya" placeholder="Jelaskan jenis kendala..." maxlength="200">
      </div>
    </div>

    <!-- SEKSI 5: KRONOLOGI -->
    <div class="form-section">
      <div class="section-label">📖 Kronologi Kejadian</div>
      <div class="form-group">
        <label class="form-label">Uraian kronologi di lapangan <span class="req">*</span></label>
        <textarea class="form-control" id="f-kronologi" rows="5"
          placeholder="Ceritakan secara singkat apa yang terjadi. Misalnya: Saat saya datang pukul 09.00 WIB, pemilik usaha menolak dengan alasan..."
          maxlength="2000"></textarea>
        <div class="char-count"><span id="krono-count">0</span> / 2000 karakter (min. 20)</div>
      </div>
    </div>

    <!-- SEKSI 6: FOTO -->
    <div class="form-section">
      <div class="section-label">📸 Foto Dokumentasi <span style="font-size:10px;color:var(--gray-400);font-weight:500;text-transform:none;letter-spacing:0">(opsional, maks 5 foto)</span></div>
      <div class="upload-zone" id="upload-zone" onclick="document.getElementById('f-foto').click()">
        <div style="font-size:32px;margin-bottom:8px">📷</div>
        <div style="font-size:13px;font-weight:600;color:var(--gray-600);margin-bottom:4px">Klik atau seret foto ke sini</div>
        <div style="font-size:12px;color:var(--gray-400)">JPG, PNG, WebP • Maks 5MB per foto • Maks 5 foto</div>
      </div>
      <input type="file" id="f-foto" accept="image/jpeg,image/png,image/webp" multiple style="display:none" onchange="handleFoto(event)">
      <div class="foto-grid" id="foto-grid"></div>
    </div>

    <!-- Footer -->
    <div class="form-footer">
      <div class="form-footer-note">
        🔒 Data Anda dilindungi dan hanya digunakan untuk keperluan pendataan BPS.
      </div>
      <div style="display:flex;gap:10px">
        <button type="button" class="btn btn-outline" onclick="resetForm()">🗑️ Reset</button>
        <button type="button" class="btn btn-primary" id="btn-submit" onclick="submitLaporan()">
          📤 Kirim Laporan
        </button>
      </div>
    </div>

  </div><!-- /form-card -->

  <!-- Success Screen -->
  <div class="success-screen" id="success-screen">
    <div class="success-icon">✅</div>
    <div class="success-title">Laporan Berhasil Dikirim!</div>
    <div class="success-desc">
      Laporan Anda sedang menunggu verifikasi dari tim BPS Kabupaten Blora.<br>
      Simpan nomor tiket berikut untuk memantau status laporan Anda.
    </div>
    <div class="tiket-box" id="tiket-display">—</div>
    <br>
    <button class="btn btn-outline" onclick="kirimLagi()">📝 Kirim Laporan Lagi</button>
  </div>

</div><!-- /container -->

<div id="toast-pub"></div>

<script>
(function() {
  'use strict';

  // ── CSRF Token ──
  const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

  // ── Client-side Rate Limiter ──
  // Maksimal 3 submit per 10 menit per session
  const RL_KEY   = 'rb_mitra_rl';
  const RL_MAX   = 3;
  const RL_WINDOW = 10 * 60 * 1000; // 10 menit

  function getRLData() {
    try { return JSON.parse(sessionStorage.getItem(RL_KEY)) || { count: 0, resetAt: Date.now() + RL_WINDOW }; }
    catch { return { count: 0, resetAt: Date.now() + RL_WINDOW }; }
  }

  function isRateLimited() {
    const d = getRLData();
    if (Date.now() > d.resetAt) {
      sessionStorage.setItem(RL_KEY, JSON.stringify({ count: 0, resetAt: Date.now() + RL_WINDOW }));
      return false;
    }
    return d.count >= RL_MAX;
  }

  function incrementRL() {
    const d = getRLData();
    d.count++;
    sessionStorage.setItem(RL_KEY, JSON.stringify(d));
  }

  // ── Toast ──
  function toast(msg, type = 'error') {
    const container = document.getElementById('toast-pub');
    const el = document.createElement('div');
    el.className = `toast-item ${type}`;
    const icons = { success: '✅', error: '❌', warning: '⚠️' };
    el.innerHTML = `<span>${icons[type]||'•'}</span><span>${msg}</span>`;
    container.appendChild(el);
    requestAnimationFrame(() => el.classList.add('show'));
    setTimeout(() => {
      el.classList.remove('show');
      setTimeout(() => el.remove(), 300);
    }, 4000);
  }

  // ── Sanitize input ──
  function sanitize(str) {
    if (typeof str !== 'string') return '';
    return str
      .trim()
      .replace(/[<>]/g, '') // strip HTML tags
      .substring(0, 2000);  // hard cap
  }

  function sanitizeShort(str, max = 200) {
    return sanitize(str).substring(0, max);
  }

  // ── Validate phone ──
  function isValidPhone(phone) {
    return /^(\+62|62|0)[0-9]{8,13}$/.test(phone.replace(/\s|-/g, ''));
  }

  // ── Desa Map ──
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
    'Kedungtuban':  ['Bajo','Bekutuk','Galuk','Gadon','Kemiri','Kedungtuban','Ngraho','Nglanjuk','Nglandeyan','Nguluhan','Ngrubuhan','Ronggolawe','Sumber','Sumberagung','Tanjung','Wado'],
    'Kradenan':     ['Bapangan','Crewek','Gabusan','Gondel','Grabagan','Kalinanas','Kalisari','Katur','Kradenan','Kuwu','Medalem','Mendenrejo','Mulyorejo','Pakis','Patalan','Sambongrejo','Sumbersoko','Tanjungrejo','Tawangrejo','Tinapan','Trembes','Wado'],
    'Sambong':      ['Biting','Giyanti','Gombang','Gunungsari','Kalinanas','Ledok','Leran','Lumbungmas','Mendenrejo','Ngraho','Sambong','Sambongrejo','Sumber','Sumberejo'],
    'Tunjungan':    ['Adirejo','Beran','Gempolrejo','Kalangan','Kedungrejo','Keser','Krangganharjo','Ngraho','Sambongrejo','Soko','Sukorejo','Tambahrejo','Tunjungan','Turi'],
  };

  // Load daftar ketua tim (PML & Subject Matter) dari API
  async function loadKetuaTim() {
    try {
      const res = await fetch('/api/publik/ketua-tim');
      if (!res.ok) return;
      const data = await res.json();
      const sel = document.getElementById('f-ketua-tim');
      (data.data || []).forEach(p => {
        const o = document.createElement('option');
        o.value = p.name;
        //o.textContent = p.name + (p.posisi ? ' (' + (p.posisi === 'pml' ? 'PML' : 'Subject Matter') + ')' : '');
        o.textContent = p.name;
        sel.appendChild(o);
      });
    } catch(e) {}
  }
  loadKetuaTim();

  window.updateDesa = function() {
    const kec  = document.getElementById('f-kecamatan').value;
    const sel  = document.getElementById('f-desa');
    const list = DESA_MAP[kec] || [];
    sel.innerHTML = '';
    if (!kec || !list.length) {
      sel.innerHTML = '<option value="">-- Pilih kecamatan dulu --</option>';
      sel.disabled = true; return;
    }
    sel.innerHTML = '<option value="">-- Pilih Desa/Kelurahan --</option>';
    list.forEach(d => {
      const o = document.createElement('option');
      o.value = d; o.textContent = d;
      sel.appendChild(o);
    });
    sel.disabled = false;
  };

  // ── GPS ──
  window.getGPS = function() {
    const dot  = document.getElementById('gps-dot');
    const text = document.getElementById('gps-text');
    const row  = document.getElementById('gps-row');
    text.textContent = '⏳ Mendeteksi...';
    if (!navigator.geolocation) { text.textContent = '❌ GPS tidak didukung'; return; }
    navigator.geolocation.getCurrentPosition(pos => {
      const lat = pos.coords.latitude.toFixed(6);
      const lng = pos.coords.longitude.toFixed(6);
      document.getElementById('f-lat').value = lat;
      document.getElementById('f-lng').value = lng;
      dot.classList.add('on');
      row.classList.add('detected');
      text.textContent = `✅ ${lat}, ${lng}`;
    }, () => {
      text.textContent = '⚠️ Gagal deteksi. Coba lagi.';
    }, { timeout: 10000 });
  };

  // ── Foto ──
  let fotoFiles = [];
  const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
  const MAX_SIZE = 5 * 1024 * 1024; // 5MB

  window.handleFoto = function(e) { addFoto(Array.from(e.target.files)); };

  function addFoto(files) {
    const valid = files.filter(f => {
      if (!ALLOWED_TYPES.includes(f.type)) { toast(`${f.name}: tipe file tidak didukung.`, 'warning'); return false; }
      if (f.size > MAX_SIZE) { toast(`${f.name}: ukuran melebihi 5MB.`, 'warning'); return false; }
      return true;
    });
    const remaining = 5 - fotoFiles.length;
    if (remaining <= 0) { toast('Maksimal 5 foto.', 'warning'); return; }
    fotoFiles = [...fotoFiles, ...valid.slice(0, remaining)];
    renderFoto();
  }

  function renderFoto() {
    document.getElementById('foto-grid').innerHTML = fotoFiles.map((f, i) => `
      <div class="foto-item">
        <img src="${URL.createObjectURL(f)}" alt="foto ${i+1}">
        <button class="foto-remove" onclick="removeFoto(${i})">✕</button>
      </div>`).join('');
  }

  window.removeFoto = function(i) { fotoFiles.splice(i, 1); renderFoto(); };

  // ── Drag & drop ──
  const zone = document.getElementById('upload-zone');
  zone.addEventListener('dragover',  e => { e.preventDefault(); zone.classList.add('drag'); });
  zone.addEventListener('dragleave', () => zone.classList.remove('drag'));
  zone.addEventListener('drop', e => {
    e.preventDefault(); zone.classList.remove('drag');
    addFoto(Array.from(e.dataTransfer.files));
  });

  // ── Kronologi counter ──
  document.getElementById('f-kronologi').addEventListener('input', function() {
    const cnt = document.getElementById('krono-count');
    cnt.textContent = this.value.length;
    cnt.style.color = this.value.length < 20 ? 'var(--red)' : 'var(--gray-400)';
  });

  // ── Kendala radio ──
  document.querySelectorAll('input[name="kendala"]').forEach(r => {
    r.addEventListener('change', () => {
      document.getElementById('lainnya-wrap').style.display =
        r.value === 'lainnya' ? 'block' : 'none';
    });
  });

  // ── Reset ──
  window.resetForm = function() {
    ['f-nama','f-id-mitra','f-phone','f-ketua-tim',
     'f-nama-usaha','f-nama-pemilik','f-alamat',
     'f-kronologi','f-lainnya','f-lat','f-lng']
      .forEach(id => { const el = document.getElementById(id); if(el) el.value = ''; });
    document.getElementById('f-kecamatan').value = '';
    document.getElementById('f-desa').innerHTML = '<option value="">-- Pilih kecamatan dulu --</option>';
    document.getElementById('f-desa').disabled = true;
    document.querySelectorAll('input[name="kendala"]').forEach(r => r.checked = false);
    document.getElementById('lainnya-wrap').style.display = 'none';
    document.getElementById('krono-count').textContent = '0';
    document.getElementById('gps-dot').classList.remove('on');
    document.getElementById('gps-row').classList.remove('detected');
    document.getElementById('gps-text').textContent = 'Klik untuk deteksi lokasi';
    fotoFiles = []; renderFoto();
    document.getElementById('f-foto').value = '';
  };

  window.kirimLagi = function() {
    document.getElementById('success-screen').classList.remove('show');
    document.getElementById('form-card').style.display = '';
    window.resetForm();
    window.scrollTo(0, 0);
  };

  // ── Submit ──
  window.submitLaporan = async function() {

    // Client-side rate limit check
    if (isRateLimited()) {
      document.getElementById('rate-warn').classList.add('show');
      window.scrollTo(0, 0);
      return;
    }

    // Ambil & sanitasi nilai
    const nama      = sanitizeShort(document.getElementById('f-nama').value, 100);
    const idMitra   = sanitizeShort(document.getElementById('f-id-mitra').value, 50);
    const phone     = sanitizeShort(document.getElementById('f-phone').value, 15);
    const ketuaTim  = sanitizeShort(document.getElementById('f-ketua-tim').value, 100);
    const namaUsaha = sanitizeShort(document.getElementById('f-nama-usaha').value, 200);
    const namaPemilik = sanitizeShort(document.getElementById('f-nama-pemilik').value, 100);
    const kecamatan = document.getElementById('f-kecamatan').value;
    const desa      = document.getElementById('f-desa').value;
    const alamat    = sanitize(document.getElementById('f-alamat').value).substring(0, 500);
    const kendala   = document.querySelector('input[name="kendala"]:checked')?.value || '';
    const lainnya   = sanitizeShort(document.getElementById('f-lainnya').value, 200);
    const kronologi = sanitize(document.getElementById('f-kronologi').value).substring(0, 2000);
    const lat       = document.getElementById('f-lat').value;
    const lng       = document.getElementById('f-lng').value;

    // Validasi
    const VALID_KECAMATAN = Object.keys(DESA_MAP);
    const VALID_KENDALA   = ['menolak_diwawancara','tidak_ditemui','lainnya'];

    if (!nama)                              { toast('Nama lengkap wajib diisi.'); return; }
    if (!idMitra)                           { toast('ID Mitra wajib diisi.'); return; }
    if (!phone || !isValidPhone(phone))     { toast('Nomor HP tidak valid.'); return; }
    if (!ketuaTim)                          { toast('Nama ketua tim wajib diisi.'); return; }
    if (!namaUsaha)                         { toast('Nama usaha wajib diisi.'); return; }
    if (!kecamatan || !VALID_KECAMATAN.includes(kecamatan)) { toast('Pilih kecamatan yang valid.'); return; }
    if (!alamat)                            { toast('Alamat wajib diisi.'); return; }
    if (!kendala || !VALID_KENDALA.includes(kendala)) { toast('Pilih jenis kendala.'); return; }
    if (kendala === 'lainnya' && !lainnya)  { toast('Jelaskan kendala lainnya.'); return; }
    if (kronologi.length < 20)              { toast('Kronologi minimal 20 karakter.'); return; }

    // Validasi lat/lng kalau ada
    if (lat && (isNaN(parseFloat(lat)) || parseFloat(lat) < -90 || parseFloat(lat) > 90)) {
      toast('Koordinat latitude tidak valid.'); return;
    }
    if (lng && (isNaN(parseFloat(lng)) || parseFloat(lng) < -180 || parseFloat(lng) > 180)) {
      toast('Koordinat longitude tidak valid.'); return;
    }

    const btn = document.getElementById('btn-submit');
    btn.textContent = '⏳ Mengirim...';
    btn.disabled = true;

    const fd = new FormData();
    fd.append('nama_mitra',     nama);
    fd.append('id_mitra',       idMitra);
    fd.append('phone_mitra',    phone);
    fd.append('ketua_tim',      ketuaTim);
    fd.append('nama_usaha',     namaUsaha);
    fd.append('nama_pemilik',   namaPemilik);
    fd.append('kecamatan',      kecamatan);
    fd.append('desa_kelurahan', desa);
    fd.append('alamat_usaha',   alamat);
    fd.append('jenis_kendala',  kendala);
    if (kendala === 'lainnya') fd.append('jenis_kendala_lainnya', lainnya);
    fd.append('kronologi',      kronologi);
    if (lat) fd.append('latitude',  parseFloat(lat).toFixed(6));
    if (lng) fd.append('longitude', parseFloat(lng).toFixed(6));
    fotoFiles.forEach((f, i) => fd.append(`foto[${i}]`, f));

    try {
      const res = await fetch('/api/laporan/mitra', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF },
        body: fd,
      });

      const data = await res.json();

      btn.textContent = '📤 Kirim Laporan';
      btn.disabled = false;

      if (res.status === 429) {
        document.getElementById('rate-warn').classList.add('show');
        window.scrollTo(0, 0);
        return;
      }

      if (!res.ok || !data.success) {
        // Jangan expose detail error ke user
        const msg = res.status === 422
          ? 'Periksa kembali isian form Anda.'
          : 'Gagal mengirim laporan. Silakan coba lagi.';
        toast(msg); return;
      }

      // Sukses
      incrementRL();
      document.getElementById('rate-warn').classList.remove('show');
      document.getElementById('tiket-display').textContent = data.data?.nomor_tiket || '—';
      document.getElementById('form-card').style.display = 'none';
      document.getElementById('success-screen').classList.add('show');
      window.scrollTo(0, 0);

    } catch(e) {
      btn.textContent = '📤 Kirim Laporan';
      btn.disabled = false;
      toast('Koneksi ke server gagal. Periksa internet Anda.');
    }
  };

})();
</script>
</body>
</html>