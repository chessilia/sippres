<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SIPPRES - Sistem Presensi dan Perizinan Perkuliahan</title>
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
      --bg-surface: #ffffff;
      --text-main: #0f172a;
      --text-secondary: #475569;
      --text-muted: #64748b;
      --border: #e2e8f0;
      --radius: 18px;
    }

    * { box-sizing: border-box; }
    html, body {
      height: 100%;
      margin: 0;
    }
    body {
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
      background: var(--bg-page);
      color: var(--text-main);
      height: 100vh;
      overflow: hidden;
      display: grid;
      grid-template-rows: auto 1fr auto;
      font-size: 15px;
      line-height: 1.55;
      -webkit-font-smoothing: antialiased;
    }

    /* Top Navbar */
    .navbar {
      width: 100%;
      background: rgba(255, 255, 255, 0.92);
      backdrop-filter: blur(8px);
      border-bottom: 1px solid var(--border);
    }
    .nav-container {
      width: 100%;
      padding: 10px 36px;
      display: flex;
      align-items: center;
    }
    .brand {
      display: flex;
      align-items: center;
      gap: 10px;
      text-decoration: none;
      color: var(--text-main);
      font-weight: 800;
      font-size: 18px;
      letter-spacing: -0.3px;
    }
    .brand-icon {
      width: 32px;
      height: 32px;
      border-radius: 8px;
      background: var(--primary);
      color: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 3px 8px rgba(37, 99, 235, 0.28);
    }

    /* Main Section - Jarak Atas & Bawah Rapat, Tengah Luas */
    .main-section {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 14px 36px;
      min-height: 0;
      background:
        radial-gradient(circle at 50% 0%, #dbeafe 0%, rgba(219, 234, 254, 0) 55%),
        var(--bg-page);
    }

    .hero-header {
      max-width: 1060px;
      width: 100%;
      text-align: center;
      margin-bottom: 16px;
      flex-shrink: 0;
    }
    .hero-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 4px 13px;
      border-radius: 999px;
      background: #eff6ff;
      border: 1px solid #bfdbfe;
      color: var(--primary);
      font-size: 12.5px;
      font-weight: 700;
      margin-bottom: 8px;
    }
    .hero-header h1 {
      margin: 0 0 4px;
      font-size: clamp(28px, 3.8vw, 36px);
      font-weight: 800;
      line-height: 1.15;
      letter-spacing: -0.7px;
      color: var(--text-main);
    }
    .hero-header h1 span {
      color: var(--primary);
    }
    .hero-header p {
      margin: 0 auto;
      font-size: 15.5px;
      color: var(--text-secondary);
      font-weight: 500;
      white-space: nowrap;
    }

    /* Account Prompt Box - Diperbesar & Mengisi Ruang Tengah */
    .prompt-container {
      width: 100%;
      max-width: 1060px;
      flex: 1;
      max-height: 450px;
      background: var(--bg-surface);
      border: 1px solid var(--border);
      border-radius: 18px;
      padding: 28px 36px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      box-shadow: 0 14px 32px -8px rgba(15, 23, 42, 0.08), 0 4px 12px -4px rgba(15, 23, 42, 0.04);
    }
    .prompt-title {
      text-align: center;
      margin-bottom: 22px;
    }
    .prompt-title h2 {
      margin: 0 0 4px;
      font-size: 21px;
      font-weight: 800;
      color: var(--text-main);
      letter-spacing: -0.3px;
    }
    .prompt-title p {
      margin: 0;
      font-size: 14.5px;
      color: var(--text-muted);
      font-weight: 500;
    }

    .choice-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 24px;
      flex: 1;
    }
    .choice-card {
      border: 1.5px solid var(--border);
      border-radius: 14px;
      padding: 26px 28px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      background: #ffffff;
      transition: all 0.18s ease;
    }
    .choice-card:hover {
      border-color: var(--primary);
      box-shadow: 0 10px 24px rgba(37, 99, 235, 0.09);
      transform: translateY(-2px);
    }
    .choice-card.featured {
      background: #f8fafc;
      border-color: #cbd5e1;
    }
    .choice-card.featured:hover {
      border-color: var(--primary);
      background: #ffffff;
    }

    .choice-top {
      margin-bottom: 20px;
    }
    .choice-icon {
      width: 46px;
      height: 46px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 14px;
    }
    .choice-icon.blue {
      background: var(--primary-soft);
      color: var(--primary);
    }
    .choice-icon.slate {
      background: #e2e8f0;
      color: #1e293b;
    }
    .choice-card h3 {
      margin: 0 0 8px;
      font-size: 18.5px;
      font-weight: 800;
      color: var(--text-main);
    }
    .choice-card p {
      margin: 0;
      font-size: 14.5px;
      color: var(--text-secondary);
      line-height: 1.6;
    }

    /* Buttons */
    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      width: 100%;
      padding: 12px 20px;
      border-radius: 10px;
      font-family: inherit;
      font-size: 15px;
      font-weight: 700;
      text-decoration: none;
      border: 1px solid transparent;
      cursor: pointer;
      transition: all 0.15s ease;
    }
    .btn-primary {
      background: var(--primary);
      color: #ffffff;
      box-shadow: 0 4px 12px rgba(37, 99, 235, 0.24);
    }
    .btn-primary:hover {
      background: var(--primary-hover);
    }
    .btn-outline {
      background: #ffffff;
      color: var(--text-main);
      border-color: #cbd5e1;
    }
    .btn-outline:hover {
      background: #f1f5f9;
      border-color: var(--primary);
      color: var(--primary);
    }

    .forgot-hint {
      margin-top: 10px;
      text-align: center;
      font-size: 13px;
      color: var(--text-muted);
    }
    .forgot-hint a {
      color: var(--primary);
      font-weight: 600;
      text-decoration: none;
    }
    .forgot-hint a:hover {
      text-decoration: underline;
    }

    /* Footer */
    .footer {
      border-top: 1px solid var(--border);
      background: #ffffff;
      padding: 10px 24px;
      text-align: center;
      font-size: 12.5px;
      color: var(--text-muted);
      font-weight: 500;
    }

    @media (max-width: 768px) {
      body {
        height: auto;
        min-height: 100vh;
        overflow-y: auto;
      }
      .nav-container {
        padding: 12px 18px;
      }
      .hero-header p {
        white-space: normal;
      }
      .choice-grid {
        grid-template-columns: 1fr;
      }
      .prompt-container {
        max-height: none;
        padding: 20px;
      }
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <header class="navbar">
    <div class="nav-container">
      <a href="{{ url('/') }}" class="brand">
        <span class="brand-icon">
          <i data-lucide="check-circle-2" style="width:18px;height:18px"></i>
        </span>
        <span>SIPPRES</span>
      </a>
    </div>
  </header>

  <!-- Main Content -->
  <main class="main-section">
    <div class="hero-header">
      <div class="hero-badge">
        <i data-lucide="qr-code" style="width:14px;height:14px"></i>
        <span>Presensi QR Code & Perizinan Digital</span>
      </div>
      <h1>Selamat Datang di <span>SIPPRES</span></h1>
      <p>Catat kehadiran perkuliahan, kelola perizinan, dan pantau rekapitulasi secara cepat dan akurat.</p>
    </div>

    <!-- User Story Entry Choice -->
    <div class="prompt-container">
      <div class="prompt-title">
        <h2>Apakah Anda sudah memiliki akun?</h2>
        <p>Pilih salah satu opsi di bawah ini untuk melanjutkan ke dalam sistem</p>
      </div>

      <div class="choice-grid">
        <!-- Sudah Punya Akun -->
        <div class="choice-card featured">
          <div class="choice-top">
            <div class="choice-icon blue">
              <i data-lucide="log-in" style="width:22px;height:22px"></i>
            </div>
            <h3>Ya, Sudah Memiliki Akun</h3>
            <p>Masuk menggunakan email dan kata sandi terdaftar untuk langsung diarahkan ke dasbor sesuai peran Anda.</p>
          </div>
          <div>
            <a href="{{ route('login') }}" class="btn btn-primary">
              <span>Masuk ke Akun</span>
              <i data-lucide="arrow-right" style="width:16px;height:16px"></i>
            </a>
            @if (Route::has('password.request'))
              <div class="forgot-hint">
                Lupa kata sandi? <a href="{{ route('password.request') }}">Atur ulang di sini</a>
              </div>
            @endif
          </div>
        </div>

        <!-- Belum Punya Akun -->
        <div class="choice-card">
          <div class="choice-top">
            <div class="choice-icon slate">
              <i data-lucide="user-plus" style="width:22px;height:22px"></i>
            </div>
            <h3>Belum Memiliki Akun</h3>
            <p>Buat akun baru dengan memilih peran sebagai <strong>Mahasiswa</strong> atau <strong>Dosen</strong> dan lengkapi data identitas Anda.</p>
          </div>
          <div>
            <a href="{{ route('registrasi') }}" class="btn btn-outline">
              <span>Daftar Akun Baru</span>
              <i data-lucide="arrow-right" style="width:16px;height:16px"></i>
            </a>
            <div class="forgot-hint" style="visibility:hidden">Spacer</div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="footer">
    &copy; {{ date('Y') }} <strong>SIPPRES</strong> &mdash; Sistem Presensi dan Perizinan Perkuliahan.
  </footer>

  <script>
    lucide.createIcons();
  </script>
</body>
</html>