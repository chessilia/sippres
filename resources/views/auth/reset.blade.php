<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Atur Ulang Kata Sandi - SIPPRES</title>
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
      --radius: 12px;
      --shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08), 0 4px 10px -4px rgba(15, 23, 42, 0.04);
    }

    * { box-sizing: border-box; }
    body {
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
      background: var(--bg-page);
      color: var(--text-main);
      margin: 0;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 24px 16px;
      font-size: 15px;
      line-height: 1.5;
      -webkit-font-smoothing: antialiased;
    }

    .auth-card {
      width: 100%;
      max-width: 420px;
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 32px;
      box-shadow: var(--shadow);
    }

    .brand-header {
      text-align: center;
      margin-bottom: 22px;
    }
    .brand-logo {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      background: var(--primary-soft);
      color: var(--primary);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 12px;
    }
    .brand-header h2 {
      margin: 0 0 6px;
      font-size: 22px;
      font-weight: 800;
      color: var(--text-main);
      letter-spacing: -0.3px;
    }
    .brand-header p {
      margin: 0;
      font-size: 14px;
      color: var(--text-secondary);
      font-weight: 500;
    }

    .alert-error {
      padding: 12px 14px;
      border-radius: 8px;
      margin-bottom: 18px;
      font-size: 14px;
      font-weight: 600;
      background: #fef2f2;
      color: #991b1b;
      border: 1px solid #fecaca;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .row { margin-bottom: 16px; }
    label {
      display: block;
      font-weight: 700;
      font-size: 14px;
      margin-bottom: 6px;
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
      width: 18px;
      height: 18px;
      pointer-events: none;
    }
    input[type=email], input[type=password], input[type=text] {
      width: 100%;
      padding: 11px 12px 11px 38px;
      border: 1px solid #cbd5e1;
      border-radius: 8px;
      font-family: inherit;
      font-size: 15px;
      color: var(--text-main);
      background: #fff;
      transition: border-color 0.15s, box-shadow 0.15s;
    }
    input:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
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

    .btn-primary {
      width: 100%;
      padding: 11px 18px;
      margin-top: 6px;
      background: var(--primary);
      color: #ffffff;
      border: none;
      border-radius: 8px;
      font-family: inherit;
      font-size: 15px;
      font-weight: 700;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: background 0.15s ease;
    }
    .btn-primary:hover { background: var(--primary-hover); }

    .auth-footer {
      margin-top: 22px;
      padding-top: 18px;
      border-top: 1px solid var(--border);
      text-align: center;
      font-size: 14px;
    }
    .auth-footer a {
      color: var(--text-secondary);
      font-weight: 700;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: color 0.15s;
    }
    .auth-footer a:hover { color: var(--primary); }
  </style>
</head>
<body>

  <div class="auth-card">
    <div class="brand-header">
      <div class="brand-logo">
        <i data-lucide="shield-check" style="width:24px;height:24px"></i>
      </div>
      <h2>Atur Ulang Kata Sandi</h2>
      <p>Buat kata sandi baru yang kuat untuk mengamankan akunmu</p>
    </div>

    @if (session('error'))
      <div class="alert-error">
        <i data-lucide="alert-circle" style="width:18px;height:18px;flex-shrink:0"></i>
        <span>{{ session('error') }}</span>
      </div>
    @endif
    @if ($errors->any())
      <div class="alert-error">
        <i data-lucide="alert-circle" style="width:18px;height:18px;flex-shrink:0"></i>
        <span>{{ $errors->first() }}</span>
      </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">

      <div class="row">
        <label for="email">Email</label>
        <div class="input-wrap">
          <i data-lucide="mail" class="input-icon"></i>
          <input type="email" id="email" name="email" value="{{ old('email', $email) }}" placeholder="nama@gmail.com" required>
        </div>
      </div>

      <div class="row">
        <label for="password">Kata Sandi Baru <span style="font-weight:500;color:#64748b">(min. 8 karakter)</span></label>
        <div class="input-wrap">
          <i data-lucide="lock" class="input-icon"></i>
          <input type="password" id="password" name="password" minlength="8" placeholder="Minimal 8 karakter" style="padding-right:38px" required autofocus>
          <button type="button" class="toggle-pwd" onclick="toggleSandi('password', 'eyeIcon1')" aria-label="Tampilkan atau sembunyikan kata sandi">
            <i data-lucide="eye" id="eyeIcon1" style="width:18px;height:18px"></i>
          </button>
        </div>
      </div>

      <div class="row">
        <label for="password_confirmation">Ulangi Kata Sandi Baru</label>
        <div class="input-wrap">
          <i data-lucide="lock-keyhole" class="input-icon"></i>
          <input type="password" id="password_confirmation" name="password_confirmation" minlength="8" placeholder="Ketik ulang kata sandi baru" style="padding-right:38px" required>
          <button type="button" class="toggle-pwd" onclick="toggleSandi('password_confirmation', 'eyeIcon2')" aria-label="Tampilkan atau sembunyikan konfirmasi kata sandi">
            <i data-lucide="eye" id="eyeIcon2" style="width:18px;height:18px"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="btn-primary">
        <i data-lucide="check" style="width:18px;height:18px"></i>
        <span>Simpan Kata Sandi Baru</span>
      </button>
    </form>

    <div class="auth-footer">
      <a href="{{ route('login') }}">
        <i data-lucide="arrow-left" style="width:16px;height:16px"></i>
        <span>Kembali ke halaman masuk</span>
      </a>
    </div>
  </div>

  <script>
    lucide.createIcons();
    function toggleSandi(inputId, iconId) {
      const f = document.getElementById(inputId);
      const icon = document.getElementById(iconId);
      const isHidden = f.type === 'password';
      f.type = isHidden ? 'text' : 'password';
      icon.setAttribute('data-lucide', isHidden ? 'eye-off' : 'eye');
      lucide.createIcons();
    }
  </script>
</body>
</html>