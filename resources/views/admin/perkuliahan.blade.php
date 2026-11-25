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

    /* Daftar Peserta Terdaftar */
    .peserta-header-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 10px;
      margin-bottom: 10px;
    }
    .peserta-list {
      display: flex;
      flex-direction: column;
      gap: 8px;
      max-height: 260px;
      overflow-y: auto;
      padding-right: 4px;
      margin: 10px 0 20px;
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

    /* Tambah Mahasiswa Skala Fakultas */
    .tambah-mhs-box {
      background: var(--bg-subtle);
      border: 1px solid var(--border);
      border-radius: 10px;
      padding: 16px;
      margin-top: 12px;
    }
    .mode-tabs {
      display: flex;
      gap: 8px;
      margin-bottom: 14px;
      border-bottom: 1px solid var(--border);
      padding-bottom: 10px;
    }
    .mode-tab-btn {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 12px;
      border-radius: 7px;
      border: 1px solid var(--border);
      background: #fff;
      color: var(--text-secondary);
      font-size: 13px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.15s ease;
    }
    .mode-tab-btn.active {
      background: var(--primary);
      border-color: var(--primary);
      color: #fff;
    }
    .calon-toolbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 10px;
      margin-bottom: 12px;
    }
    .calon-search {
      position: relative;
      flex: 1;
      min-width: 240px;
    }
    .calon-search svg {
      position: absolute;
      left: 11px;
      top: 50%;
      transform: translateY(-50%);
      width: 15px;
      height: 15px;
      color: var(--text-muted);
      pointer-events: none;
    }
    .calon-search input {
      padding-left: 34px;
      background: #fff;
    }
    .calon-bulk-actions {
      display: flex;
      align-items: center;
      gap: 8px;
      flex-wrap: wrap;
    }
    .calon-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
      gap: 8px;
      max-height: 280px;
      overflow-y: auto;
      padding: 4px 2px;
      margin-bottom: 14px;
    }
    .calon-item {
      display: flex;
      align-items: center;
      gap: 9px;
      padding: 9px 12px;
      border: 1px solid var(--border);
      border-radius: 8px;
      background: #fff;
      cursor: pointer;
      font-weight: 600 !important;
      margin: 0 !important;
      transition: all 0.12s ease;
    }
    .calon-item:hover {
      border-color: #93c5fd;
      background: #eff6ff;
    }
    .calon-item.checked {
      border-color: var(--primary);
      background: #eff6ff;
    }
    .calon-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 12px;
      padding-top: 12px;
      border-top: 1px solid var(--border);
    }
    .selected-counter {
      font-size: 13.5px;
      font-weight: 700;
      color: var(--primary-hover);
    }
    .paste-result {
      margin-top: 8px;
      font-size: 13px;
      font-weight: 600;
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

      <div class="peserta-header-bar">
        <p style="margin:0; color:var(--text-secondary)">Total <strong>{{ $peserta->count() }}</strong> mahasiswa terdaftar pada kelas ini.</p>
        @if ($peserta->count() > 5)
          <div class="search-box" style="max-width:260px">
            <i data-lucide="search"></i>
            <input type="text" id="filterPesertaTerdaftar" placeholder="Cari peserta terdaftar...">
          </div>
        @endif
      </div>

      <div class="peserta-list" id="listPesertaTerdaftar">
        @forelse ($peserta as $m)
          <div class="peserta-item" data-search="{{ strtolower($m->nama . ' ' . $m->username) }}">
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

      <h4 style="padding-top:14px; border-top:1px solid var(--border); margin-bottom:6px">Tambahkan Mahasiswa ke Kelas</h4>
      @if ($calon->isEmpty())
        <p style="color:var(--text-muted); font-size:14px">Semua mahasiswa sudah terdaftar pada perkuliahan ini.</p>
      @else
        <div class="tambah-mhs-box">
          {{-- Mode Tab: Cari & Centang vs Paste Daftar Username/NIM --}}
          <div class="mode-tabs">
            <button type="button" class="mode-tab-btn active" data-mode="daftar">
              <i data-lucide="list-checks" style="width:15px;height:15px"></i> Cari &amp; Pilih dari Daftar ({{ $calon->count() }})
            </button>
            <button type="button" class="mode-tab-btn" data-mode="paste">
              <i data-lucide="clipboard-paste" style="width:15px;height:15px"></i> Tempel Daftar Username / NIM
            </button>
          </div>

          {{-- Panel Mode Tempel Daftar (Bulk Paste dari Excel/KRS) --}}
          <div id="panelModePaste" style="display:none; margin-bottom:14px">
            <label for="bulkUsernameInput" style="font-size:13px; color:var(--text-secondary)">
              Tempel daftar username/NIM mahasiswa (pisahkan dengan baris baru, koma, atau spasi):
            </label>
            <textarea id="bulkUsernameInput" rows="3" style="background:#fff; margin-top:4px"
                      placeholder="Contoh:&#10;mhs1&#10;mhs2&#10;235150400111001"></textarea>
            <div style="display:flex; align-items:center; gap:10px; margin-top:8px; flex-wrap:wrap">
              <button type="button" class="btn-batal btn-sm" id="btnCocokkanUsername">
                <i data-lucide="check-square" style="width:14px;height:14px"></i> Cocokkan &amp; Centang Otomatis
              </button>
              <span id="pasteResultInfo" class="paste-result"></span>
            </div>
          </div>

          <form method="POST" action="{{ route('peserta.tambah', $kelola->idPerkuliahan) }}" id="formTambahPeserta">
            @csrf

            {{-- Toolbar Cari & Pilih Semua --}}
            <div class="calon-toolbar">
              <div class="calon-search">
                <i data-lucide="search"></i>
                <input type="text" id="cariCalonInput" placeholder="Ketik nama atau username/NIM untuk menyaring...">
              </div>

              <div class="calon-bulk-actions">
                <button type="button" class="btn-batal btn-sm" id="btnPilihSemuaTampil">
                  <i data-lucide="check-check" style="width:14px;height:14px"></i> Pilih Semua yang Tampil
                </button>
                <button type="button" class="btn-batal btn-sm" id="btnResetPilihan">
                  Batal Pilih
                </button>
              </div>
            </div>

            {{-- Grid Scrollable Calon Mahasiswa --}}
            <div class="calon-grid" id="calonGrid">
              @foreach ($calon as $m)
                <label class="calon-item"
                       data-search="{{ strtolower($m->nama . ' ' . $m->username) }}"
                       data-username="{{ strtolower(trim($m->username)) }}">
                  <input type="checkbox" class="cb-calon" name="mahasiswa[]" value="{{ $m->idUser }}"
                         style="width:16px;height:16px;accent-color:var(--primary)">
                  <span>{{ $m->nama }} <small style="color:var(--text-muted)">({{ $m->username }})</small></span>
                </label>
              @endforeach
            </div>

            <div id="calonEmptySearch" style="display:none; text-align:center; padding:18px; color:var(--text-muted); font-size:13.5px">
              Tidak ada mahasiswa yang cocok dengan kata kunci pencarian.
            </div>

            <div class="calon-footer">
              <span class="selected-counter" id="selectedCounterText">0 mahasiswa dipilih</span>
              <button type="submit" class="btn-primary btn-sm" id="btnSubmitPeserta" disabled>
                <i data-lucide="user-plus" style="width:15px;height:15px"></i> Tambahkan yang Dipilih
              </button>
            </div>
          </form>
        </div>
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

  @if ($kelola)
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        // 1. Filter peserta yang sudah terdaftar
        const filterTerdaftar = document.getElementById('filterPesertaTerdaftar');
        if (filterTerdaftar) {
          filterTerdaftar.addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            document.querySelectorAll('#listPesertaTerdaftar .peserta-item').forEach(function (item) {
              item.style.display = item.dataset.search.includes(q) ? '' : 'none';
            });
          });
        }

        // 2. Pengelolaan Calon Mahasiswa (Live Search, Select All, Counter, Bulk Paste)
        const cariCalonInput   = document.getElementById('cariCalonInput');
        const calonItems       = Array.from(document.querySelectorAll('#calonGrid .calon-item'));
        const emptySearchMsg   = document.getElementById('calonEmptySearch');
        const counterText      = document.getElementById('selectedCounterText');
        const btnSubmit        = document.getElementById('btnSubmitPeserta');
        const btnPilihSemua    = document.getElementById('btnPilihSemuaTampil');
        const btnResetPilihan  = document.getElementById('btnResetPilihan');

        function perbaruiCounter() {
          let totalDipilih = 0;
          calonItems.forEach(function (item) {
            const cb = item.querySelector('.cb-calon');
            if (cb.checked) {
              totalDipilih++;
              item.classList.add('checked');
            } else {
              item.classList.remove('checked');
            }
          });
          counterText.textContent = totalDipilih + ' mahasiswa dipilih';
          btnSubmit.disabled = totalDipilih === 0;
        }

        calonItems.forEach(function (item) {
          item.querySelector('.cb-calon').addEventListener('change', perbaruiCounter);
        });

        if (cariCalonInput) {
          cariCalonInput.addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            let tampil = 0;
            calonItems.forEach(function (item) {
              const cocok = item.dataset.search.includes(q);
              item.style.display = cocok ? '' : 'none';
              if (cocok) tampil++;
            });
            emptySearchMsg.style.display = tampil === 0 ? 'block' : 'none';
          });
        }

        if (btnPilihSemua) {
          btnPilihSemua.addEventListener('click', function () {
            calonItems.forEach(function (item) {
              if (item.style.display !== 'none') {
                item.querySelector('.cb-calon').checked = true;
              }
            });
            perbaruiCounter();
          });
        }

        if (btnResetPilihan) {
          btnResetPilihan.addEventListener('click', function () {
            calonItems.forEach(function (item) {
              item.querySelector('.cb-calon').checked = false;
            });
            perbaruiCounter();
          });
        }

        // 3. Switch Mode Tab (Daftar vs Paste Username)
        const modeBtns       = document.querySelectorAll('.mode-tab-btn');
        const panelModePaste = document.getElementById('panelModePaste');

        modeBtns.forEach(function (btn) {
          btn.addEventListener('click', function () {
            modeBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            panelModePaste.style.display = this.dataset.mode === 'paste' ? 'block' : 'none';
          });
        });

        // 4. Cocokkan & Centang Otomatis dari Paste Username / NIM
        const btnCocokkan     = document.getElementById('btnCocokkanUsername');
        const bulkInput       = document.getElementById('bulkUsernameInput');
        const pasteResultInfo = document.getElementById('pasteResultInfo');

        if (btnCocokkan && bulkInput) {
          btnCocokkan.addEventListener('click', function () {
            const tokens = bulkInput.value
              .split(/[\s,;|\n\r\t]+/)
              .map(s => s.trim().toLowerCase())
              .filter(Boolean);

            if (tokens.length === 0) {
              pasteResultInfo.style.color = '#b91c1c';
              pasteResultInfo.textContent = 'Masukkan minimal satu username/NIM terlebih dahulu.';
              return;
            }

            const tokenSet = new Set(tokens);
            let ditemukan = 0;

            calonItems.forEach(function (item) {
              if (tokenSet.has(item.dataset.username)) {
                item.querySelector('.cb-calon').checked = true;
                ditemukan++;
              }
            });

            perbaruiCounter();

            if (ditemukan > 0) {
              pasteResultInfo.style.color = '#15803d';
              pasteResultInfo.textContent = ditemukan + ' mahasiswa berhasil dicocokkan & dicentang. Klik "Tambahkan yang Dipilih" untuk menyimpan.';
            } else {
              pasteResultInfo.style.color = '#b91c1c';
              pasteResultInfo.textContent = 'Tidak ada username/NIM yang cocok (atau sudah terdaftar di kelas ini).';
            }
          });
        }
      });
    </script>
  @endif
@endsection