@extends('layouts.app')

@section('judul', 'Dashboard Dosen')

@section('isi')
  <div class="card">
    <h3>Hi, {{ Auth::user()->nama }}!</h3>
    <p>
      @if ($jadwalHariIni->isEmpty())
        Tidak ada jadwal mengajar hari ini.
      @else
        Kamu mengajar {{ $jadwalHariIni->count() }} perkuliahan hari ini.
      @endif
    </p>
  </div>

  {{-- Sesi presensi aktif --}}
  @forelse ($sesiAktif as $s)
    <div class="card">
      <span class="badge st-Selesai">Sesi Aktif</span>
      Sisa waktu: <strong class="sisa" data-akhir="{{ $s->waktuBerakhir()->timestamp * 1000 }}">...</strong>
      <h3>{{ $s->perkuliahan->namaMatkul }} (Kelas {{ $s->perkuliahan->kelas }})</h3>
      <p>Ruang {{ $s->perkuliahan->ruang }} | Mahasiswa hadir: {{ $s->hadir }} / {{ $s->total }}</p>
      <a class="btn btn-primary" href="{{ route('presensi.dosen') }}">Kelola Sesi</a>
    </div>
  @empty
    <div class="card">
      <p>Tidak ada sesi presensi aktif.</p>
      <a class="btn btn-primary" href="{{ route('presensi.dosen') }}">Buat Sesi Presensi</a>
    </div>
  @endforelse

  <div class="grid4">
    <div class="stat">Izin menunggu <b>{{ $izinMenunggu->count() }}</b>
      <a href="{{ route('izin.dosen') }}">Proses pengajuan</a></div>
    <div class="stat">Rata-rata kehadiran <b>{{ $rata === null ? '-' : $rata . '%' }}</b>
      @if ($rata !== null) <progress value="{{ $rata }}" max="100"></progress> @endif
      <a href="{{ route('rekap.dosen') }}">Lihat rekap</a></div>
  </div>

  {{-- Jadwal hari ini --}}
  <div class="card">
    <h3>Jadwal Mengajar Hari Ini</h3>
    @forelse ($jadwalHariIni as $p)
      <p>
        {{ substr($p->jamMulai, 0, 5) }} - {{ substr($p->jamSelesai, 0, 5) }}
        | {{ $p->namaMatkul }} (Kelas {{ $p->kelas }}) | Ruang {{ $p->ruang }}
        <span class="badge {{ $p->sesiAktif ? 'st-Selesai' : 'st-Belum' }}">
          {{ $p->sesiAktif ? 'Sesi aktif' : 'Belum dibuka' }}</span>
      </p>
    @empty
      <p>Tidak ada jadwal hari ini.</p>
    @endforelse
  </div>

  {{-- Pengajuan izin terbaru --}}
  <div class="card">
    <h3>Pengajuan Izin Terbaru</h3>
    @forelse ($izinMenunggu->take(3) as $z)
      <p>
        <strong>{{ $z->mahasiswa->nama }}</strong> | {{ $z->perkuliahan->namaMatkul }}, pertemuan {{ $z->pertemuan }}
        <br><small>{{ \Illuminate\Support\Str::limit($z->alasan, 80) }}</small>
        <a href="{{ route('izin.dosen', ['id' => $z->idIzin]) }}">Tinjau</a>
      </p>
    @empty
      <p>Tidak ada pengajuan yang menunggu.</p>
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