<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lupa Kata Sandi - SIPPRES</title>
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
      line-height: 1.55;
    }

    .alert-error, .alert-success {
      padding: 12px 14px;
      border-radius: 8px;
      margin-bottom: 18px;
      font-size: 14px;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .alert-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
    .alert-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }

    .dev-box {
      background: #fffbeb;
      color: #92400e;
      border: 1px solid #fde68a;
      padding: 12px 14px;
      border-radius: 8px;
      margin-bottom: 18px;
      font-size: 13.5px;
      line-height: 1.5;
      word-break: break-all;
    }
    .dev-box strong {
      display: flex;
      align-items: center;
      gap: 6px;
      font-weight: 700;
      margin-bottom: 4px;
      color: #78350f;
    }
    .dev-box a {
      color: var(--primary);
      font-weight: 600;
      text-decoration: underline;
    }

    .row { margin-bottom: 18px; }
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
    input[type=email] {
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

    .btn-primary {
      width: 100%;
      padding: 11px 18px;
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
        <i data-lucide="key-round" style="width:24px;height:24px"></i>
      </div>
      <h2>Lupa Kata Sandi</h2>
      <p>Masukkan email akunmu. Kami akan mengirim tautan untuk mengatur ulang kata sandi.</p>
    </div>

    @if (session('success'))
      <div class="alert-success">
        <i data-lucide="check-circle" style="width:18px;height:18px;flex-shrink:0"></i>
        <span>{{ session('success') }}</span>
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

    @if (session('tautan_dev'))
      <div class="dev-box">
        <strong><i data-lucide="wrench" style="width:15px;height:15px"></i> Mode Pengembangan</strong>
        Email tidak dikirim sungguhan. Buka tautan ini:<br>
        <a href="{{ session('tautan_dev') }}">{{ session('tautan_dev') }}</a>
      </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
      @csrf
      <div class="row">
        <label for="email">Email Kampus</label>
        <div class="input-wrap">
          <i data-lucide="mail" class="input-icon"></i>
          <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="nama@gmail.com" required autofocus>
        </div>
      </div>

      <button type="submit" class="btn-primary">
        <i data-lucide="send" style="width:17px;height:17px"></i>
        <span>Kirim Tautan Reset</span>
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
  </script>
</body>
</html>