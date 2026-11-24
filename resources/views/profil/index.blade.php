@extends('layouts.app')

@section('judul', 'Profil Saya')

@section('isi')
  <style>
    .profile-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 20px;
    }
    .profile-identity {
      display: flex;
      align-items: center;
      gap: 20px;
    }
    .avatar-ring {
      padding: 3px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--primary), #60a5fa);
      flex-shrink: 0;
    }
    .avatar-ring .avatar {
      border: 3px solid #ffffff;
      width: 80px;
      height: 80px;
      font-size: 30px;
    }
    .profile-name {
      margin: 0 0 6px !important;
      padding: 0 !important;
      border: none !important;
      font-size: 22px !important;
      font-weight: 800;
      color: var(--text-main);
    }
    .profile-meta {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 6px;
      font-size: 14px;
      color: var(--text-secondary);
      font-weight: 500;
    }
    .meta-item {
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }
    .form-grid-2 {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 18px 20px;
    }
    .upload-box {
      border: 1.5px dashed #cbd5e1;
      border-radius: 12px;
      padding: 16px 18px;
      background: var(--bg-subtle);
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 14px;
      transition: border-color 0.15s ease;
    }
    .upload-box:hover {
      border-color: var(--primary);
    }
    .upload-info {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .upload-icon {
      width: 40px;
      height: 40px;
      border-radius: 10px;
      background: var(--primary-soft);
      color: var(--primary);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    .form-footer {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 22px;
      padding-top: 16px;
      border-top: 1px solid var(--border);
    }
    .danger-card {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 16px;
      border-color: #fecaca;
      background: #fffafa;
    }
    @media (max-width: 640px) {
      .form-grid-2 {
        grid-template-columns: 1fr;
      }
      .form-footer {
        flex-direction: column-reverse;
      }
      .form-footer button {
        width: 100%;
      }
    }
  </style>

  {{-- Kartu Identitas Utama --}}
  <div class="card" style="margin-top:0">
    <div class="profile-header">
      <div class="profile-identity">
        <div class="avatar-ring">
          @if ($user->fotoProfil)
            <img class="avatar" id="avatarPreview" src="{{ route('profil.foto', $user->idUser) }}?v={{ md5($user->fotoProfil) }}" alt="Foto profil">
          @else
            <div class="avatar" id="avatarFallback">{{ strtoupper(substr($user->nama, 0, 1)) }}</div>
            <img class="avatar" id="avatarPreview" src="" alt="Foto profil" style="display:none">
          @endif
        </div>
        <div>
          <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap">
            <h3 class="profile-name">{{ $user->nama }}</h3>
            <span class="badge st-{{ $user->role }}">{{ $user->role }}</span>
          </div>
          <div class="profile-meta">
            <span class="meta-item">
              <i data-lucide="at-sign" style="width:15px;height:15px;color:var(--text-muted)"></i>
              {{ $user->username }}
            </span>
            <span>•</span>
            <span class="meta-item">
              <i data-lucide="mail" style="width:15px;height:15px;color:var(--text-muted)"></i>
              {{ $user->email }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Ringkasan Statistik --}}
  @if (!empty($ringkasan))
    <div class="grid4">
      @foreach ($ringkasan as $label => $nilai)
        <div class="stat">
          {{ $label }}
          <b>{{ $nilai }}</b>
        </div>
      @endforeach
    </div>
  @endif

  {{-- Form Ubah Profil --}}
  <div class="card">
    <h3>
      <i data-lucide="user-cog" style="width:19px;height:19px;vertical-align:-3px;color:var(--primary);margin-right:6px"></i>
      Pengaturan Profil
    </h3>

    <form method="POST" action="{{ route('profil.update') }}" enctype="multipart/form-data">
      @csrf

      <div class="form-grid-2">
        <div class="row" style="margin-bottom:0">
          <label for="nama">Nama Lengkap</label>
          <input type="text" name="nama" id="nama" maxlength="100" required value="{{ old('nama', $user->nama) }}" placeholder="Masukkan nama lengkap">
        </div>

        <div class="row" style="margin-bottom:0">
          <label for="email">Alamat Email</label>
          <input type="email" name="email" id="email" maxlength="100" required value="{{ old('email', $user->email) }}" placeholder="nama@email.com">
        </div>
      </div>

      <div class="row" style="margin-top:18px; margin-bottom:0">
        <label for="fotoProfil">Foto Profil</label>
        <div class="upload-box">
          <div class="upload-info">
            <div class="upload-icon">
              <i data-lucide="image-plus" style="width:20px;height:20px"></i>
            </div>
            <div>
              <div style="font-weight:700; font-size:14px; color:var(--text-main)" id="fileNameText">Pilih foto profil baru</div>
              <div style="font-size:12.5px; color:var(--text-muted)">Format JPG, JPEG, atau PNG • Ukuran maksimal 2 MB</div>
            </div>
          </div>
          <input type="file" name="fotoProfil" id="fotoProfil" accept=".jpg,.jpeg,.png" style="width:auto; max-width:260px; padding:7px 10px; font-size:13.5px" onchange="previewFoto(event)">
        </div>
      </div>

      <div class="form-footer">
        <button type="submit" name="aksi" value="Batal" class="btn-batal" formnovalidate>Batal</button>
        <button type="submit" name="aksi" value="Simpan" class="btn-primary">
          <i data-lucide="check" style="width:16px;height:16px"></i> Simpan Perubahan
        </button>
      </div>
    </form>
  </div>

  {{-- Sesi Akun / Keluar --}}
  <div class="card danger-card">
    <div>
      <h4 style="margin:0 0 2px; color:#991b1b">Keluar dari Sesi</h4>
      <p style="margin:0; font-size:13.5px; color:var(--text-secondary)">Akhiri sesi login Anda pada perangkat ini dengan aman.</p>
    </div>
    <form method="POST" action="{{ route('logout') }}" style="margin:0" onsubmit="return confirm('Yakin ingin keluar?')">
      @csrf
      <button type="submit" class="btn-hapus">
        <i data-lucide="log-out" style="width:16px;height:16px"></i> Keluar Akun
      </button>
    </form>
  </div>

  @push('scripts')
  <script>
    function previewFoto(event) {
      const file = event.target.files[0];
      if (!file) return;
      document.getElementById('fileNameText').textContent = file.name;
      const reader = new FileReader();
      reader.onload = function(e) {
        const preview = document.getElementById('avatarPreview');
        const fallback = document.getElementById('avatarFallback');
        preview.src = e.target.result;
        preview.style.display = 'flex';
        if (fallback) fallback.style.display = 'none';
      };
      reader.readAsDataURL(file);
    }
  </script>
  @endpush
@endsection