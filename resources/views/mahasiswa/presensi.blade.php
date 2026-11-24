@extends('layouts.app')

@section('judul', 'Presensi Pindai QR Code')

@section('isi')
<style>
  .page-subtitle {
    margin: 0 0 18px;
    color: var(--text-secondary);
    font-size: 14.5px;
  }

  /* Grid 2 Kolom: Pemindai Kamera & Panel Sesi/Manual */
  .scan-layout-grid {
    display: grid;
    grid-template-columns: 1.15fr 1fr;
    gap: 18px;
    align-items: start;
  }
  .scan-layout-grid .card {
    margin-top: 0;
  }

  /* Status Lokasi Pill */
  .location-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 10px 14px;
    background: var(--bg-subtle);
    border: 1px solid var(--border);
    border-radius: 8px;
    margin-bottom: 16px;
    font-size: 13.5px;
  }
  .location-label {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: var(--text-secondary);
    font-weight: 600;
  }

  /* Kotak Viewfinder Kamera */
  .scanner-stage {
    background: #0f172a;
    border-radius: 10px;
    padding: 16px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 290px;
    position: relative;
    overflow: hidden;
  }
  #reader {
    width: 100%;
    max-width: 320px;
    border-radius: 8px;
    overflow: hidden;
  }
  .scanner-controls {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 16px;
  }

  /* Daftar Sesi Aktif */
  .session-pill-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 18px;
  }
  .session-mini-card {
    padding: 12px 14px;
    border: 1px solid var(--border);
    border-left: 4px solid var(--primary);
    border-radius: 8px;
    background: var(--bg-subtle);
  }
  .session-mini-card.done {
    border-left-color: var(--success);
    background: #f0fdf4;
  }
  .session-mini-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
    margin-bottom: 5px;
    flex-wrap: wrap;
  }
  .timer-chip {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 12.5px;
    font-weight: 700;
    color: var(--primary-hover);
    background: #ffffff;
    border: 1px solid #bfdbfe;
    padding: 2px 8px;
    border-radius: 99px;
  }
  .session-mini-title {
    font-weight: 700;
    font-size: 14.5px;
    color: var(--text-main);
  }
  .session-mini-meta {
    font-size: 12.5px;
    color: var(--text-secondary);
    margin-top: 2px;
  }

  /* Input Manual Card */
  .manual-card {
    margin-top: 18px;
    border-top: 3px solid var(--border);
  }
  .empty-box {
    text-align: center;
    padding: 44px 20px;
    color: var(--text-muted);
  }
  .empty-box h4 {
    margin: 10px 0 4px;
    color: var(--text-main);
    font-size: 16px;
  }

  @media (max-width: 920px) {
    .scan-layout-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

  <p class="page-subtitle">Pilih sesi perkuliahan yang aktif, lalu arahkan kamera perangkat ke QR Code yang ditampilkan oleh dosen.</p>

  @if ($daftarSesi->isEmpty())
    <div class="card" style="margin-top:0">
      <div class="empty-box">
        <i data-lucide="qr-code" style="width:36px;height:36px;color:var(--text-muted)"></i>
        <h4>Sesi presensi tidak dapat digunakan</h4>
        <p style="margin:0; font-size:14px">Belum ada sesi presensi aktif pada perkuliahan yang kamu ikuti saat ini.</p>
      </div>
    </div>
  @else
    <div class="scan-layout-grid">
      {{-- Kolom Kiri: Pemindai Kamera --}}
      <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; padding-bottom:12px; margin-bottom:16px; border-bottom:1px solid var(--border)">
          <h3 style="margin:0; padding:0; border:none">Pemindai Kamera QR</h3>
          <span class="badge st-Selesai">Siap Pindai</span>
        </div>

        <form method="POST" action="{{ route('presensi.kirim') }}" id="formPresensi">
          @csrf
          <input type="hidden" name="latitude"  id="latitude">
          <input type="hidden" name="longitude" id="longitude">
          <input type="hidden" name="kodeQR"    id="kodeQR">

          {{-- Pilih Sesi Perkuliahan --}}
          <div class="row">
            <label for="idSesi">Sesi Perkuliahan</label>
            <select name="idSesi" id="idSesi" required>
              @foreach ($daftarSesi as $s)
                <option value="{{ $s->idSesi }}" @disabled($s->sudahPresensi)>
                  {{ $s->perkuliahan->namaMatkul }} (Kelas {{ $s->perkuliahan->kelas }})
                  @if ($s->sudahPresensi) — Sudah Presensi @endif
                </option>
              @endforeach
            </select>
          </div>

          {{-- Status GPS --}}
          <div class="location-bar">
            <span class="location-label">
              <i data-lucide="map-pin" style="width:15px;height:15px;color:var(--primary)"></i> Status Lokasi GPS
            </span>
            <strong id="statusLokasi" style="color:var(--text-main)">Mendeteksi lokasi...</strong>
          </div>

          {{-- Area Kamera --}}
          <div class="scanner-stage">
            <div id="reader"></div>
          </div>

          <div class="scanner-controls">
            <button type="button" class="btn-primary" id="btnPindai">
              <i data-lucide="camera" style="width:16px;height:16px"></i> Mulai Pindai Kamera
            </button>
            <button type="button" class="btn-hapus" id="btnStop" style="display:none">
              <i data-lucide="camera-off" style="width:16px;height:16px"></i> Hentikan Kamera
            </button>
          </div>
        </form>
      </div>

      {{-- Kolom Kanan: Info Sesi Aktif & Input Manual --}}
      <div>
        <div class="card" style="margin-top:0">
          <h3 style="margin-bottom:14px">Sesi Aktif Saat Ini</h3>
          <div class="session-pill-list" style="margin-bottom:0">
            @foreach ($daftarSesi as $s)
              <div class="session-mini-card {{ $s->sudahPresensi ? 'done' : '' }}">
                <div class="session-mini-top">
                  @if ($s->sudahPresensi)
                    <span class="badge st-Hadir">Sudah Presensi</span>
                  @else
                    <span class="badge st-Selesai">Sesi Aktif</span>
                  @endif
                  <span class="timer-chip">
                    <i data-lucide="clock" style="width:12px;height:12px"></i>
                    Sisa <span class="sisa" data-akhir="{{ $s->waktuBerakhir()->timestamp * 1000 }}">--:--</span>
                  </span>
                </div>
                <div class="session-mini-title">{{ $s->perkuliahan->namaMatkul }} (Kelas {{ $s->perkuliahan->kelas }})</div>
                <div class="session-mini-meta">
                  Radius: {{ $s->radius > 0 ? $s->radius . ' meter' : 'Tanpa validasi lokasi' }}
                </div>
              </div>
            @endforeach
          </div>
        </div>

        {{-- Input Manual Cadangan --}}
        <div class="card manual-card">
          <h3>Input Kode Manual</h3>
          <p style="margin:0 0 12px; font-size:13.5px; color:var(--text-secondary)">
            Gunakan kolom ini apabila kamera perangkat bermasalah atau izin kamera diblokir browser.
          </p>

          <form method="POST" action="{{ route('presensi.kirim') }}" id="formManual">
            @csrf
            <input type="hidden" name="latitude"  id="latitude2">
            <input type="hidden" name="longitude" id="longitude2">
            <input type="hidden" name="idSesi"    id="idSesi2">

            <div class="row">
              <label for="kodeManual">Kode QR Sesi</label>
              <input type="text" name="kodeQR" id="kodeManual" placeholder="Contoh: QR-1-20261124-AbCd1234" required>
            </div>
            <button type="submit" class="btn-primary" style="width:100%; justify-content:center">
              <i data-lucide="send" style="width:15px;height:15px"></i> Kirim Presensi Manual
            </button>
          </form>
        </div>
      </div>
    </div>
  @endif
@endsection

@push('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
  <script>
    var formUtama = document.getElementById('formPresensi');
    if (formUtama) {

      // ---- lokasi mahasiswa ----
      function isiLokasi(lat, lng) {
        ['latitude', 'latitude2'].forEach(function (id) { document.getElementById(id).value = lat; });
        ['longitude', 'longitude2'].forEach(function (id) { document.getElementById(id).value = lng; });
      }
      var statusEl = document.getElementById('statusLokasi');
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (p) {
          isiLokasi(p.coords.latitude, p.coords.longitude);
          statusEl.textContent = 'Lokasi terdeteksi';
          statusEl.style.color = 'var(--success)';
        }, function () {
          statusEl.textContent = 'Lokasi tidak terdeteksi';
          statusEl.style.color = 'var(--danger)';
        });
      } else {
        statusEl.textContent = 'Browser tidak mendukung lokasi';
        statusEl.style.color = 'var(--danger)';
      }

      // sinkronkan pilihan sesi ke form manual
      var pilih = document.getElementById('idSesi');
      function samakanSesi() { document.getElementById('idSesi2').value = pilih.value; }
      pilih.addEventListener('change', samakanSesi);
      samakanSesi();

      // ---- pemindai kamera ----
      var scanner = new Html5Qrcode('reader');
      var btnPindai = document.getElementById('btnPindai');
      var btnStop   = document.getElementById('btnStop');

      btnPindai.addEventListener('click', function () {
        scanner.start(
          { facingMode: 'environment' },
          { fps: 10, qrbox: 220 },
          function (hasil) {                         // QR berhasil terbaca
            scanner.stop().then(function () {
              document.getElementById('kodeQR').value = hasil;
              formUtama.submit();
            });
          }
        ).then(function () {
          btnPindai.style.display = 'none';
          btnStop.style.display = 'inline-flex';
        }).catch(function () {
          alert('Kamera tidak bisa diakses. Izinkan akses kamera, atau pakai input kode manual di samping.');
        });
      });

      btnStop.addEventListener('click', function () {
        scanner.stop().then(function () {
          btnPindai.style.display = 'inline-flex';
          btnStop.style.display = 'none';
        });
      });

      // ---- hitung mundur sisa waktu ----
      function hitungMundur() {
        document.querySelectorAll('.sisa').forEach(function (el) {
          var sisa = Math.floor((Number(el.dataset.akhir) - Date.now()) / 1000);
          if (sisa <= 0) { location.reload(); return; }
          el.textContent = String(Math.floor(sisa / 60)).padStart(2, '0') + ':' + String(sisa % 60).padStart(2, '0');
        });
      }
      hitungMundur();
      setInterval(hitungMundur, 1000);
    }
  </script>
@endpush