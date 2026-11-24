@extends('layouts.app')

@section('judul', 'Ajukan Izin')

@section('isi')
<style>
  .page-subtitle {
    margin: 0 0 18px;
    color: var(--text-secondary);
    font-size: 14.5px;
  }

  /* Grid Utama: Form Pengajuan & Riwayat Pengajuan */
  .izin-mhs-grid {
    display: grid;
    grid-template-columns: 1fr 1.35fr;
    gap: 18px;
    align-items: start;
  }
  .izin-mhs-grid .card {
    margin-top: 0;
  }
  .izin-mhs-grid .card.is-editing {
    border-top: 4px solid var(--primary);
  }

  .form-row-2 {
    display: grid;
    grid-template-columns: 1.7fr 1fr;
    gap: 14px;
    margin-bottom: 14px;
  }

  .file-upload-box {
    border: 1px dashed #cbd5e1;
    border-radius: 8px;
    padding: 12px 14px;
    background: var(--bg-subtle);
  }
  .file-upload-box input[type="file"] {
    width: 100%;
    font-size: 13.5px;
  }
  .file-hint {
    display: block;
    margin-top: 6px;
    font-size: 12.5px;
    color: var(--text-muted);
  }

  .form-footer-bar {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 8px;
    margin-top: 18px;
    padding-top: 14px;
    border-top: 1px solid var(--border);
  }

  .row-editing {
    background: #eff6ff !important;
  }

  .action-group {
    display: inline-flex;
    align-items: center;
    justify-content: flex-end;
    flex-wrap: wrap;
    gap: 6px;
  }
  .empty-box {
    text-align: center;
    padding: 36px 16px;
    color: var(--text-muted);
    font-weight: 600;
    font-size: 14px;
  }

  @media (max-width: 1024px) {
    .izin-mhs-grid {
      grid-template-columns: 1fr;
    }
  }
  @media (max-width: 540px) {
    .form-row-2 {
      grid-template-columns: 1fr;
    }
  }
</style>

  <p class="page-subtitle">Ajukan permohonan izin ketidakhadiran perkuliahan beserta bukti pendukung agar dapat diperiksa oleh dosen pengampu.</p>

  <div class="izin-mhs-grid">
    {{-- Form Pengajuan Izin --}}
    <div class="card {{ $ubah ? 'is-editing' : '' }}">
      <div style="display:flex; justify-content:space-between; align-items:center; padding-bottom:12px; margin-bottom:16px; border-bottom:1px solid var(--border)">
        <h3 style="margin:0; padding:0; border:none">
          {{ $ubah ? 'Ubah Pengajuan Izin' : 'Form Pengajuan Izin' }}
        </h3>
        @if ($ubah)
          <span class="badge st-Menunggu">Mode Ubah</span>
        @endif
      </div>

      @if ($daftarPerkuliahan->isEmpty())
        <div class="empty-box">Kamu belum terdaftar pada perkuliahan mana pun.</div>
      @else
        <form method="POST" enctype="multipart/form-data"
              action="{{ $ubah ? route('izin.ubah', $ubah->idIzin) : route('izin.kirim') }}">
          @csrf

          <div class="form-row-2">
            <div>
              <label for="idPerkuliahan">Mata Kuliah</label>
              <select name="idPerkuliahan" id="idPerkuliahan" required>
                @foreach ($daftarPerkuliahan as $p)
                  <option value="{{ $p->idPerkuliahan }}"
                    @selected(old('idPerkuliahan', $ubah?->idPerkuliahan) == $p->idPerkuliahan)>
                    {{ $p->kodeMatkul }} - {{ $p->namaMatkul }} (Kelas {{ $p->kelas }})
                  </option>
                @endforeach
              </select>
            </div>

            <div>
              <label for="pertemuan">Pertemuan ke-</label>
              <select name="pertemuan" id="pertemuan" required>
                @for ($i = 1; $i <= 16; $i++)
                  <option value="{{ $i }}" @selected(old('pertemuan', $ubah?->pertemuan) == $i)>
                    Pertemuan {{ $i }}
                  </option>
                @endfor
              </select>
            </div>
          </div>

          <div class="row">
            <label for="alasan">Alasan Ketidakhadiran</label>
            <textarea name="alasan" id="alasan" rows="3" maxlength="255" required
                      placeholder="Tuliskan alasan ketidakhadiran dengan jelas...">{{ old('alasan', $ubah?->alasan) }}</textarea>
          </div>

          <div class="row" style="margin-bottom:0">
            <label for="fileBukti">Bukti Pendukung <span style="font-weight:400; color:var(--text-muted)">(opsional)</span></label>
            <div class="file-upload-box">
              <input type="file" name="fileBukti" id="fileBukti" accept=".pdf,.jpg,.jpeg,.png">
              <span class="file-hint">Format: PDF, JPG, atau PNG (maks. 2 MB).</span>
              @if ($ubah && $ubah->fileBukti)
                <span class="file-hint" style="color:var(--primary); font-weight:600">
                  Sudah ada lampiran bukti. Pilih file baru hanya jika ingin menggantinya.
                </span>
              @endif
            </div>
          </div>

          <div class="form-footer-bar">
            @if ($ubah)
              <a class="btn btn-batal" href="{{ route('izin.mahasiswa') }}">Batal</a>
            @else
              <button type="submit" name="aksi" value="Batal" class="btn-batal" formnovalidate>Batal</button>
            @endif
            <button type="submit" name="aksi" value="Kirim" class="btn-primary">
              <i data-lucide="send" style="width:15px;height:15px"></i>
              {{ $ubah ? 'Simpan Perubahan' : 'Kirim Pengajuan' }}
            </button>
          </div>
        </form>
      @endif
    </div>

    {{-- Daftar Pengajuan Saya --}}
    <div class="card">
      <div style="display:flex; justify-content:space-between; align-items:center; padding-bottom:12px; margin-bottom:14px; border-bottom:1px solid var(--border)">
        <h3 style="margin:0; padding:0; border:none">Pengajuan Saya</h3>
        <span style="font-size:13px; font-weight:600; color:var(--text-muted)">{{ $daftarPengajuan->count() }} pengajuan</span>
      </div>

      @if ($daftarPengajuan->isEmpty())
        <div class="empty-box">Belum ada riwayat pengajuan izin.</div>
      @else
        <table>
          <thead>
            <tr>
              <th>Mata Kuliah</th>
              <th style="text-align:center">Pertemuan</th>
              <th>Tanggal</th>
              <th>Status</th>
              <th style="text-align:right">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($daftarPengajuan as $z)
              <tr class="{{ $ubah && $ubah->idIzin == $z->idIzin ? 'row-editing' : '' }}">
                <td>
                  <strong>{{ $z->perkuliahan->namaMatkul }}</strong><br>
                  <small style="color:var(--text-muted)">Kelas {{ $z->perkuliahan->kelas }}</small>
                </td>
                <td style="text-align:center">
                  <span class="badge st-Izin">Ke-{{ $z->pertemuan }}</span>
                </td>
                <td style="white-space:nowrap">{{ $z->tanggalPengajuan->format('d/m/Y') }}</td>
                <td><span class="badge st-{{ $z->status }}">{{ $z->status }}</span></td>
                <td style="text-align:right">
                  <div class="action-group">
                    @if ($z->fileBukti)
                      <a class="btn btn-batal" style="padding:5px 10px; font-size:12.5px"
                         href="{{ route('izin.bukti', $z->idIzin) }}" target="_blank" title="Lihat Bukti">
                        <i data-lucide="paperclip" style="width:13px;height:13px;color:var(--primary)"></i> Bukti
                      </a>
                    @endif

                    @if ($z->status === 'Menunggu')
                      <a class="btn btn-batal" style="padding:5px 10px; font-size:12.5px"
                         href="{{ route('izin.mahasiswa', ['ubah' => $z->idIzin]) }}">
                        <i data-lucide="edit-2" style="width:13px;height:13px"></i> Ubah
                      </a>

                      <form method="POST" action="{{ route('izin.batal', $z->idIzin) }}" style="margin:0"
                            onsubmit="return confirm('Batalkan pengajuan izin ini?')">
                        @csrf
                        <button type="submit" class="btn-hapus" style="padding:5px 10px; font-size:12.5px">
                          Batalkan
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
  </div>
@endsection