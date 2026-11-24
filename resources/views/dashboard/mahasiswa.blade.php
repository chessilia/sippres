@extends('layouts.app')

@section('judul', 'Dashboard Mahasiswa')

@section('isi')
  <div class="card">
    <h3>Hi, {{ Auth::user()->nama }}!</h3>
    <p>
      @if ($presensiHariIni > 0)
        Kamu sudah melakukan presensi hari ini ({{ $presensiHariIni }} kali).
      @else
        Belum ada presensi hari ini.
      @endif
    </p>
  </div>

  {{-- Sesi aktif --}}
  @forelse ($sesiAktif as $s)
    <div class="card">
      <span class="badge st-Selesai">Sesi Aktif</span>
      Sisa waktu: <strong class="sisa" data-akhir="{{ $s->waktuBerakhir()->timestamp * 1000 }}">...</strong>
      <h3>{{ $s->perkuliahan->namaMatkul }} (Kelas {{ $s->perkuliahan->kelas }})</h3>
      <p>Ruang {{ $s->perkuliahan->ruang }} | Dimulai {{ $s->waktuMulai->format('H:i') }}
         | Batas presensi {{ $s->waktuBerakhir()->format('H:i') }}</p>
      @if ($s->sudahPresensi)
        <p><em>Kamu sudah presensi pada sesi ini.</em></p>
      @else
        <a class="btn btn-primary" href="{{ route('presensi.mahasiswa') }}">Pindai QR Code</a>
      @endif
    </div>
  @empty
    <div class="card"><p>Tidak ada sesi presensi yang sedang dibuka.</p></div>
  @endforelse

  {{-- Ringkasan kehadiran --}}
  <div class="grid4">
    <div class="stat">Hadir <b>{{ $ringkasan['Hadir'] }}</b></div>
    <div class="stat">Izin <b>{{ $ringkasan['Izin'] }}</b></div>
    <div class="stat">Sakit <b>{{ $ringkasan['Sakit'] }}</b></div>
    <div class="stat">Alpa <b>{{ $ringkasan['Alpa'] }}</b></div>
  </div>

  <div class="card">
    <a class="btn btn-primary" href="{{ route('izin.mahasiswa') }}">Ajukan Izin</a>
    <a class="btn btn-batal" href="{{ route('crisis.pengguna') }}">Lapor Kendala</a>
  </div>

  {{-- Kuliah berikutnya --}}
  <div class="card">
    <h3>Kuliah Berikutnya <a href="{{ route('jadwal') }}" style="font-size:14px">Lihat semua</a></h3>
    @forelse ($berikutnya as $p)
      <p>
        <strong>{{ $p->selisihHari === 0 ? 'Hari ini' : $p->hari }}</strong>,
        {{ substr($p->jamMulai, 0, 5) }} - {{ substr($p->jamSelesai, 0, 5) }}
        | {{ $p->namaMatkul }} | Ruang {{ $p->ruang }}
        <span class="badge {{ $p->aktif ? 'st-Selesai' : 'st-Belum' }}">{{ $p->aktif ? 'Aktif' : 'Nanti' }}</span>
      </p>
    @empty
      <p>Belum ada perkuliahan.</p>
    @endforelse
  </div>
@endsection

@push('scripts')
  <script>
    function hitungMundur() {
      document.querySelectorAll('.sisa').forEach(function (el) {
        var sisa = Math.floor((Number(el.dataset.akhir) - Date.now()) / 1000);
        if (sisa <= 0) { location.reload(); return; }
        el.textContent = String(Math.floor(sisa / 60)).padStart(2, '0') + ':' + String(sisa % 60).padStart(2, '0');
      });
    }
    hitungMundur();
    setInterval(hitungMundur, 1000);
  </script>
@endpush