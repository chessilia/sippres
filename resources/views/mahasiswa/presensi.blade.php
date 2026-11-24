@extends('layouts.app')

@section('judul', 'Presensi Pindai QR Code')

@section('isi')
  <p>Pilih sesi perkuliahan, lalu arahkan kamera ke QR Code yang ditampilkan dosen.</p>

  <div class="card">
    @if ($daftarSesi->isEmpty())
      <p>Sesi presensi tidak dapat digunakan</p>
      <small>Belum ada sesi presensi aktif pada perkuliahan yang kamu ikuti.</small>
    @else
      <form method="POST" action="{{ route('presensi.kirim') }}" id="formPresensi">
        @csrf
        <input type="hidden" name="latitude"  id="latitude">
        <input type="hidden" name="longitude" id="longitude">
        <input type="hidden" name="kodeQR"    id="kodeQR">

        {{-- Sesi Perkuliahan - Dropdown --}}
        <div class="row">
          <label for="idSesi">Sesi Perkuliahan</label>
          <select name="idSesi" id="idSesi" required>
            @foreach ($daftarSesi as $s)
              <option value="{{ $s->idSesi }}" @disabled($s->sudahPresensi)>
                {{ $s->perkuliahan->namaMatkul }} (Kelas {{ $s->perkuliahan->kelas }})
                @if ($s->sudahPresensi) - sudah presensi @endif
              </option>
            @endforeach
          </select>
        </div>

        {{-- Sisa Waktu + Status Sesi --}}
        @foreach ($daftarSesi as $s)
          <p>
            <span class="badge">Sesi Aktif</span>
            {{ $s->perkuliahan->namaMatkul }}: sisa waktu
            <strong class="sisa" data-akhir="{{ $s->waktuBerakhir()->timestamp * 1000 }}">...</strong>
            | Radius {{ $s->radius > 0 ? $s->radius . ' m' : 'tanpa validasi lokasi' }}
          </p>
        @endforeach

        {{-- Status Lokasi --}}
        <p>Status lokasi: <strong id="statusLokasi">mendeteksi lokasi...</strong></p>

        {{-- Area Kamera --}}
        <div id="reader" style="width:300px; max-width:100%;"></div>
        <br>
        <button type="button" class="btn-primary" id="btnPindai">Mulai Pindai</button>
        <button type="button" class="btn-batal" id="btnStop" style="display:none">Hentikan Kamera</button>
      </form>

      {{-- Input manual: cadangan jika kamera tidak tersedia --}}
      <hr>
      <form method="POST" action="{{ route('presensi.kirim') }}" id="formManual">
        @csrf
        <input type="hidden" name="latitude"  id="latitude2">
        <input type="hidden" name="longitude" id="longitude2">
        <input type="hidden" name="idSesi"    id="idSesi2">

        <div class="row">
          <label for="kodeManual">Kamera tidak bisa dipakai? Masukkan kode QR manual</label>
          <input type="text" name="kodeQR" id="kodeManual" placeholder="Contoh: QR-1-20261124-AbCd1234" required>
        </div>
        <button type="submit" class="btn-primary">Kirim Presensi</button>
      </form>
    @endif
  </div>
@endsection

@push('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
  <script>
    var formUtama  = document.getElementById('formPresensi');
    if (formUtama) {

      // ---- lokasi mahasiswa ----
      function isiLokasi(lat, lng) {
        ['latitude', 'latitude2'].forEach(function (id) { document.getElementById(id).value = lat; });
        ['longitude', 'longitude2'].forEach(function (id) { document.getElementById(id).value = lng; });
      }
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (p) {
          isiLokasi(p.coords.latitude, p.coords.longitude);
          document.getElementById('statusLokasi').textContent = 'lokasi terdeteksi';
        }, function () {
          document.getElementById('statusLokasi').textContent = 'lokasi tidak terdeteksi';
        });
      } else {
        document.getElementById('statusLokasi').textContent = 'browser tidak mendukung lokasi';
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
          btnStop.style.display = 'inline-block';
        }).catch(function () {
          alert('Kamera tidak bisa diakses. Izinkan akses kamera, atau pakai input kode manual di bawah.');
        });
      });

      btnStop.addEventListener('click', function () {
        scanner.stop().then(function () {
          btnPindai.style.display = 'inline-block';
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