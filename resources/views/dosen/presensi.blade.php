@extends('layouts.app')

@section('judul', 'Presensi QR Code')

@section('isi')
  <p>Buat sesi, tampilkan QR Code ke mahasiswa, lalu tutup sesi setelah selesai.</p>

  {{-- Form Buat Sesi --}}
  <div class="card">
    <h3>Buat Sesi Presensi</h3>

    @if ($daftar->isEmpty())
      <p>Sesi presensi tidak dapat dibuat, data perkuliahan belum tersedia</p>
    @else
      <form method="POST" action="{{ route('presensi.buat') }}">
        @csrf
        <input type="hidden" name="latitude"  id="latitude">
        <input type="hidden" name="longitude" id="longitude">

        <div class="row">
          <label for="idPerkuliahan">Perkuliahan</label>
          <select name="idPerkuliahan" id="idPerkuliahan" required>
            @foreach ($daftar as $p)
              <option value="{{ $p->idPerkuliahan }}" @selected(old('idPerkuliahan') == $p->idPerkuliahan)>
                {{ $p->kodeMatkul }} - {{ $p->namaMatkul }} (Kelas {{ $p->kelas }})
              </option>
            @endforeach
          </select>
        </div>

        <div class="row">
          <label>Durasi sesi</label>
          @foreach ([10, 15, 30, 60] as $d)
            <label style="display:inline; font-weight:normal; margin-right:12px">
              <input type="radio" name="durasi" value="{{ $d }}" {{ $d == 15 ? 'checked' : '' }}> {{ $d }} mnt
            </label>
          @endforeach
        </div>

        <div class="row">
          <label for="radius">Radius lokasi presensi (meter)</label>
          <input type="number" name="radius" id="radius" value="{{ old('radius', 0) }}" min="0" max="1000" required>
          <small>Isi 0 untuk tanpa validasi lokasi. Kalau diisi, izinkan akses lokasi di browser.</small>
        </div>

        <p><small>Sesi baru tidak dapat dibuat jika perkuliahan yang sama masih memiliki sesi aktif.</small></p>
        <button type="submit" class="btn-primary">Buat Sesi Presensi</button>
      </form>
    @endif
  </div>

  {{-- Sesi Aktif --}}
  @foreach ($sesiAktif as $s)
    @php
      $berakhir = $s->waktuMulai->copy()->addMinutes($s->durasi);
      $persen   = $s->total > 0 ? round($s->hadir / $s->total * 100) : 0;
    @endphp
    <div class="card">
      <span class="badge">Sesi Aktif</span>
      Sisa waktu: <strong class="sisa" data-akhir="{{ $berakhir->timestamp * 1000 }}">...</strong>

      <h3>{{ $s->perkuliahan->namaMatkul }} (Kelas {{ $s->perkuliahan->kelas }})</h3>

      <div id="qr-{{ $s->idSesi }}" class="qr" data-kode="{{ $s->kodeQR }}"></div>
      <small>Kode: {{ $s->kodeQR }}</small>

      <p>Ruang {{ $s->perkuliahan->ruang }} | Durasi {{ $s->durasi }} mnt |
         Radius {{ $s->radius > 0 ? $s->radius . ' m' : 'tanpa validasi lokasi' }}</p>
      <p>Dimulai: {{ $s->waktuMulai->format('H:i') }}</p>
      <p>Mahasiswa hadir: {{ $s->hadir }} / {{ $s->total }} ({{ $persen }}%)</p>
      <progress value="{{ $s->hadir }}" max="{{ max($s->total, 1) }}" style="width:100%"></progress>
      <br>

      <form method="POST" action="{{ route('presensi.tutup', $s->idSesi) }}"
            onsubmit="return confirm('Tutup sesi presensi ini?')">
        @csrf
        <button type="submit" class="btn-hapus">Tutup Sesi</button>
      </form>
    </div>
  @endforeach
@endsection

@push('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
  <script>
    // ambil lokasi dosen (dipakai sebagai titik pusat radius)
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function (p) {
        document.getElementById('latitude').value  = p.coords.latitude;
        document.getElementById('longitude').value = p.coords.longitude;
      });
    }

    // gambar QR Code untuk setiap sesi aktif
    document.querySelectorAll('.qr').forEach(function (el) {
      new QRCode(el, { text: el.dataset.kode, width: 220, height: 220 });
    });

    // hitung mundur sisa waktu; saat habis, muat ulang halaman
    function hitungMundur() {
      document.querySelectorAll('.sisa').forEach(function (el) {
        var sisa = Math.floor((Number(el.dataset.akhir) - Date.now()) / 1000);
        if (sisa <= 0) { location.reload(); return; }
        var m = String(Math.floor(sisa / 60)).padStart(2, '0');
        var d = String(sisa % 60).padStart(2, '0');
        el.textContent = m + ':' + d;
      });
    }
    hitungMundur();
    setInterval(hitungMundur, 1000);
  </script>
@endpush