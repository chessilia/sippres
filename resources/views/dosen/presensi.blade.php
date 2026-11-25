@extends('layouts.app')

@section('judul', 'Presensi QR Code')

@section('isi')
<style>
  .page-subtitle {
    margin: 0 0 18px;
    color: var(--text-secondary);
    font-size: 14.5px;
  }

  /* Form Buat Sesi */
  .form-grid-qr {
    display: grid;
    grid-template-columns: 1.5fr 0.9fr 1fr 0.85fr;
    gap: 16px 16px;
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
    padding: 8px 14px;
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
  .form-footer-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 18px;
    padding-top: 16px;
    border-top: 1px solid var(--border);
  }
  .hint-text {
    font-size: 13px;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 6px;
  }

  /* Kartu Sesi Aktif */
  .session-card {
    border-left: 5px solid var(--primary);
    margin-top: 20px;
  }
  .session-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 12px;
    padding-bottom: 14px;
    margin-bottom: 18px;
    border-bottom: 1px solid var(--border);
  }
  .session-header h3 {
    margin: 6px 0 0;
    padding: 0;
    border: none;
    font-size: 20px;
  }
  .session-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }
  .timer-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    color: var(--primary-hover);
    padding: 4px 12px;
    border-radius: 99px;
    font-size: 13.5px;
    font-weight: 700;
  }

  /* Split QR & Pemantauan Real-time */
  .session-body-grid {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 24px;
    align-items: start;
  }
  .qr-box {
    background: var(--bg-subtle);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 18px;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
  }
  .qr-canvas-wrap {
    background: #ffffff;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid var(--border);
    display: inline-block;
  }
  .qr-code-copy-box {
    margin-top: 10px;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    background: #ffffff;
    padding: 5px 6px 5px 10px;
    border-radius: 7px;
    border: 1px solid var(--border);
  }
  .qr-code-text {
    font-family: monospace;
    font-size: 12.5px;
    font-weight: 700;
    color: var(--text-secondary);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  .btn-copy-qr {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 5px 9px;
    border-radius: 6px;
    border: 1px solid #bfdbfe;
    background: #eff6ff;
    color: var(--primary-hover);
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    flex-shrink: 0;
    transition: all 0.15s ease;
  }
  .btn-copy-qr:hover {
    background: var(--primary);
    color: #ffffff;
    border-color: var(--primary);
  }
  .btn-copy-qr.copied {
    background: #16a34a;
    color: #ffffff;
    border-color: #16a34a;
  }

  .meta-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 14px;
  }
  .meta-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-secondary);
    background: var(--bg-subtle);
    border: 1px solid var(--border);
    padding: 5px 11px;
    border-radius: 7px;
  }
  .notes-banner {
    display: flex;
    align-items: flex-start;
    gap: 9px;
    background: #f8fafc;
    border: 1px solid var(--border);
    border-left: 3px solid var(--primary);
    border-radius: 8px;
    padding: 10px 14px;
    margin-bottom: 14px;
    font-size: 13.5px;
    color: var(--text-main);
    line-height: 1.5;
  }

  .pantau-box {
    background: var(--bg-subtle);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 16px 18px;
  }
  .pantau-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
    font-size: 14.5px;
    font-weight: 700;
  }
  .progress-fill {
    width: var(--w, 0%);
  }

  .daftar-hadir {
    list-style: none;
    margin: 14px 0 0;
    padding: 0;
    max-height: 280px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 6px;
  }
  .daftar-hadir li {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    background: #ffffff;
    border: 1px solid var(--border);
    padding: 8px 12px;
    border-radius: 7px;
    font-size: 13.5px;
    font-weight: 600;
  }
  .daftar-hadir li.belum {
    background: #f8fafc;
    color: var(--text-secondary);
  }
  .mhs-left {
    display: flex;
    align-items: center;
    gap: 9px;
    min-width: 0;
  }
  .mhs-num {
    width: 22px;
    height: 22px;
    border-radius: 99px;
    background: #e2e8f0;
    color: #334155;
    font-size: 11px;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .mhs-nama {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .mhs-right {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
  }
  .hadir-time {
    font-size: 12px;
    color: var(--text-muted);
    font-weight: 700;
    font-variant-numeric: tabular-nums;
  }
  .status-pill {
    font-size: 11.5px;
    font-weight: 800;
    padding: 2px 9px;
    border-radius: 99px;
    border: 1px solid transparent;
    white-space: nowrap;
  }
  .status-pill.hadir {
    background: #f0fdf4;
    border-color: #bbf7d0;
    color: #15803d;
  }
  .status-pill.izin {
    background: #eff6ff;
    border-color: #bfdbfe;
    color: #1d4ed8;
  }
  .status-pill.sakit {
    background: #fefce8;
    border-color: #fef08a;
    color: #a16207;
  }
  .status-pill.alpa {
    background: #fef2f2;
    border-color: #fecaca;
    color: #b91c1c;
  }
  .status-pill.belum {
    background: #f1f5f9;
    border-color: #cbd5e1;
    color: #475569;
  }
  .daftar-hadir-empty {
    text-align: center;
    color: var(--text-muted);
    font-size: 13.5px;
    padding: 16px 0 4px;
  }

  @media (max-width: 1100px) {
    .form-grid-qr { grid-template-columns: 1fr 1fr; }
  }
  @media (max-width: 960px) {
    .session-body-grid { grid-template-columns: 1fr; }
  }
  @media (max-width: 640px) {
    .form-grid-qr { grid-template-columns: 1fr; }
  }
</style>

  <p class="page-subtitle">Buat sesi presensi beserta pertemuan &amp; catatan materi perkuliahan, tampilkan QR Code kepada mahasiswa, lalu tutup sesi setelah selesai.</p>

  {{-- Form Buat Sesi --}}
  <div class="card" style="margin-top:0">
    <h3>Buat Sesi Presensi</h3>

    @if ($daftar->isEmpty())
      <p style="margin:0; color:var(--text-muted)">Sesi presensi tidak dapat dibuat karena data perkuliahan belum tersedia.</p>
    @else
      <form method="POST" action="{{ route('presensi.buat') }}">
        @csrf
        <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude">

        @php
          $selectedMatkulId = old('idPerkuliahan', $daftar->first()->idPerkuliahan);
          $selectedMatkul   = $daftar->firstWhere('idPerkuliahan', $selectedMatkulId) ?? $daftar->first();
          $defaultPertemuan = old('pertemuan', $selectedMatkul->pertemuanBerikutnya ?? 1);
        @endphp

        <div class="form-grid-qr">
          <div>
            <label for="idPerkuliahan">Mata Kuliah &amp; Kelas</label>
            <select name="idPerkuliahan" id="idPerkuliahan" required>
              @foreach ($daftar as $p)
                <option value="{{ $p->idPerkuliahan }}"
                        data-next-pertemuan="{{ $p->pertemuanBerikutnya ?? 1 }}"
                        @selected($selectedMatkulId == $p->idPerkuliahan)>
                  {{ $p->kodeMatkul }} - {{ $p->namaMatkul }} (Kelas {{ $p->kelas }})
                </option>
              @endforeach
            </select>
          </div>

          <div>
            <label for="pertemuan">Pertemuan ke-</label>
            <select name="pertemuan" id="pertemuan" required>
              @for ($i = 1; $i <= 16; $i++)
                <option value="{{ $i }}" @selected($defaultPertemuan == $i)>Pertemuan {{ $i }}</option>
              @endfor
            </select>
            <small style="display:block; margin-top:5px; color:var(--text-muted)">
              Otomatis mengikuti urutan sesi.
            </small>
          </div>

          <div>
            <label for="tanggal">Tanggal Perkuliahan</label>
            <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', now()->format('Y-m-d')) }}" required>
            <small style="display:block; margin-top:5px; color:var(--text-muted)">
              Hari ini ({{ now()->locale('id')->translatedFormat('d M Y') }}).
            </small>
          </div>

          <div>
            <label for="radius">Radius Lokasi (meter)</label>
            <input type="number" name="radius" id="radius" value="{{ old('radius', 0) }}" min="0" max="1000" required>
            <small style="display:block; margin-top:5px; color:var(--text-muted)">
              Isi <strong>0</strong> tanpa validasi GPS.
            </small>
          </div>
        </div>

        <div class="row" style="margin-top:14px">
          <label for="catatan">Catatan / Materi Perkuliahan</label>
          <textarea name="catatan" id="catatan" rows="2" maxlength="450"
                    placeholder="Contoh: Pembahasan Normalisasi Database &amp; Praktikum ERD">{{ old('catatan') }}</textarea>
          <small style="display:block; margin-top:4px; color:var(--text-muted)">
            Opsional. Nomor pertemuan yang dipilih di atas akan otomatis dicantumkan di awal catatan.
          </small>
        </div>

        <div class="row" style="margin-top:14px; margin-bottom:0">
          <label>Durasi Sesi Presensi</label>
          <div class="radio-pill-group">
            @foreach ([10, 15, 30, 60] as $d)
              <label class="radio-pill">
                <input type="radio" name="durasi" value="{{ $d }}" {{ old('durasi', 15) == $d ? 'checked' : '' }}>
                <span>{{ $d }} menit</span>
              </label>
            @endforeach
          </div>
        </div>

        <div class="form-footer-bar">
          <span class="hint-text">
            <i data-lucide="info" style="width:15px;height:15px;color:var(--primary)"></i>
            Sesi baru tidak dapat dibuat jika mata kuliah yang sama masih memiliki sesi aktif.
          </span>
          <button type="submit" class="btn-primary">
            <i data-lucide="qr-code" style="width:16px;height:16px"></i> Buat Sesi Presensi
          </button>
        </div>
      </form>
    @endif
  </div>

  {{-- Sesi Aktif --}}
  @foreach ($sesiAktif as $s)
    @php
      $berakhir  = $s->waktuBerakhir();
      $persen    = $s->total > 0 ? round($s->hadir / $s->total * 100) : 0;
      $tglKuliah = ($s->tanggal ?? $s->waktuMulai)->locale('id')->translatedFormat('l, d M Y');
    @endphp
    <div class="card session-card">
      <div class="session-header">
        <div>
          <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap">
            <span class="badge st-Selesai">Sesi Aktif</span>
            <span class="timer-badge">
              <i data-lucide="clock" style="width:14px;height:14px"></i>
              Sisa waktu: <span class="sisa" data-akhir="{{ $berakhir->timestamp * 1000 }}">--:--</span>
            </span>
          </div>
          <h3>{{ $s->perkuliahan->namaMatkul }} (Kelas {{ $s->perkuliahan->kelas }})</h3>
        </div>

        <div class="session-actions">
          <form method="POST" action="{{ route('presensi.tutup', $s->idSesi) }}" style="margin:0"
                onsubmit="return confirm('Tutup sesi presensi ini? Mahasiswa yang belum scan akan tercatat Alpa.')">
            @csrf
            <button type="submit" class="btn-primary">
              <i data-lucide="lock" style="width:14px;height:14px"></i> Tutup Sesi
            </button>
          </form>

          <form method="POST" action="{{ route('presensi.batal', $s->idSesi) }}" style="margin:0"
                onsubmit="return confirm('Batalkan dan hapus sesi presensi ini? Data kehadiran sementara pada sesi ini akan ikut terhapus.')">
            @csrf
            <button type="submit" class="btn-hapus">
              <i data-lucide="trash-2" style="width:14px;height:14px"></i> Batalkan Sesi
            </button>
          </form>
        </div>
      </div>

      <div class="session-body-grid">
        {{-- Kolom Kiri: QR Code + Salin Kode --}}
        <div class="qr-box">
          <div class="qr-canvas-wrap">
            <div id="qr-{{ $s->idSesi }}" class="qr" data-kode="{{ $s->kodeQR }}"></div>
          </div>

          <div class="qr-code-copy-box">
            <span class="qr-code-text" title="{{ $s->kodeQR }}">{{ $s->kodeQR }}</span>
            <button type="button" class="btn-copy-qr" data-kode="{{ $s->kodeQR }}" onclick="salinKodeQR(this)">
              <i data-lucide="copy" style="width:13px;height:13px"></i>
              <span>Salin</span>
            </button>
          </div>

          <small style="margin-top:8px; color:var(--text-muted)">Scan QR atau bagikan kode di atas</small>
        </div>

        {{-- Kolom Kanan: Info, Catatan & Pemantauan Kehadiran --}}
        <div>
          <div class="meta-pills">
            <span class="meta-pill">
              <i data-lucide="calendar" style="width:14px;height:14px;color:var(--primary)"></i>
              {{ $tglKuliah }}
            </span>
            <span class="meta-pill">
              <i data-lucide="clock" style="width:14px;height:14px;color:var(--primary)"></i>
              {{ $s->waktuMulai->format('H:i') }} - {{ $berakhir->format('H:i') }} WIB ({{ $s->durasi }} mnt)
            </span>
            <span class="meta-pill">
              <i data-lucide="map-pin" style="width:14px;height:14px;color:var(--primary)"></i>
              Ruang {{ $s->perkuliahan->ruang }}
            </span>
            <span class="meta-pill">
              <i data-lucide="radar" style="width:14px;height:14px;color:var(--primary)"></i>
              {{ $s->radius > 0 ? 'Radius ' . $s->radius . ' m' : 'Tanpa GPS' }}
            </span>
          </div>

          @if ($s->catatan)
            <div class="notes-banner">
              <i data-lucide="notebook-pen" style="width:16px;height:16px;color:var(--primary);flex-shrink:0;margin-top:2px"></i>
              <div>
                <strong style="display:block; font-size:12.5px; color:var(--text-secondary)">Pertemuan &amp; Materi Perkuliahan:</strong>
                <span>{{ $s->catatan }}</span>
              </div>
            </div>
          @endif

          <div class="pantau-box pantau" data-url="{{ route('presensi.hadir', $s->idSesi) }}">
            <div class="pantau-head">
              <span>Kehadiran Real-time</span>
              <span>
                <strong class="jml">{{ $s->hadir }} / {{ $s->total }}</strong>
                <span style="color:var(--text-muted)">(<span class="persen">{{ $persen }}</span>%)</span>
              </span>
            </div>
            <div class="progress">
              <div class="progress-fill" data-w="{{ $persen }}"></div>
            </div>

            <ul class="daftar-hadir"></ul>
          </div>
        </div>
      </div>
    </div>
  @endforeach

  {{-- Riwayat Sesi Presensi (Sudah Selesai) --}}
  <div class="card" style="margin-top:20px">
    <div style="display:flex; justify-content:space-between; align-items:center; padding-bottom:12px; margin-bottom:14px; border-bottom:1px solid var(--border)">
      <h3 style="margin:0; padding:0; border:none">Riwayat Sesi Presensi</h3>
      <span style="font-size:13px; font-weight:600; color:var(--text-muted)">{{ $riwayatSesi->count() }} sesi terakhir</span>
    </div>

    @if ($riwayatSesi->isEmpty())
      <p style="margin:0; text-align:center; padding:24px 0; color:var(--text-muted); font-weight:600">
        Belum ada riwayat sesi presensi yang selesai.
      </p>
    @else
      <table>
        <thead>
          <tr>
            <th>Mata Kuliah</th>
            <th>Tanggal &amp; Jam</th>
            <th>Pertemuan &amp; Materi</th>
            <th style="text-align:center">Kehadiran</th>
            <th>Status</th>
            <th style="text-align:right">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($riwayatSesi as $r)
            @php
              $tglRiwayat = ($r->tanggal ?? $r->waktuMulai)->locale('id')->translatedFormat('D, d M Y');
              $jamSelesai = $r->waktuMulai->copy()->addMinutes($r->durasi)->format('H:i');
            @endphp
            <tr>
              <td>
                <strong>{{ $r->perkuliahan->namaMatkul }}</strong><br>
                <small style="color:var(--text-muted)">Kelas {{ $r->perkuliahan->kelas }} • Ruang {{ $r->perkuliahan->ruang }}</small>
              </td>
              <td style="white-space:nowrap">
                <strong>{{ $tglRiwayat }}</strong><br>
                <small style="color:var(--text-muted)">{{ $r->waktuMulai->format('H:i') }} - {{ $jamSelesai }} WIB</small>
              </td>
              <td style="max-width:260px">
                @if ($r->catatan)
                  <span style="font-size:13.5px; color:var(--text-main)">{{ $r->catatan }}</span>
                @else
                  <small style="color:var(--text-muted); font-style:italic">Tidak ada catatan</small>
                @endif
              </td>
              <td style="text-align:center">
                <strong>{{ $r->hadir }} / {{ $r->total }}</strong>
              </td>
              <td><span class="badge st-Selesai">{{ $r->status }}</span></td>
              <td style="text-align:right">
                <form method="POST" action="{{ route('presensi.batal', $r->idSesi) }}" style="margin:0; display:inline"
                      onsubmit="return confirm('Hapus riwayat sesi presensi ini? Seluruh data kehadiran mahasiswa pada sesi ini juga akan terhapus permanen.')">
                  @csrf
                  <button type="submit" class="btn-hapus" style="padding:5px 11px; font-size:12.5px">
                    <i data-lucide="trash-2" style="width:13px;height:13px"></i> Hapus
                  </button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>
@endsection

@push('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
  <script>
    // Otomatis sesuaikan Pertemuan ke- saat Mata Kuliah diganti
    var selectMatkul = document.getElementById('idPerkuliahan');
    var selectPertemuan = document.getElementById('pertemuan');
    if (selectMatkul && selectPertemuan) {
      selectMatkul.addEventListener('change', function () {
        var opt = this.options[this.selectedIndex];
        if (opt && opt.dataset.nextPertemuan) {
          selectPertemuan.value = opt.dataset.nextPertemuan;
        }
      });
    }

    // Salin kode QR langsung ke clipboard
    function salinKodeQR(btn) {
      var kode = btn.dataset.kode;
      var label = btn.querySelector('span');

      function tandaiBerhasil() {
        btn.classList.add('copied');
        label.textContent = 'Tersalin!';
        setTimeout(function () {
          btn.classList.remove('copied');
          label.textContent = 'Salin';
        }, 2000);
      }

      if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(kode).then(tandaiBerhasil);
      } else {
        var temp = document.createElement('textarea');
        temp.value = kode;
        temp.style.position = 'fixed';
        temp.style.opacity = '0';
        document.body.appendChild(temp);
        temp.select();
        document.execCommand('copy');
        document.body.removeChild(temp);
        tandaiBerhasil();
      }
    }

    // Inisialisasi lebar bar awal
    document.querySelectorAll('.progress-fill').forEach(function (el) {
      el.style.width = (el.dataset.w || 0) + '%';
    });

    // Ambil lokasi dosen (dipakai sebagai titik pusat radius)
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function (p) {
        document.getElementById('latitude').value  = p.coords.latitude;
        document.getElementById('longitude').value = p.coords.longitude;
      });
    }

    // Gambar QR Code untuk setiap sesi aktif
    document.querySelectorAll('.qr').forEach(function (el) {
      new QRCode(el, { text: el.dataset.kode, width: 210, height: 210 });
    });

    // Hitung mundur sisa waktu; saat habis, muat ulang halaman
    function hitungMundur() {
      document.querySelectorAll('.sisa').forEach(function (el) {
        var sisa = Math.floor((Number(el.dataset.akhir) - Date.now()) / 1000);
        if (sisa <= 0) { location.reload(); return; }
        el.textContent = String(Math.floor(sisa / 60)).padStart(2, '0') + ':' + String(sisa % 60).padStart(2, '0');
      });
    }
    hitungMundur();
    setInterval(hitungMundur, 1000);

    // Perbarui daftar seluruh mahasiswa & status real-time setiap 3 detik
    document.querySelectorAll('.pantau').forEach(function (el) {
      function muat() {
        fetch(el.dataset.url, { headers: { 'Accept': 'application/json' } })
          .then(function (r) { return r.json(); })
          .then(function (d) {
            var persen = d.total ? Math.round(d.hadir / d.total * 100) : 0;
            el.querySelector('.jml').textContent = d.hadir + ' / ' + d.total;
            el.querySelector('.persen').textContent = persen;
            el.querySelector('.progress-fill').style.width = persen + '%';

            var ul = el.querySelector('.daftar-hadir');
            ul.innerHTML = '';

            if (d.daftar.length === 0) {
              var empty = document.createElement('div');
              empty.className = 'daftar-hadir-empty';
              empty.textContent = 'Belum ada mahasiswa yang terdaftar pada kelas ini.';
              ul.appendChild(empty);
            } else {
              d.daftar.forEach(function (m, idx) {
                var li = document.createElement('li');
                if (m.status === 'Belum Presensi') {
                  li.className = 'belum';
                }

                var leftDiv = document.createElement('div');
                leftDiv.className = 'mhs-left';

                var numSpan = document.createElement('span');
                numSpan.className = 'mhs-num';
                numSpan.textContent = idx + 1;

                var namaSpan = document.createElement('span');
                namaSpan.className = 'mhs-nama';
                namaSpan.textContent = m.nama;

                leftDiv.appendChild(numSpan);
                leftDiv.appendChild(namaSpan);

                var rightDiv = document.createElement('div');
                rightDiv.className = 'mhs-right';

                if (m.waktu) {
                  var waktuSpan = document.createElement('span');
                  waktuSpan.className = 'hadir-time';
                  waktuSpan.textContent = m.waktu;
                  rightDiv.appendChild(waktuSpan);
                }

                var statusSpan = document.createElement('span');
                var statusClass = 'belum';
                if (m.status === 'Hadir') statusClass = 'hadir';
                else if (m.status === 'Izin') statusClass = 'izin';
                else if (m.status === 'Sakit') statusClass = 'sakit';
                else if (m.status === 'Alpa') statusClass = 'alpa';

                statusSpan.className = 'status-pill ' + statusClass;
                statusSpan.textContent = m.status;
                rightDiv.appendChild(statusSpan);

                li.appendChild(leftDiv);
                li.appendChild(rightDiv);
                ul.appendChild(li);
              });
            }
          });
      }
      muat();
      setInterval(muat, 3000);
    });
  </script>
@endpush