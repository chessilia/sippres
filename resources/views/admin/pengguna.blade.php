@extends('layouts.app')

@section('judul', 'Data Pengguna')

@section('isi')
<style>
  .page-header-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    margin: 0 0 18px;
  }
  .page-subtitle {
    margin: 0;
    color: var(--text-secondary);
    font-size: 14.5px;
  }

  /* Search Bar */
  .search-card {
    background: #ffffff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 14px 16px;
    margin-bottom: 18px;
    box-shadow: var(--shadow-sm);
  }
  .search-form {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
  }
  .search-input-wrap {
    position: relative;
    flex: 1;
    min-width: 240px;
  }
  .search-input-wrap svg {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    width: 16px;
    height: 16px;
    color: var(--text-muted);
    pointer-events: none;
  }
  .search-input-wrap input {
    padding-left: 36px;
    margin: 0;
  }

  /* Filter Tabs */
  .tabs-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    border-bottom: 2px solid var(--border);
  }
  .tabs-bar .tab {
    margin-right: 0;
    border-radius: 8px 8px 0 0;
    padding: 9px 16px;
    font-size: 13.5px;
  }
  .table-card {
    margin-top: 0;
    border-top-left-radius: 0;
    border-top: none;
  }

  /* User Cell */
  .user-cell {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .user-avatar-sm {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--primary-soft);
    color: var(--primary);
    font-weight: 800;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .user-meta {
    font-size: 13px;
    color: var(--text-muted);
    margin-top: 2px;
  }

  /* Action Buttons */
  .action-cell {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
  }
  .btn-sm {
    padding: 6px 12px;
    font-size: 13px;
  }
  .row-active {
    background: #eff6ff !important;
  }

  /* Form Grid */
  .form-grid-2 {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px 18px;
  }
  .radio-pill-group {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 4px;
  }
  .radio-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    border: 1px solid var(--border);
    border-radius: 8px;
    background: var(--bg-subtle);
    font-weight: 600;
    font-size: 13.5px;
    color: var(--text-main);
    cursor: pointer;
    margin: 0;
    transition: all 0.15s ease;
  }
  .radio-pill:hover {
    border-color: #93c5fd;
    background: #eff6ff;
  }
  .radio-pill input[type="radio"] {
    accent-color: var(--primary);
    margin: 0;
  }
  .form-footer {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 8px;
    margin-top: 18px;
    padding-top: 16px;
    border-top: 1px solid var(--border);
  }

  /* Warning Card */
  .warning-card {
    border-color: #fca5a5;
    background: #fef2f2;
  }
  .warning-card h3 {
    color: #991b1b;
    border-bottom-color: #fecaca;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .empty-box {
    text-align: center;
    padding: 32px 20px;
    color: var(--text-muted);
    font-weight: 600;
  }

  @media (max-width: 768px) {
    .form-grid-2 { grid-template-columns: 1fr; }
  }
</style>

  <div class="page-header-bar">
    <p class="page-subtitle">Kelola akun mahasiswa, dosen, dan admin yang terdaftar di sistem.</p>
    <a class="btn btn-primary" href="{{ route('pengguna.daftar', ['tambah' => 1]) }}">
      <i data-lucide="user-plus" style="width:16px;height:16px"></i> Tambah Pengguna
    </a>
  </div>

  {{-- Peringatan: pengguna masih punya data terkait --}}
  @if ($hapus && count($terkait) > 0)
    <div class="card warning-card" style="margin-top:0; margin-bottom:18px">
      <h3>
        <i data-lucide="alert-triangle" style="width:20px;height:20px"></i>
        Pengguna Masih Memiliki Data Terkait
      </h3>
      <p style="margin-top:0; color:#7f1d1d">
        Akun <strong>{{ $hapus->nama }}</strong> (<code>{{ $hapus->username }}</code>) masih terhubung dengan data berikut:
      </p>
      <ul style="margin:8px 0 14px; color:#991b1b; font-weight:600">
        @foreach ($terkait as $nama => $jumlahData)
          <li>{{ $jumlahData }} {{ $nama }}</li>
        @endforeach
      </ul>
      <p style="margin:0 0 16px; font-size:13.5px; color:#7f1d1d">
        Jika dilanjutkan, seluruh data di atas akan ikut terhapus secara permanen dan tidak dapat dikembalikan.
      </p>

      <div style="display:flex; gap:8px; flex-wrap:wrap">
        <a class="btn btn-batal" href="{{ route('pengguna.daftar') }}">Batal</a>
        <form method="POST" action="{{ route('pengguna.hapus', $hapus->idUser) }}" style="margin:0"
              onsubmit="return confirm('Yakin hapus pengguna beserta semua data terkaitnya?')">
          @csrf
          <input type="hidden" name="lanjut" value="1">
          <button type="submit" class="btn-hapus">
            <i data-lucide="trash-2" style="width:15px;height:15px"></i> Tetap Hapus Semua
          </button>
        </form>
      </div>
    </div>
  @endif

  {{-- Panel tambah pengguna --}}
  @if ($tambah)
    <div class="card" style="margin-top:0; margin-bottom:18px">
      <h3>Tambah Pengguna Baru</h3>
      <form method="POST" action="{{ route('pengguna.tambah') }}">
        @csrf
        <div class="form-grid-2">
          <div>
            <label for="t_nama">Nama Lengkap</label>
            <input type="text" name="nama" id="t_nama" maxlength="100" required value="{{ old('nama') }}" placeholder="Contoh: Budi Santoso">
          </div>
          <div>
            <label for="t_username">Username / NIM / NIDN</label>
            <input type="text" name="username" id="t_username" maxlength="30" required value="{{ old('username') }}" placeholder="Contoh: 235150200111001">
          </div>
          <div>
            <label for="t_email">Alamat Email</label>
            <input type="email" name="email" id="t_email" maxlength="100" required value="{{ old('email') }}" placeholder="nama@student.ub.ac.id">
          </div>
          <div>
            <label for="t_password">Kata Sandi Awal (min. 8 karakter)</label>
            <input type="text" name="password" id="t_password" minlength="8" required placeholder="Minimal 8 karakter">
          </div>
        </div>

        <div class="row" style="margin-top:14px; margin-bottom:0">
          <label>Peran Akun</label>
          <div class="radio-pill-group">
            @foreach (['Mahasiswa', 'Dosen', 'Admin'] as $r)
              <label class="radio-pill">
                <input type="radio" name="role" value="{{ $r }}" @checked(old('role', 'Mahasiswa') == $r)>
                <span>{{ $r }}</span>
              </label>
            @endforeach
          </div>
        </div>

        <div class="form-footer">
          <a class="btn btn-batal" href="{{ route('pengguna.daftar') }}">Batal</a>
          <button type="submit" class="btn-primary">
            <i data-lucide="check" style="width:15px;height:15px"></i> Simpan Pengguna
          </button>
        </div>
      </form>
    </div>
  @endif

  {{-- Panel edit --}}
  @if ($detail)
    <div class="card" style="margin-top:0; margin-bottom:18px">
      <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border); padding-bottom:12px; margin-bottom:16px">
        <h3 style="margin:0; padding:0; border:none">Edit Data Pengguna</h3>
        <span class="badge st-{{ $detail->role }}">{{ $detail->role }}</span>
      </div>

      <form method="POST" action="{{ route('pengguna.perbarui', $detail->idUser) }}">
        @csrf
        <div class="form-grid-2">
          <div>
            <label for="nama">Nama Lengkap</label>
            <input type="text" name="nama" id="nama" maxlength="100" required value="{{ old('nama', $detail->nama) }}">
          </div>
          <div>
            <label for="username">Username</label>
            <input type="text" name="username" id="username" maxlength="30" required value="{{ old('username', $detail->username) }}">
          </div>
          <div>
            <label for="email">Alamat Email</label>
            <input type="email" name="email" id="email" maxlength="100" required value="{{ old('email', $detail->email) }}">
          </div>
          <div>
            <label>Peran Akun</label>
            @if ($detail->role === 'Admin')
              <div style="padding:9px 12px; background:var(--bg-subtle); border:1px solid var(--border); border-radius:8px; font-size:13.5px; color:var(--text-muted)">
                Peran Admin tidak dapat diubah dari halaman ini.
              </div>
            @else
              <div class="radio-pill-group">
                @foreach (['Mahasiswa', 'Dosen'] as $r)
                  <label class="radio-pill">
                    <input type="radio" name="role" value="{{ $r }}" @checked(old('role', $detail->role) == $r)>
                    <span>{{ $r }}</span>
                  </label>
                @endforeach
              </div>
            @endif
          </div>
        </div>

        <div class="form-footer">
          <a class="btn btn-batal" href="{{ route('pengguna.daftar') }}">Batal</a>
          <button type="submit" class="btn-primary">
            <i data-lucide="save" style="width:15px;height:15px"></i> Simpan Perubahan
          </button>
        </div>
      </form>
    </div>
  @endif

  {{-- Cari --}}
  <div class="search-card">
    <form method="GET" action="{{ route('pengguna.daftar') }}" class="search-form">
      <input type="hidden" name="peran" value="{{ $peran }}">
      <div class="search-input-wrap">
        <i data-lucide="search"></i>
        <input type="text" name="cari" value="{{ $cari }}" placeholder="Cari nama lengkap, username, atau email...">
      </div>
      <button type="submit" class="btn-primary">Cari</button>
      @if ($cari)
        <a href="{{ route('pengguna.daftar', ['peran' => $peran]) }}" class="btn btn-batal">Reset</a>
      @endif
    </form>
  </div>

  {{-- Filter peran --}}
  <div class="tabs-bar">
    <a class="tab {{ !$peran ? 'aktif' : '' }}" href="{{ route('pengguna.daftar', ['cari' => $cari]) }}">Semua ({{ $total }})</a>
    <a class="tab {{ $peran == 'Mahasiswa' ? 'aktif' : '' }}" href="{{ route('pengguna.daftar', ['peran' => 'Mahasiswa', 'cari' => $cari]) }}">Mahasiswa ({{ $jumlah['Mahasiswa'] ?? 0 }})</a>
    <a class="tab {{ $peran == 'Dosen' ? 'aktif' : '' }}" href="{{ route('pengguna.daftar', ['peran' => 'Dosen', 'cari' => $cari]) }}">Dosen ({{ $jumlah['Dosen'] ?? 0 }})</a>
    <a class="tab {{ $peran == 'Admin' ? 'aktif' : '' }}" href="{{ route('pengguna.daftar', ['peran' => 'Admin', 'cari' => $cari]) }}">Admin ({{ $jumlah['Admin'] ?? 0 }})</a>
  </div>

  {{-- Tabel pengguna --}}
  <div class="card table-card">
    @if ($daftar->isEmpty())
      <div class="empty-box">Tidak ada pengguna yang cocok dengan pencarian atau filter.</div>
    @else
      <table>
        <thead>
          <tr>
            <th>Pengguna</th>
            <th>Peran</th>
            <th style="text-align:right">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($daftar as $u)
            <tr class="{{ $detail && $detail->idUser == $u->idUser ? 'row-active' : '' }}">
              <td>
                <div class="user-cell">
                  <div class="user-avatar-sm">{{ strtoupper(substr($u->nama, 0, 1)) }}</div>
                  <div>
                    <strong>{{ $u->nama }}</strong>
                    <div class="user-meta">{{ $u->username }} &bull; {{ $u->email }}</div>
                  </div>
                </div>
              </td>
              <td><span class="badge st-{{ $u->role }}">{{ $u->role }}</span></td>
              <td>
                <div class="action-cell">
                  <a class="btn btn-batal btn-sm"
                     href="{{ route('pengguna.daftar', ['peran' => $peran, 'cari' => $cari, 'edit' => $u->idUser]) }}">
                    <i data-lucide="pencil" style="width:14px;height:14px"></i> Edit
                  </a>
                  @if ($u->idUser !== Auth::id())
                    <form method="POST" action="{{ route('pengguna.hapus', $u->idUser) }}" style="margin:0"
                          onsubmit="return confirm('Hapus pengguna {{ $u->nama }}?')">
                      @csrf
                      <button type="submit" class="btn-hapus btn-sm">
                        <i data-lucide="trash-2" style="width:14px;height:14px"></i> Hapus
                      </button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>
@endsection