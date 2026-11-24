<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('judul') - SIPPRES</title>
  <style>
    body { font-family: Arial, sans-serif; margin:0; display:flex; background:#f4f6f8; }
    .sidebar { width:210px; background:#1f3b63; color:#fff; min-height:100vh; padding:16px; box-sizing:border-box; }
    .sidebar a { display:block; color:#fff; text-decoration:none; padding:8px 0; }
    .sidebar a.aktif { font-weight:bold; }
    .main { flex:1; padding:24px; }
    .topbar { display:flex; justify-content:space-between; align-items:center; }
    .card { background:#fff; border:1px solid #ddd; border-radius:8px; padding:16px; margin-top:16px; }
    .row { margin-bottom:12px; }
    label { display:block; font-weight:bold; margin-bottom:4px; }
    input[type=text], input[type=number], select { width:100%; padding:8px; box-sizing:border-box; }
    button, .btn { padding:8px 14px; border:none; border-radius:5px; cursor:pointer;
    text-decoration:none; font-size:14px; display:inline-block; }
    .btn-primary { background:#007bff; color:#fff; }
    .btn-hapus { background:#dc3545; color:#fff; }
    .btn-batal { background:#ccc; color:#000; }
    .alert-error { background:#fde2e2; padding:8px; border-radius:4px; margin-top:12px; }
    .alert-success { background:#dff5e1; padding:8px; border-radius:4px; margin-top:12px; }
    .badge { background:#28a745; color:#fff; padding:2px 8px; border-radius:10px; font-size:12px; }
    .tab { display:inline-block; padding:8px 14px; background:#e5e9ef; color:#000; text-decoration:none; border-radius:5px 5px 0 0; }
    .tab.aktif { background:#007bff; color:#fff; }
    .progress { background:#e5e9ef; border-radius:5px; height:12px; }
    .progress div { background:#28a745; height:12px; border-radius:5px; }

    .st-Menunggu  { background:#f0ad4e; }
    .st-Disetujui { background:#28a745; }
    .st-Ditolak   { background:#dc3545; }
    .st-Hadir { background:#28a745; }
    .st-Izin  { background:#17a2b8; }
    .st-Sakit { background:#f0ad4e; }
    .st-Alpa  { background:#dc3545; }
    .st-Belum { background:#999; }
    .st-Baru       { background:#17a2b8; }
    .st-Diproses   { background:#f0ad4e; }
    .st-Selesai    { background:#28a745; }
    .st-TidakValid { background:#999; }
    .st-Mahasiswa { background:#17a2b8; }
    .st-Dosen     { background:#6f42c1; }
    .st-Admin     { background:#343a40; }
    .grid4 { display:grid; grid-template-columns:repeat(auto-fit, minmax(190px, 1fr)); gap:12px; margin-top:16px; }
    .stat { background:#fff; border:1px solid #ddd; border-radius:8px; padding:14px; }
    .stat b { font-size:26px; display:block; }
    @media print {
      .sidebar, .noprint, .topbar form { display:none !important; }
      .main { padding:0; }
    .avatar { width:72px; height:72px; border-radius:50%; background:#1f3b63; color:#fff;
      display:flex; align-items:center; justify-content:center; font-size:28px; object-fit:cover; }
    }
  </style>
</head>
<body>

    <div class="sidebar">
    <h3>SIPPRES</h3>
    <p style="font-size:12px">Portal {{ Auth::user()->role }}</p>

    @if (Auth::user()->role === 'Dosen')
      <a href="{{ route('dashboard.dosen') }}">Dashboard</a>
      <a href="{{ route('jadwal') }}">Jadwal Mengajar</a>
      <a href="{{ route('presensi.dosen') }}">Presensi QR</a>
      <a href="{{ route('izin.dosen') }}">Pengajuan Izin
        @php
          $menunggu = \App\Models\PengajuanIzin::where('status', 'Menunggu')
              ->whereHas('perkuliahan', fn ($q) => $q->where('idDosen', Auth::id()))->count();
        @endphp
        @if ($menunggu > 0) <span class="badge" style="background:#dc3545">{{ $menunggu }}</span> @endif
      </a>
      <a href="{{ route('koreksi.dosen') }}">Data Presensi</a>
      <a href="{{ route('rekap.dosen') }}">Rekap Kehadiran</a>
    @elseif (Auth::user()->role === 'Mahasiswa')
      <a href="{{ route('dashboard.mahasiswa') }}">Dashboard</a>
      <a href="{{ route('jadwal') }}">Jadwal Kuliah</a>
      <a href="{{ route('presensi.mahasiswa') }}">Presensi</a>
      <a href="{{ route('izin.mahasiswa') }}">Pengajuan Izin</a>
      <a href="{{ route('riwayat') }}">Riwayat</a>
    @elseif (Auth::user()->role === 'Admin')
      <a href="{{ route('dashboard.admin') }}">Dashboard</a>
      <a href="{{ route('perkuliahan.daftar') }}">Data Perkuliahan</a>
      <a href="{{ route('pengguna.daftar') }}">Data Pengguna</a>
      <a href="{{ route('crisis.admin') }}">Crisis Center
        @php $laporanBaru = \App\Models\LaporanKendala::where('status', 'Baru')->count(); @endphp
        @if ($laporanBaru > 0) <span class="badge" style="background:#dc3545">{{ $laporanBaru }}</span> @endif
      </a>
      <a href="{{ route('statistik.admin') }}">Rekap dan Statistik</a>
    @endif

    @if (in_array(Auth::user()->role, ['Mahasiswa', 'Dosen']))
      <hr>
      <a href="{{ route('notifikasi') }}">Notifikasi
        @php $belumBaca = \App\Models\Notifikasi::where('idPenerima', Auth::id())->where('statusBaca', false)->count(); @endphp
        @if ($belumBaca > 0) <span class="badge" style="background:#dc3545">{{ $belumBaca }}</span> @endif
      </a>
      <a href="{{ route('crisis.pengguna') }}">Lapor Kendala</a>
    @endif
    <hr>
    <a href="{{ route('profil') }}">Profil</a>
  </div>

  <div class="main">
        <div class="topbar">
      <h2>@yield('judul')</h2>
      <div>
        <a href="{{ route('profil') }}">{{ Auth::user()->nama }}</a>
        <form method="POST" action="{{ route('logout') }}" style="display:inline"
              onsubmit="return confirm('Yakin ingin keluar?')">
          @csrf
          <button type="submit" class="btn-batal">Keluar</button>
        </form>
      </div>
    </div>

    @if (session('success')) <div class="alert-success">{{ session('success') }}</div> @endif
    @if (session('error'))   <div class="alert-error">{{ session('error') }}</div>   @endif
    @if ($errors->any())     <div class="alert-error">{{ $errors->first() }}</div>    @endif

    @yield('isi')
  </div>

  @stack('scripts')
</body>
</html>