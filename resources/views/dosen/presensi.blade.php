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
    grid-template-columns: 1.4fr 1fr;
    gap: 16px 20px;
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
  .qr-code-text {
    margin-top: 10px;
    font-family: monospace;
    font-size: 13px;
    font-weight: 700;
    color: var(--text-secondary);
    background: #ffffff;
    padding: 4px 10px;
    border-radius: 6px;
    border: 1px solid var(--border);
    word-break: break-all;
  }

  .meta-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 16px;
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
    max-height: 220px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 6px;
  }
  .daftar-hadir li {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #ffffff;
    border: 1px solid var(--border);
    padding: 8px 12px;
    border-radius: 7px;
    font-size: 13.5px;
    font-weight: 600;
  }
  .daftar-hadir li .hadir-time {
    font-size: 12.5px;
    color: var(--text-muted);
    font-weight: 700;
  }
  .daftar-hadir-empty {
    text-align: center;
    color: var(--text-muted);
    font-size: 13.5px;
    padding: 16px 0 4px;
  }

  @media (max-width: 860px) {
    .form-grid-qr { grid-template-columns: 1fr; }
    .session-body-grid { grid-template-columns: 1fr; }
  }
</style>

  <p class="page-subtitle">Buat sesi presensi, tampilkan QR Code kepada mahasiswa, lalu tutup sesi setelah perkuliahan selesai.</p>

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

        <div class="form-grid-qr">
          <div>
            <label for="idPerkuliahan">Mata Kuliah &amp; Kelas</label>
            <select name="idPerkuliahan" id="idPerkuliahan" required>
              @foreach ($daftar as $p)
                <option value="{{ $p->idPerkuliahan }}" @selected(old('idPerkuliahan') == $p->idPerkuliahan)>
                  {{ $p->kodeMatkul }} - {{ $p->namaMatkul }} (Kelas {{ $p->kelas }})
                </option>
              @endforeach
            </select>
          </div>

          <div>
            <label for="radius">Radius Lokasi Presensi (meter)</label>
            <input type="number" name="radius" id="radius" value="{{ old('radius', 0) }}" min="0" max="1000" required>
            <small style="display:block; margin-top:5px; color:var(--text-muted)">
              Isi <strong>0</strong> tanpa validasi GPS. Jika diisi &gt; 0, izinkan akses lokasi di browser.
            </small>
          </div>
        </div>

        <div class="row" style="margin-top:14px; margin-bottom:0">
          <label>Durasi Sesi Presensi</label>
          <div class="radio-pill-group">
            @foreach ([10, 15, 30, 60] as $d)
              <label class="radio-pill">
                <input type="radio" name="durasi" value="{{ $d }}" {{ $d == 15 ? 'checked' : '' }}>
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
      $berakhir = $s->waktuBerakhir();
      $persen   = $s->total > 0 ? round($s->hadir / $s->total * 100) : 0;
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
        {{-- Kolom Kiri: QR Code --}}
        <div class="qr-box">
          <div class="qr-canvas-wrap">
            <div id="qr-{{ $s->idSesi }}" class="qr" data-kode="{{ $s->kodeQR }}"></div>
          </div>
          <div class="qr-code-text">{{ $s->kodeQR }}</div>
          <small style="margin-top:8px; color:var(--text-muted)">Arahkan kamera mahasiswa ke QR Code ini</small>
        </div>

        {{-- Kolom Kanan: Info & Pemantauan Kehadiran --}}
        <div>
          <div class="meta-pills">
            <span class="meta-pill">
              <i data-lucide="map-pin" style="width:14px;height:14px;color:var(--primary)"></i>
              Ruang {{ $s->perkuliahan->ruang }}
            </span>
            <span class="meta-pill">
              <i data-lucide="play-circle" style="width:14px;height:14px;color:var(--primary)"></i>
              Mulai {{ $s->waktuMulai->format('H:i') }} ({{ $s->durasi }} mnt)
            </span>
            <span class="meta-pill">
              <i data-lucide="radar" style="width:14px;height:14px;color:var(--primary)"></i>
              Radius: {{ $s->radius > 0 ? $s->radius . ' m' : 'Tanpa validasi lokasi' }}
            </span>
          </div>

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
            <th>Waktu Sesi</th>
            <th>Kode QR</th>
            <th style="text-align:center">Kehadiran</th>
            <th>Status</th>
            <th style="text-align:right">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($riwayatSesi as $r)
            <tr>
              <td>
                <strong>{{ $r->perkuliahan->namaMatkul }}</strong><br>
                <small style="color:var(--text-muted)">Kelas {{ $r->perkuliahan->kelas }} • Ruang {{ $r->perkuliahan->ruang }}</small>
              </td>
              <td style="white-space:nowrap">
                {{ $r->waktuMulai->format('d/m/Y H:i') }}<br>
                <small style="color:var(--text-muted)">Durasi {{ $r->durasi }} menit</small>
              </td>
              <td>
                <code style="font-size:12.5px; background:var(--bg-subtle); padding:3px 7px; border-radius:5px; border:1px solid var(--border)">
                  {{ $r->kodeQR }}
                </code>
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

    // Perbarui daftar mahasiswa hadir setiap 3 detik
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
              empty.textContent = 'Belum ada mahasiswa yang melakukan presensi.';
              ul.appendChild(empty);
            } else {
              d.daftar.forEach(function (m) {
                var li = document.createElement('li');
                var namaSpan = document.createElement('span');
                namaSpan.textContent = m.nama;
                var waktuSpan = document.createElement('span');
                waktuSpan.className = 'hadir-time';
                waktuSpan.textContent = m.waktu;
                li.appendChild(namaSpan);
                li.appendChild(waktuSpan);
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