<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registrasi - SIPPRES</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    :root {
      --primary: #2563eb;
      --primary-hover: #1d4ed8;
      --primary-soft: #dbeafe;
      --bg-page: #f1f5f9;
      --bg-card: #ffffff;
      --text-main: #0f172a;
      --text-secondary: #475569;
      --text-muted: #64748b;
      --border: #e2e8f0;
      --radius: 18px;
      --shadow: 0 14px 32px -8px rgba(15, 23, 42, 0.08), 0 4px 12px -4px rgba(15, 23, 42, 0.04);
    }

    * { box-sizing: border-box; }
    html, body {
      height: 100%;
      margin: 0;
    }
    body {
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
      background:
        radial-gradient(circle at 50% 0%, #dbeafe 0%, rgba(219, 234, 254, 0) 55%),
        var(--bg-page);
      color: var(--text-main);
      height: 100vh;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 16px;
      font-size: 14.5px;
      line-height: 1.45;
      -webkit-font-smoothing: antialiased;
    }

    .auth-card {
      width: 100%;
      max-width: 490px;
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 26px 36px;
      box-shadow: var(--shadow);
    }

    .brand-header {
      text-align: center;
      margin-bottom: 18px;
    }
    .brand-logo {
      width: 42px;
      height: 42px;
      border-radius: 11px;
      background: var(--primary);
      color: #ffffff;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 8px;
      box-shadow: 0 4px 12px rgba(37, 99, 235, 0.28);
    }
    .brand-header h2 {
      margin: 0 0 2px;
      font-size: 20px;
      font-weight: 800;
      color: var(--text-main);
      letter-spacing: -0.3px;
    }
    .brand-header p {
      margin: 0;
      font-size: 13.5px;
      color: var(--text-muted);
      font-weight: 500;
    }

    .alert-error {
      padding: 9px 12px;
      border-radius: 9px;
      margin-bottom: 14px;
      font-size: 13.5px;
      font-weight: 600;
      background: #fef2f2;
      color: #991b1b;
      border: 1px solid #fecaca;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .row {
      margin-bottom: 11px;
    }
    label {
      display: block;
      font-weight: 700;
      font-size: 13px;
      margin-bottom: 4px;
      color: #334155;
    }

    .input-wrap {
      position: relative;
      display: flex;
      align-items: center;
    }
    .input-icon {
      position: absolute;
      left: 12px;
      color: var(--text-muted);
      width: 17px;
      height: 17px;
      pointer-events: none;
    }
    input[type=text], input[type=email], input[type=password], select {
      width: 100%;
      padding: 9.5px 12px 9.5px 38px;
      border: 1px solid #cbd5e1;
      border-radius: 9px;
      font-family: inherit;
      font-size: 14px;
      color: var(--text-main);
      background-color: #ffffff;
      transition: border-color 0.15s, box-shadow 0.15s;
    }
    input:focus, select:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }

    /* Hilangkan warna kuning/biru bawaan autofill browser */
    input:-webkit-autofill,
    input:-webkit-autofill:hover,
    input:-webkit-autofill:focus {
      -webkit-box-shadow: 0 0 0 1000px #ffffff inset !important;
      -webkit-text-fill-color: var(--text-main) !important;
      transition: background-color 5000s ease-in-out 0s;
    }

    .toggle-pwd {
      position: absolute;
      right: 10px;
      background: transparent;
      border: none;
      color: var(--text-muted);
      cursor: pointer;
      padding: 4px;
      display: flex;
      align-items: center;
    }
    .toggle-pwd:hover { color: var(--text-main); }

    .btn-group {
      display: flex;
      gap: 10px;
      margin-top: 18px;
    }
    .btn {
      padding: 10.5px 18px;
      border: 1px solid transparent;
      border-radius: 9px;
      font-family: inherit;
      font-size: 14.5px;
      font-weight: 700;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 7px;
      transition: all 0.15s ease;
    }
    .btn-primary {
      flex: 1;
      background: var(--primary);
      color: #ffffff;
      box-shadow: 0 4px 12px rgba(37, 99, 235, 0.22);
    }
    .btn-primary:hover { background: var(--primary-hover); }
    .btn-batal {
      width: 110px;
      flex-shrink: 0;
      background: #ffffff;
      color: var(--text-secondary);
      border-color: #cbd5e1;
    }
    .btn-batal:hover {
      background: #f8fafc;
      color: var(--text-main);
    }

    .auth-footer {
      margin-top: 16px;
      padding-top: 14px;
      border-top: 1px solid var(--border);
      text-align: center;
      font-size: 13.5px;
      color: var(--text-secondary);
      font-weight: 500;
    }
    .auth-footer a {
      color: var(--primary);
      font-weight: 700;
      text-decoration: none;
    }
    .auth-footer a:hover { text-decoration: underline; }

    @media (max-width: 520px) {
      body {
        height: auto;
        min-height: 100vh;
        overflow-y: auto;
      }
      .auth-card {
        padding: 22px 18px;
      }
    }
  </style>
</head>
<body>

  <div class="auth-card">
    <div class="brand-header">
      <div class="brand-logo">
        <i data-lucide="user-plus" style="width:20px;height:20px"></i>
      </div>
      <h2>Daftar Akun SIPPRES</h2>
      <p>Lengkapi data di bawah untuk membuat akun baru</p>
    </div>

    @if (session('error'))
      <div class="alert-error">
        <i data-lucide="alert-circle" style="width:17px;height:17px;flex-shrink:0"></i>
        <span>{{ session('error') }}</span>
      </div>
    @endif
    @if ($errors->any())
      <div class="alert-error">
        <i data-lucide="alert-circle" style="width:17px;height:17px;flex-shrink:0"></i>
        <span>{{ $errors->first() }}</span>
      </div>
    @endif

    <form method="POST" action="{{ route('registrasi.submit') }}">
      @csrf

      <div class="row">
        <label for="role">Daftar Sebagai</label>
        <div class="input-wrap">
          <i data-lucide="shield" class="input-icon"></i>
          <select name="role" id="role">
            <option value="Mahasiswa" @selected(old('role') == 'Mahasiswa')>Mahasiswa</option>
            <option value="Dosen" @selected(old('role') == 'Dosen')>Dosen</option>
          </select>
        </div>
      </div>

      <div class="row">
        <label for="username">Username</label>
        <div class="input-wrap">
          <i data-lucide="at-sign" class="input-icon"></i>
          <input type="text" id="username" name="username" value="{{ old('username') }}" placeholder="Contoh: 235150200111001" required>
        </div>
      </div>

      <div class="row">
        <label for="nama">Nama Lengkap</label>
        <div class="input-wrap">
          <i data-lucide="user" class="input-icon"></i>
          <input type="text" id="nama" name="nama" value="{{ old('nama') }}" placeholder="Masukkan nama lengkap" required>
        </div>
      </div>

      <div class="row">
        <label for="email">Email</label>
        <div class="input-wrap">
          <i data-lucide="mail" class="input-icon"></i>
          <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="nama@gmail.com" required>
        </div>
      </div>

      <div class="row">
        <label for="password">Kata Sandi <span style="font-weight:500;color:#64748b">(min. 8 karakter)</span></label>
        <div class="input-wrap">
          <i data-lucide="lock" class="input-icon"></i>
          <input type="password" id="password" name="password" minlength="8" placeholder="Minimal 8 karakter" style="padding-right:38px" required>
          <button type="button" class="toggle-pwd" onclick="toggleSandi()" aria-label="Tampilkan atau sembunyikan kata sandi">
            <i data-lucide="eye" id="eyeIcon" style="width:17px;height:17px"></i>
          </button>
        </div>
      </div>

      <div class="btn-group">
        <button type="submit" name="aksi" value="Batal" class="btn btn-batal" formnovalidate>Batal</button>
        <button type="submit" name="aksi" value="Submit" class="btn btn-primary">
          <i data-lucide="check" style="width:17px;height:17px"></i>
          <span>Submit Registrasi</span>
        </button>
      </div>
    </form>

    <div class="auth-footer">
      Sudah punya akun? <a href="{{ route('login') }}">Masuk Sekarang</a>
    </div>
  </div>

  <script>
    lucide.createIcons();
    function toggleSandi() {
      const f = document.getElementById('password');
      const icon = document.getElementById('eyeIcon');
      const isHidden = f.type === 'password';
      f.type = isHidden ? 'text' : 'password';
      icon.setAttribute('data-lucide', isHidden ? 'eye-off' : 'eye');
      lucide.createIcons();
    }
  </script>
</body>
</html>