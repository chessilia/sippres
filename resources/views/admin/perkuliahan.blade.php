@extends('layouts.app')

@section('judul', 'Data Perkuliahan')

@section('isi')
  <style>
    .toolbar-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 12px;
      margin-bottom: 18px;
    }
    .filter-group {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: 10px;
      flex: 1;
      min-width: 280px;
    }
    .search-box {
      position: relative;
      flex: 1;
      max-width: 340px;
      min-width: 200px;
    }
    .search-box svg {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      width: 16px;
      height: 16px;
      color: var(--text-muted);
      pointer-events: none;
    }
    .search-box input {
      padding-left: 36px;
    }
    .filter-select {
      width: auto !important;
      min-width: 155px;
    }
    .grid-2 {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 16px 20px;
    }
    .matkul-title {
      font-weight: 700;
      color: var(--text-main);
      font-size: 15px;
      display: block;
      margin-bottom: 4px;
    }
    .matkul-meta {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 13px;
      color: var(--text-secondary);
    }
    .kode-pill {
      background: var(--primary-soft);
      color: var(--primary-hover);
      font-weight: 700;
      font-size: 12px;
      padding: 2px 8px;
      border-radius: 6px;
    }
    .action-group {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: 6px;
    }
    .btn-sm {
      padding: 6px 12px;
      font-size: 13px;
      border-radius: 7px;
    }
    .btn-edit {
      background: #fef3c7;
      color: #92400e;
      border-color: #fde68a;
    }
    .btn-edit:hover {
      background: #fde68a;
      color: #78350f;
    }
    .peserta-list {
      display: flex;
      flex-direction: column;
      gap: 8px;
      margin: 12px 0 20px;
    }
    .peserta-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 14px;
      background: var(--bg-subtle);
      border: 1px solid var(--border);
      border-radius: 9px;
    }
    .calon-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
      gap: 10px;
      margin: 10px 0 16px;
    }
    .calon-item {
      display: flex;
      align-items: center;
      gap: 9px;
      padding: 10px 12px;
      border: 1px solid var(--border);
      border-radius: 8px;
      background: #fff;
      cursor: pointer;
      font-weight: 600 !important;
      margin: 0 !important;
    }
    .calon-item:hover {
      border-color: #93c5fd;
      background: var(--bg-subtle);
    }
    .form-actions {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 20px;
      padding-top: 16px;
      border-top: 1px solid var(--border);
    }
    @media (max-width: 640px) {
      .grid-2 { grid-template-columns: 1fr; }
    }
  </style>

  {{-- Panel Tambah Perkuliahan --}}
  @if ($tambah)
    <div class="card" style="margin-top:0; margin-bottom:20px; border-color:#93c5fd;">
      <h3><i data-lucide="plus-circle" style="width:19px;height:19px;vertical-align:-3px;color:var(--primary);margin-right:6px"></i> Tambah Perkuliahan Baru</h3>
      <form method="POST" action="{{ route('perkuliahan.tambah') }}">
        @csrf
        <div class="grid-2">
          <div>
            <label>Kode Mata Kuliah</label>
            <input type="text" name="kodeMatkul" maxlength="10" value="{{ old('kodeMatkul') }}" placeholder="Contoh: SI4201" required>
          </div>
          <div>
            <label>Kelas</label>
            <input type="text" name="kelas" maxlength="5" value="{{ old('kelas') }}" placeholder="Contoh: A" required>
          </div>
          <div>
            <label>Nama Mata Kuliah</label>
            <input type="text" name="namaMatkul" maxlength="100" value="{{ old('namaMatkul') }}" placeholder="Masukkan nama mata kuliah" required>
          </div>
          <div>
            <label>Dosen Pengampu</label>
            <select name="idDosen" required>
              @foreach ($daftarDosen as $d)
                <option value="{{ $d->idUser }}" @selected(old('idDosen') == $d->idUser)>{{ $d->nama }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label>Hari</label>
            <select name="hari" required>
              @foreach (['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)
                <option value="{{ $h }}" @selected(old('hari') == $h)>{{ $h }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label>Ruang</label>
            <input type="text" name="ruang" maxlength="20" value="{{ old('ruang') }}" placeholder="Contoh: F3.10" required>
          </div>
          <div>
            <label>Jam Mulai</label>
            <input type="time" name="jamMulai" value="{{ old('jamMulai') }}" required>
          </div>
          <div>
            <label>Jam Selesai</label>
            <input type="time" name="jamSelesai" value="{{ old('jamSelesai') }}" required>
          </div>
        </div>
        <div class="form-actions">
          <a class="btn btn-batal" href="{{ route('perkuliahan.daftar') }}">Batal</a>
          <button type="submit" class="btn-primary">
            <i data-lucide="check" style="width:16px;height:16px"></i> Simpan Perkuliahan
          </button>
        </div>
      </form>
    </div>
  @endif

  {{-- Panel Edit Perkuliahan --}}
  @if ($detail)
    <div class="card" style="margin-top:0; margin-bottom:20px; border-color:#fde68a;">
      <h3><i data-lucide="edit-3" style="width:19px;height:19px;vertical-align:-3px;color:#d97706;margin-right:6px"></i> Edit Perkuliahan: {{ $detail->namaMatkul }}</h3>
      <form method="POST" action="{{ route('perkuliahan.proses', $detail->idPerkuliahan) }}">
        @csrf
        <input type="hidden" name="opsi" value="Edit">

        <div class="grid-2">
          <div>
            <label>Kode Mata Kuliah</label>
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

        <div class="form-actions">
          <a class="btn btn-batal" href="{{ route('perkuliahan.daftar') }}">Batal</a>
          <button type="submit" class="btn-primary">
            <i data-lucide="save" style="width:16px;height:16px"></i> Simpan Perubahan
          </button>
        </div>
      </form>
    </div>
  @endif

  {{-- Panel Peserta Kuliah --}}
  @if ($kelola)
    <div class="card" style="margin-top:0; margin-bottom:20px; border-color:#93c5fd;">
      <h3>
        <i data-lucide="users" style="width:19px;height:19px;vertical-align:-3px;color:var(--primary);margin-right:6px"></i>
        Peserta Kuliah: {{ $kelola->namaMatkul }} (Kelas {{ $kelola->kelas }})
      </h3>
      <p style="margin-top:0; color:var(--text-secondary)">Total <strong>{{ $peserta->count() }}</strong> mahasiswa terdaftar pada kelas ini.</p>

      <div class="peserta-list">
        @forelse ($peserta as $m)
          <div class="peserta-item">
            <div>
              <strong>{{ $m->nama }}</strong>
              <span style="color:var(--text-muted); font-size:13.5px; margin-left:6px">({{ $m->username }})</span>
            </div>
            <form method="POST" action="{{ route('peserta.hapus', [$kelola->idPerkuliahan, $m->idUser]) }}" style="margin:0"
                  onsubmit="return confirm('Keluarkan {{ $m->nama }} dari perkuliahan ini?')">
              @csrf
              <button type="submit" class="btn-hapus btn-sm">
                <i data-lucide="user-minus" style="width:14px;height:14px"></i> Keluarkan
              </button>
            </form>
          </div>
        @empty
          <p style="color:var(--text-muted); font-style:italic">Belum ada peserta terdaftar.</p>
        @endforelse
      </div>

      <h4 style="padding-top:14px; border-top:1px solid var(--border)">Tambahkan Mahasiswa ke Kelas</h4>
      @if ($calon->isEmpty())
        <p style="color:var(--text-muted); font-size:14px">Semua mahasiswa sudah terdaftar pada perkuliahan ini.</p>
      @else
        <form method="POST" action="{{ route('peserta.tambah', $kelola->idPerkuliahan) }}">
          @csrf
          <div class="calon-grid">
            @foreach ($calon as $m)
              <label class="calon-item">
                <input type="checkbox" name="mahasiswa[]" value="{{ $m->idUser }}" style="width:16px;height:16px;accent-color:var(--primary)">
                <span>{{ $m->nama }} <small style="color:var(--text-muted)">({{ $m->username }})</small></span>
              </label>
            @endforeach
          </div>
          <button type="submit" class="btn-primary btn-sm">
            <i data-lucide="user-plus" style="width:15px;height:15px"></i> Tambahkan yang Dipilih
          </button>
        </form>
      @endif

      <div class="form-actions">
        <a class="btn btn-batal" href="{{ route('perkuliahan.daftar') }}">Tutup Panel Peserta</a>
      </div>
    </div>
  @endif

  {{-- Tabel Utama Daftar Perkuliahan --}}
  <div class="card" style="margin-top:0">
    <div class="toolbar-row">
      <form method="GET" action="{{ route('perkuliahan.daftar') }}" class="filter-group">
        <div class="search-box">
          <i data-lucide="search"></i>
          <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari mata kuliah, kode, atau dosen...">
        </div>
        <select name="hari" class="filter-select">
          <option value="">Semua Hari</option>
          @foreach (['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)
            <option value="{{ $h }}" @selected(request('hari') == $h)>{{ $h }}</option>
          @endforeach
        </select>
        <button type="submit" class="btn-batal">
          <i data-lucide="filter" style="width:15px;height:15px"></i> Filter
        </button>
        @if (request('cari') || request('hari'))
          <a href="{{ route('perkuliahan.daftar') }}" class="btn btn-batal">Reset</a>
        @endif
      </form>

      <a class="btn btn-primary" href="{{ route('perkuliahan.daftar', ['tambah' => 1]) }}">
        <i data-lucide="plus" style="width:16px;height:16px"></i> Tambah Perkuliahan
      </a>
    </div>

    <table>
      <thead>
        <tr>
          <th>Mata Kuliah</th>
          <th>Dosen Pengampu</th>
          <th>Jadwal</th>
          <th style="width:240px">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($daftar as $p)
          <tr>
            <td>
              <span class="matkul-title">{{ $p->namaMatkul }}</span>
              <div class="matkul-meta">
                <span class="kode-pill">{{ $p->kodeMatkul }}</span>
                <span>• Kelas {{ $p->kelas }}</span>
                <span>• Ruang {{ $p->ruang }}</span>
              </div>
            </td>
            <td>
              <div style="display:flex; align-items:center; gap:8px; font-weight:600">
                <i data-lucide="user-check" style="width:16px;height:16px;color:var(--text-muted)"></i>
                <span>{{ $p->dosen->nama ?? '-' }}</span>
              </div>
            </td>
            <td>
              <span class="badge st-Izin">{{ $p->hari }}</span>
              <div style="font-size:13.5px; color:var(--text-secondary); margin-top:4px; font-weight:600">
                {{ substr($p->jamMulai, 0, 5) }} – {{ substr($p->jamSelesai, 0, 5) }} WIB
              </div>
            </td>
            <td>
              <div class="action-group">
                <a class="btn btn-batal btn-sm" href="{{ route('perkuliahan.daftar', ['peserta' => $p->idPerkuliahan]) }}">
                  <i data-lucide="users" style="width:14px;height:14px"></i> Peserta
                </a>
                <a class="btn btn-edit btn-sm" href="{{ route('perkuliahan.daftar', ['edit' => $p->idPerkuliahan]) }}">
                  <i data-lucide="pencil" style="width:14px;height:14px"></i> Edit
                </a>
                <form method="POST" action="{{ route('perkuliahan.proses', $p->idPerkuliahan) }}" style="margin:0"
                      onsubmit="return confirm('Hapus perkuliahan {{ $p->namaMatkul }}?')">
                  @csrf
                  <input type="hidden" name="opsi" value="Hapus">
                  <button type="submit" class="btn-hapus btn-sm">
                    <i data-lucide="trash-2" style="width:14px;height:14px"></i> Hapus
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4" style="text-align:center; padding:32px; color:var(--text-muted)">
              Belum ada data perkuliahan yang sesuai.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endsection