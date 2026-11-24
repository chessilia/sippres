<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Perkuliahan - SIPPRES</title>
  <style>
    body { font-family: Arial, sans-serif; margin:0; display:flex; background:#f4f6f8; }
    .sidebar { width:200px; background:#1f3b63; color:#fff; min-height:100vh; padding:16px; }
    .sidebar a { display:block; color:#fff; text-decoration:none; padding:8px 0; }
    .sidebar a.aktif { font-weight:bold; }
    .main { flex:1; padding:24px; }
    .topbar { display:flex; justify-content:space-between; align-items:center; }
    table { width:100%; border-collapse:collapse; background:#fff; margin-top:12px; }
    th, td { border:1px solid #ddd; padding:8px; text-align:left; }
    th { background:#eef1f5; }
    .filter { display:flex; gap:8px; margin-top:12px; }
    .filter input, .filter select { padding:8px; }
    .panel { background:#fff; border:1px solid #ddd; border-radius:8px; padding:16px; margin-top:20px; }
    .grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
    label { display:block; font-weight:bold; margin-bottom:4px; }
    .panel input, .panel select { width:100%; padding:8px; box-sizing:border-box; }
    button, .btn { padding:8px 14px; border:none; border-radius:5px; cursor:pointer;
                   text-decoration:none; font-size:14px; display:inline-block; }
    .btn-edit { background:#ffc107; color:#000; }
    .btn-hapus { background:#dc3545; color:#fff; }
    .btn-simpan { background:#007bff; color:#fff; }
    .btn-batal { background:#ccc; color:#000; }
    .alert-error { background:#fde2e2; padding:8px; border-radius:4px; margin-top:12px; }
    .alert-success { background:#dff5e1; padding:8px; border-radius:4px; margin-top:12px; }
  </style>
</head>
<body>

  <!-- Menu Navigasi - Sidebar -->
  <div class="sidebar">
    <h3>SIPPRES</h3>
    <a href="{{ route('dashboard.admin') }}">Dashboard</a>
    <a href="{{ route('perkuliahan.daftar') }}" class="aktif">Data Perkuliahan</a>
    <a href="#">Data Pengguna</a>
    <a href="{{ route('crisis.admin') }}">Crisis Center</a>
    <a href="#">Rekap dan Statistik</a>
  </div>

  <div class="main">
    <!-- Profil Admin & Keluar -->
    <div class="topbar">
      <h2>Data Perkuliahan</h2>
      <form method="POST" action="{{ route('logout') }}"
            onsubmit="return confirm('Yakin ingin keluar?')">
        @csrf
        {{ Auth::user()->nama }}
        <button type="submit" class="btn-batal">Keluar</button>
      </form>
    </div>
    <p>Pilih perkuliahan, lalu edit atau hapus datanya.</p>

    @if (session('success')) <div class="alert-success">{{ session('success') }}</div> @endif
    @if (session('error'))   <div class="alert-error">{{ session('error') }}</div>   @endif

    <!-- Cari + Filter Hari -->
    <form method="GET" action="{{ route('perkuliahan.daftar') }}" class="filter">
      <input type="text" name="cari" value="{{ request('cari') }}"
             placeholder="Cari mata kuliah, kode, atau dosen">
      <select name="hari">
        <option value="">Semua Hari</option>
        @foreach (['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)
          <option value="{{ $h }}" @selected(request('hari') == $h)>{{ $h }}</option>
        @endforeach
      </select>
      <button type="submit" class="btn-simpan">Cari</button>
    </form>

    <!-- Tabel Perkuliahan -->
    <table>
      <tr>
        <th>Mata Kuliah</th>
        <th>Dosen Pengampu</th>
        <th>Jadwal</th>
        <th>Aksi</th>
      </tr>
      @forelse ($daftar as $p)
        <tr>
          <td>{{ $p->kodeMatkul }} - {{ $p->namaMatkul }}<br>Kelas {{ $p->kelas }}, Ruang {{ $p->ruang }}</td>
          <td>{{ $p->dosen->nama ?? '-' }}</td>
          <td>{{ $p->hari }}, {{ substr($p->jamMulai, 0, 5) }} - {{ substr($p->jamSelesai, 0, 5) }}</td>
          <td>
            <a class="btn btn-edit" href="{{ route('perkuliahan.daftar', ['edit' => $p->idPerkuliahan]) }}">Edit</a>

            <form method="POST" action="{{ route('perkuliahan.proses', $p->idPerkuliahan) }}"
                  style="display:inline"
                  onsubmit="return confirm('Hapus perkuliahan {{ $p->namaMatkul }}?')">
              @csrf
              <input type="hidden" name="opsi" value="Hapus">
              <button type="submit" class="btn-hapus">Hapus</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="4">Belum ada data perkuliahan.</td></tr>
      @endforelse
    </table>

    <!-- Panel Edit Perkuliahan (muncul setelah klik Edit) -->
    @if ($detail)
      <div class="panel">
        <h3>Edit Perkuliahan</h3>
        <form method="POST" action="{{ route('perkuliahan.proses', $detail->idPerkuliahan) }}">
          @csrf
          <input type="hidden" name="opsi" value="Edit">

          <div class="grid">
            <div>
              <label>Kode</label>
              <input type="text" name="kodeMatkul" value="{{ old('kodeMatkul', $detail->kodeMatkul) }}" required>
            </div>
            <div>
              <label>Kelas</label>
              <input type="text" name="kelas" value="{{ old('kelas', $detail->kelas) }}" required>
            </div>
            <div>
              <label>Nama Mata Kuliah</label>
              <input type="text" name="namaMatkul" value="{{ old('namaMatkul', $detail->namaMatkul) }}" required>
            </div>
            <div>
              <label>Dosen Pengampu</label>
              <select name="idDosen" required>
                @foreach ($daftarDosen as $d)
                  <option value="{{ $d->idUser }}" @selected(old('idDosen', $detail->idDosen) == $d->idUser)>{{ $d->nama }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label>Hari</label>
              <select name="hari" required>
                @foreach (['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)
                  <option value="{{ $h }}" @selected(old('hari', $detail->hari) == $h)>{{ $h }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label>Ruang</label>
              <input type="text" name="ruang" value="{{ old('ruang', $detail->ruang) }}" required>
            </div>
            <div>
              <label>Jam Mulai</label>
              <input type="time" name="jamMulai" value="{{ old('jamMulai', substr($detail->jamMulai, 0, 5)) }}" required>
            </div>
            <div>
              <label>Jam Selesai</label>
              <input type="time" name="jamSelesai" value="{{ old('jamSelesai', substr($detail->jamSelesai, 0, 5)) }}" required>
            </div>
          </div>

          <br>
          <a class="btn btn-batal" href="{{ route('perkuliahan.daftar') }}">Batal</a>
          <button type="submit" class="btn-simpan">Simpan Perubahan</button>
        </form>
      </div>
    @endif
  </div>
</body>
</html>