<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Masuk - SIPPRES</title>
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
      padding: 20px;
      font-size: 15px;
      line-height: 1.5;
      -webkit-font-smoothing: antialiased;
    }

    .auth-card {
      width: 100%;
      max-width: 480px;
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 36px 40px;
      box-shadow: var(--shadow);
    }

    .brand-header {
      text-align: center;
      margin-bottom: 24px;
    }
    .brand-logo {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      background: var(--primary);
      color: #ffffff;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 12px;
      box-shadow: 0 4px 12px rgba(37, 99, 235, 0.28);
    }
    .brand-header h2 {
      margin: 0 0 4px;
      font-size: 22px;
      font-weight: 800;
      color: var(--text-main);
      letter-spacing: -0.3px;
    }
    .brand-header p {
      margin: 0;
      font-size: 14.5px;
      color: var(--text-muted);
      font-weight: 500;
    }

    .alert-error, .alert-success {
      padding: 11px 14px;
      border-radius: 10px;
      margin-bottom: 18px;
      font-size: 14px;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .alert-error {
      background: #fef2f2;
      color: #991b1b;
      border: 1px solid #fecaca;
    }
    .alert-success {
      background: #f0fdf4;
      color: #166534;
      border: 1px solid #bbf7d0;
    }

    .row { margin-bottom: 18px; }
    .label-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 6px;
    }
    label {
      display: block;
      font-weight: 700;
      font-size: 14px;
      color: #334155;
    }
    .forgot-link {
      font-size: 13px;
      font-weight: 600;
      color: var(--primary);
      text-decoration: none;
    }
    .forgot-link:hover { text-decoration: underline; }

    .input-wrap {
      position: relative;
      display: flex;
      align-items: center;
    }
    .input-icon {
      position: absolute;
      left: 13px;
      color: var(--text-muted);
      width: 18px;
      height: 18px;
      pointer-events: none;
    }
    input[type=text], input[type=email], input[type=password] {
      width: 100%;
      padding: 12px 14px 12px 40px;
      border: 1px solid #cbd5e1;
      border-radius: 10px;
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
      right: 12px;
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
      gap: 12px;
      margin-top: 24px;
    }
    .btn {
      padding: 12px 22px;
      border: 1px solid transparent;
      border-radius: 10px;
      font-family: inherit;
      font-size: 15px;
      font-weight: 700;
      text-decoration: none;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
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
      min-width: 110px;
      background: #ffffff;
      color: var(--text-secondary);
      border-color: #cbd5e1;
    }
    .btn-batal:hover {
      background: #f8fafc;
      color: var(--text-main);
    }

    .auth-footer {
      margin-top: 22px;
      padding-top: 18px;
      border-top: 1px solid var(--border);
      text-align: center;
      font-size: 14px;
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
        padding: 26px 20px;
      }
    }
  </style>
</head>
<body>

  <div class="auth-card">
    <div class="brand-header">
      <div class="brand-logo">
        <i data-lucide="log-in" style="width:22px;height:22px"></i>
      </div>
      <h2>Masuk ke SIPPRES</h2>
      <p>Gunakan email dan kata sandi akun Anda</p>
    </div>

    @if (session('status'))
      <div class="alert-success">
        <i data-lucide="check-circle-2" style="width:18px;height:18px;flex-shrink:0"></i>
        <span>{{ session('status') }}</span>
      </div>
    @endif
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

    <form method="POST" action="{{ route('login') }}">
      @csrf

      <div class="row">
        <div class="label-row">
          <label for="email">Email Kampus</label>
        </div>
        <div class="input-wrap">
          <i data-lucide="mail" class="input-icon"></i>
          <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="nama@gmail.com" required autofocus>
        </div>
      </div>

      <div class="row">
        <div class="label-row">
          <label for="password">Kata Sandi</label>
          @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="forgot-link">Lupa kata sandi?</a>
          @endif
        </div>
        <div class="input-wrap">
          <i data-lucide="lock" class="input-icon"></i>
          <input type="password" id="password" name="password" placeholder="Masukkan kata sandi" style="padding-right:40px" required>
          <button type="button" class="toggle-pwd" onclick="toggleSandi()" aria-label="Tampilkan atau sembunyikan kata sandi">
            <i data-lucide="eye" id="eyeIcon" style="width:18px;height:18px"></i>
          </button>
        </div>
      </div>

      <div class="btn-group">
        <a href="{{ url('/') }}" class="btn btn-batal">Kembali</a>
        <button type="submit" class="btn btn-primary">
          <span>Masuk Sekarang</span>
          <i data-lucide="arrow-right" style="width:17px;height:17px"></i>
        </button>
      </div>
    </form>

    <div class="auth-footer">
      Belum punya akun? <a href="{{ route('registrasi') }}">Daftar Akun Baru</a>
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