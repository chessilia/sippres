<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('judul') - SIPPRES</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    :root {
      --primary: #2563eb;
      --primary-hover: #1d4ed8;
      --primary-soft: #dbeafe;
      --sidebar-bg: #f1f5f9;
      --sidebar-border: #cbd5e1;
      --bg-body: #ffffff;
      --bg-subtle: #f8fafc;
      --text-main: #0f172a;
      --text-secondary: #475569;
      --text-muted: #64748b;
      --border: #e2e8f0;
      --radius: 10px;
      --shadow-sm: 0 1px 3px rgba(15, 23, 42, 0.05);
      --shadow-md: 0 4px 12px rgba(15, 23, 42, 0.06);
    }

    * { box-sizing: border-box; }
    body {
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
      margin: 0;
      display: flex;
      background: var(--bg-body);
      color: var(--text-main);
      font-size: 15px;
      line-height: 1.55;
      -webkit-font-smoothing: antialiased;
    }

    /* Sidebar - Clean Tinted Slate */
    .sidebar {
      width: 266px;
      background: var(--sidebar-bg);
      border-right: 1px solid var(--sidebar-border);
      min-height: 100vh;
      padding: 20px 14px;
      display: flex;
      flex-direction: column;
      position: sticky;
      top: 0;
      height: 100vh;
      overflow-y: auto;
      transition: transform 0.25s ease;
      z-index: 50;
    }
    .brand {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 4px 10px 18px;
      border-bottom: 1px solid var(--sidebar-border);
      margin-bottom: 14px;
    }
    .brand-logo {
      width: 40px;
      height: 40px;
      border-radius: 10px;
      background: var(--primary);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 17px;
      color: #fff;
      box-shadow: 0 2px 6px rgba(37, 99, 235, 0.3);
      flex-shrink: 0;
    }
    .brand h3 { margin: 0; font-size: 20px; font-weight: 800; letter-spacing: 0.3px; color: var(--text-main); line-height: 1.2; }
    .brand p { margin: 0; font-size: 12.5px; color: var(--text-muted); font-weight: 600; }

    .nav-label {
      font-size: 11.5px;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: var(--text-muted);
      font-weight: 700;
      padding: 12px 12px 6px;
    }
    .sidebar a {
      display: flex;
      align-items: center;
      justify-content: space-between;
      color: var(--text-secondary);
      text-decoration: none;
      padding: 10px 12px;
      border-radius: 8px;
      margin-bottom: 3px;
      font-weight: 600;
      font-size: 14.5px;
      transition: all 0.15s ease;
    }
    .nav-item-left {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .sidebar svg {
      width: 18px;
      height: 18px;
      stroke-width: 2;
      flex-shrink: 0;
    }
    .sidebar a:hover {
      background: #e2e8f0;
      color: var(--text-main);
    }
    .sidebar a.aktif {
      background: var(--primary);
      color: #ffffff;
      font-weight: 700;
      box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25);
    }
    .sidebar hr {
      border: 0;
      border-top: 1px solid var(--sidebar-border);
      margin: 12px 6px;
    }

    /* Main Content & Topbar */
    .main {
      flex: 1;
      min-width: 0;
      display: flex;
      flex-direction: column;
      background: var(--bg-body);
    }
    .topbar {
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(8px);
      border-bottom: 1px solid var(--border);
      padding: 14px 32px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: sticky;
      top: 0;
      z-index: 40;
    }
    .topbar-left { display: flex; align-items: center; gap: 12px; }
    .topbar h2 { margin: 0; font-size: 23px; font-weight: 800; color: var(--text-main); letter-spacing: -0.3px; }
    .menu-toggle {
      display: none;
      background: var(--bg-subtle);
      border: 1px solid var(--border);
      color: var(--text-main);
      padding: 6px 9px;
      border-radius: 8px;
    }
    .user-area { display: flex; align-items: center; gap: 10px; }
    .user-pill {
      display: flex;
      align-items: center;
      gap: 8px;
      text-decoration: none;
      color: var(--text-main);
      font-weight: 700;
      font-size: 14px;
      padding: 4px 12px 4px 5px;
      border-radius: 99px;
      background: var(--bg-subtle);
      border: 1px solid var(--border);
      transition: all 0.15s ease;
    }
    .user-pill:hover {
      background: var(--primary-soft);
      border-color: #93c5fd;
      color: var(--primary-hover);
    }
    .user-dot {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      background: var(--primary);
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 13px;
      font-weight: 700;
    }

    .content-wrapper {
      padding: 26px 32px;
      max-width: 1340px;
      width: 100%;
    }

    /* Cards & Grid */
    .card {
      background: #ffffff;
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 24px;
      margin-top: 16px;
      box-shadow: var(--shadow-sm);
    }
    .card h3 {
      margin: 0 0 16px;
      padding-bottom: 12px;
      border-bottom: 1px solid var(--border);
      font-size: 20px;
      font-weight: 800;
      color: var(--text-main);
      letter-spacing: -0.2px;
    }
    .card h4 { margin: 0 0 10px; font-size: 16.5px; font-weight: 700; color: var(--text-main); }
    /* Kata yang ditonjolkan dibuat sedikit lebih besar */
    .card strong, .card b { font-weight: 700; color: var(--text-main); font-size: 1.07em; }

    .grid4 {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 16px;
      margin-top: 16px;
    }
    .stat {
      background: var(--bg-subtle);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 18px 20px;
      font-size: 14.5px;
      font-weight: 600;
      color: var(--text-secondary);
      transition: all 0.15s ease;
    }
    .stat:hover {
      background: #ffffff;
      transform: translateY(-2px);
      box-shadow: var(--shadow-md);
      border-color: #93c5fd;
    }
    .stat b { font-size: 34px; font-weight: 800; display: block; color: var(--text-main); margin-top: 4px; letter-spacing: -0.5px; line-height: 1.2; }

    /* Forms & Inputs */
    .row { margin-bottom: 14px; }
    label { display: block; font-weight: 700; font-size: 14px; margin-bottom: 6px; color: #334155; }
    input[type=text], input[type=number], input[type=email], input[type=password],
    input[type=date], input[type=time], input[type=file], select, textarea {
      width: 100%;
      padding: 10px 12px;
      border: 1px solid #cbd5e1;
      border-radius: 8px;
      font-family: inherit;
      font-size: 15px;
      color: var(--text-main);
      background: #fff;
      transition: border-color 0.15s, box-shadow 0.15s;
    }
    input:focus, select:focus, textarea:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.14);
    }

    /* Buttons */
    button, .btn {
      padding: 9px 18px;
      border: 1px solid transparent;
      border-radius: 8px;
      cursor: pointer;
      text-decoration: none;
      font-family: inherit;
      font-size: 14px;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      transition: all 0.15s ease;
    }
    .btn-primary {
      background: var(--primary);
      color: #fff;
    }
    .btn-primary:hover { background: var(--primary-hover); }
    .btn-hapus { background: #ef4444; color: #fff; }
    .btn-hapus:hover { background: #dc2626; }
    .btn-batal { background: #fff; color: var(--text-secondary); border-color: var(--border); }
    .btn-batal:hover { background: var(--bg-subtle); color: var(--text-main); }

    /* Tables */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 12px;
      font-size: 14.5px;
    }
    th {
      background: var(--bg-subtle);
      color: var(--text-secondary);
      font-weight: 700;
      font-size: 14px;
      text-align: left;
      padding: 12px 14px;
      border-bottom: 1px solid var(--border);
      white-space: nowrap;
    }
    td {
      padding: 13px 14px;
      border-bottom: 1px solid var(--border);
      vertical-align: middle;
    }
    td strong, td b { font-weight: 700; color: var(--text-main); font-size: 1.06em; }
    tbody tr:hover { background: var(--bg-subtle); }

    /* Alerts */
    .alert-error, .alert-success {
      padding: 12px 16px;
      border-radius: 9px;
      margin-bottom: 16px;
      font-weight: 600;
      border: 1px solid transparent;
    }
    .alert-error { background: #fef2f2; color: #991b1b; border-color: #fecaca; }
    .alert-success { background: #f0fdf4; color: #166534; border-color: #bbf7d0; }

    /* Badges & Statuses */
    .badge {
      background: var(--primary);
      color: #fff;
      padding: 3px 11px;
      border-radius: 99px;
      font-size: 12.5px;
      font-weight: 700;
      display: inline-block;
    }
    .sidebar .badge { padding: 2px 8px; font-size: 12px; }

    .st-Menunggu, .st-Sakit, .st-Diproses { background: #fef3c7; color: #92400e; }
    .st-Disetujui, .st-Hadir, .st-Selesai { background: #dcfce7; color: #166534; }
    .st-Ditolak, .st-Alpa { background: #fee2e2; color: #991b1b; }
    .st-Belum, .st-TidakValid { background: #f1f5f9; color: #475569; }
    .st-Izin, .st-Baru, .st-Mahasiswa { background: #dbeafe; color: #1e40af; }
    .st-Dosen { background: #f3e8ff; color: #6b21a8; }
    .st-Admin { background: #e2e8f0; color: #1e293b; }

    /* Tabs & Progress */
    .tab {
      display: inline-block;
      padding: 9px 18px;
      background: var(--sidebar-bg);
      color: var(--text-secondary);
      text-decoration: none;
      border-radius: 8px 8px 0 0;
      font-weight: 700;
      font-size: 14px;
      margin-right: 4px;
      transition: all 0.15s ease;
    }
    .tab:hover { background: #e2e8f0; color: var(--text-main); }
    .tab.aktif { background: var(--primary); color: #fff; }

    .progress { background: #e2e8f0; border-radius: 99px; height: 10px; overflow: hidden; }
    .progress div { background: var(--primary); height: 100%; border-radius: 99px; }

    .avatar {
      width: 76px; height: 76px; border-radius: 50%;
      background: var(--primary); color: #fff;
      display: flex; align-items: center; justify-content: center;
      font-size: 28px; font-weight: 800; object-fit: cover;
    }

    .sidebar-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, 0.35);
      z-index: 45;
    }

    @media print {
      .sidebar, .noprint, .topbar form, .menu-toggle { display: none !important; }
      .main, .content-wrapper { padding: 0; }
      .topbar { position: static; border: none; padding: 0 0 12px; }
      .card { box-shadow: none; border: 1px solid #ccc; }
    }

    @media (max-width: 768px) {
      .sidebar {
        position: fixed;
        left: 0; top: 0; bottom: 0;
        transform: translateX(-100%);
      }
      .sidebar.open { transform: translateX(0); }
      .sidebar-overlay.open { display: block; }
      .menu-toggle { display: inline-flex; }
      .topbar { padding: 12px 16px; }
      .topbar h2 { font-size: 19px; }
      .content-wrapper { padding: 16px; }
      table { display: block; overflow-x: auto; }
    }
  </style>
</head>
<body>

  <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

  <aside class="sidebar" id="sidebar">
    <div class="brand">
      <div class="brand-logo"><i data-lucide="check-circle-2" style="width:20px;height:20px"></i></div>
      <div>
        <h3>SIPPRES</h3>
        <p>Portal {{ Auth::user()->role }}</p>
      </div>
    </div>

    <div class="nav-label">Menu Utama</div>

    @if (Auth::user()->role === 'Dosen')
      <a href="{{ route('dashboard.dosen') }}" class="{{ request()->routeIs('dashboard.dosen') ? 'aktif' : '' }}">
        <span class="nav-item-left"><i data-lucide="layout-dashboard"></i> Dashboard</span>
      </a>
      <a href="{{ route('jadwal') }}" class="{{ request()->routeIs('jadwal*') ? 'aktif' : '' }}">
        <span class="nav-item-left"><i data-lucide="calendar"></i> Jadwal Mengajar</span>
      </a>
      <a href="{{ route('presensi.dosen') }}" class="{{ request()->routeIs('presensi.dosen*') ? 'aktif' : '' }}">
        <span class="nav-item-left"><i data-lucide="qr-code"></i> Presensi QR</span>
      </a>
      <a href="{{ route('izin.dosen') }}" class="{{ request()->routeIs('izin.dosen*') ? 'aktif' : '' }}">
        <span class="nav-item-left"><i data-lucide="file-check-2"></i> Pengajuan Izin</span>
        @php
          $menunggu = \App\Models\PengajuanIzin::where('status', 'Menunggu')
              ->whereHas('perkuliahan', fn ($q) => $q->where('idDosen', Auth::id()))->count();
        @endphp
        @if ($menunggu > 0) <span class="badge" style="background:#ef4444">{{ $menunggu }}</span> @endif
      </a>
      <a href="{{ route('koreksi.dosen') }}" class="{{ request()->routeIs('koreksi.dosen*') ? 'aktif' : '' }}">
        <span class="nav-item-left"><i data-lucide="clipboard-list"></i> Data Presensi</span>
      </a>
      <a href="{{ route('rekap.dosen') }}" class="{{ request()->routeIs('rekap.dosen*') ? 'aktif' : '' }}">
        <span class="nav-item-left"><i data-lucide="bar-chart-3"></i> Rekap Kehadiran</span>
      </a>

    @elseif (Auth::user()->role === 'Mahasiswa')
      <a href="{{ route('dashboard.mahasiswa') }}" class="{{ request()->routeIs('dashboard.mahasiswa') ? 'aktif' : '' }}">
        <span class="nav-item-left"><i data-lucide="layout-dashboard"></i> Dashboard</span>
      </a>
      <a href="{{ route('jadwal') }}" class="{{ request()->routeIs('jadwal*') ? 'aktif' : '' }}">
        <span class="nav-item-left"><i data-lucide="calendar"></i> Jadwal Kuliah</span>
      </a>
      <a href="{{ route('presensi.mahasiswa') }}" class="{{ request()->routeIs('presensi.mahasiswa*') ? 'aktif' : '' }}">
        <span class="nav-item-left"><i data-lucide="scan-line"></i> Presensi</span>
      </a>
      <a href="{{ route('izin.mahasiswa') }}" class="{{ request()->routeIs('izin.mahasiswa*') ? 'aktif' : '' }}">
        <span class="nav-item-left"><i data-lucide="file-plus-2"></i> Pengajuan Izin</span>
      </a>
      <a href="{{ route('riwayat') }}" class="{{ request()->routeIs('riwayat*') ? 'aktif' : '' }}">
        <span class="nav-item-left"><i data-lucide="history"></i> Riwayat</span>
      </a>

    @elseif (Auth::user()->role === 'Admin')
      <a href="{{ route('dashboard.admin') }}" class="{{ request()->routeIs('dashboard.admin') ? 'aktif' : '' }}">
        <span class="nav-item-left"><i data-lucide="layout-dashboard"></i> Dashboard</span>
      </a>
      <a href="{{ route('perkuliahan.daftar') }}" class="{{ request()->routeIs('perkuliahan*') ? 'aktif' : '' }}">
        <span class="nav-item-left"><i data-lucide="book-open"></i> Data Perkuliahan</span>
      </a>
      <a href="{{ route('pengguna.daftar') }}" class="{{ request()->routeIs('pengguna*') ? 'aktif' : '' }}">
        <span class="nav-item-left"><i data-lucide="users"></i> Data Pengguna</span>
      </a>
      <a href="{{ route('crisis.admin') }}" class="{{ request()->routeIs('crisis.admin*') ? 'aktif' : '' }}">
        <span class="nav-item-left"><i data-lucide="life-buoy"></i> Crisis Center</span>
        @php $laporanBaru = \App\Models\LaporanKendala::where('status', 'Baru')->count(); @endphp
        @if ($laporanBaru > 0) <span class="badge" style="background:#ef4444">{{ $laporanBaru }}</span> @endif
      </a>
      <a href="{{ route('statistik.admin') }}" class="{{ request()->routeIs('statistik.admin*') ? 'aktif' : '' }}">
        <span class="nav-item-left"><i data-lucide="pie-chart"></i> Rekap dan Statistik</span>
      </a>
    @endif

    @if (in_array(Auth::user()->role, ['Mahasiswa', 'Dosen']))
      <hr>
      <div class="nav-label">Layanan</div>
      <a href="{{ route('notifikasi') }}" class="{{ request()->routeIs('notifikasi*') ? 'aktif' : '' }}">
        <span class="nav-item-left"><i data-lucide="bell"></i> Notifikasi</span>
        @php $belumBaca = \App\Models\Notifikasi::where('idPenerima', Auth::id())->where('statusBaca', false)->count(); @endphp
        @if ($belumBaca > 0) <span class="badge" style="background:#ef4444">{{ $belumBaca }}</span> @endif
      </a>
      <a href="{{ route('crisis.pengguna') }}" class="{{ request()->routeIs('crisis.pengguna*') ? 'aktif' : '' }}">
        <span class="nav-item-left"><i data-lucide="alert-circle"></i> Lapor Kendala</span>
      </a>
    @endif

    <hr>
    <a href="{{ route('profil') }}" class="{{ request()->routeIs('profil*') ? 'aktif' : '' }}">
      <span class="nav-item-left"><i data-lucide="user-circle"></i> Profil Saya</span>
    </a>
  </aside>

  <div class="main">
    <header class="topbar">
      <div class="topbar-left">
        <button type="button" class="menu-toggle" onclick="toggleSidebar()" aria-label="Buka Menu">
          <i data-lucide="menu" style="width:18px;height:18px"></i>
        </button>
        <h2>@yield('judul')</h2>
      </div>
      <div class="user-area">
        <a href="{{ route('profil') }}" class="user-pill">
          <span class="user-dot">{{ strtoupper(substr(Auth::user()->nama, 0, 1)) }}</span>
          <span>{{ Auth::user()->nama }}</span>
        </a>
        <form method="POST" action="{{ route('logout') }}" style="margin:0"
              onsubmit="return confirm('Yakin ingin keluar?')">
          @csrf
          <button type="submit" class="btn-batal">
            <i data-lucide="log-out" style="width:15px;height:15px"></i> Keluar
          </button>
        </form>
      </div>
    </header>

    <main class="content-wrapper">
      @if (session('success')) <div class="alert-success">{{ session('success') }}</div> @endif
      @if (session('error'))   <div class="alert-error">{{ session('error') }}</div>   @endif
      @if ($errors->any())     <div class="alert-error">{{ $errors->first() }}</div>    @endif

      @yield('isi')
    </main>
  </div>

  <script>
    lucide.createIcons();
    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('open');
      document.getElementById('sidebarOverlay').classList.toggle('open');
    }
  </script>
  @stack('scripts')
</body>
</html>