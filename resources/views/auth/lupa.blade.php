<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lupa Kata Sandi - SIPPRES</title>
  <style>
    body { font-family: Arial, sans-serif; background:#f4f6f8; margin:0; }
    .container { max-width:380px; margin:60px auto; padding:24px; background:#fff;
                 border:1px solid #ddd; border-radius:8px; }
    .row { margin-bottom:14px; }
    label { display:block; font-weight:bold; margin-bottom:4px; }
    input[type=email] { width:100%; padding:8px; box-sizing:border-box; }
    button { width:100%; padding:10px; border:none; border-radius:5px; cursor:pointer; background:#007bff; color:#fff; }
    .alert-error { background:#fde2e2; padding:8px; border-radius:4px; margin-bottom:12px; }
    .alert-success { background:#dff5e1; padding:8px; border-radius:4px; margin-bottom:12px; }
    .dev { background:#fff3cd; padding:8px; border-radius:4px; margin-bottom:12px; word-break:break-all; font-size:13px; }
  </style>
</head>
<body>
  <div class="container">
    <h3>Lupa Kata Sandi</h3>
    <p>Masukkan email akunmu. Kami akan mengirim tautan untuk mengatur ulang kata sandi.</p>

    @if (session('success')) <div class="alert-success">{{ session('success') }}</div> @endif
    @if (session('error'))   <div class="alert-error">{{ session('error') }}</div>   @endif

    @if (session('tautan_dev'))
      <div class="dev">
        <strong>Mode pengembangan:</strong> email tidak dikirim sungguhan. Buka tautan ini:<br>
        <a href="{{ session('tautan_dev') }}">{{ session('tautan_dev') }}</a>
      </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
      @csrf
      <div class="row">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required>
      </div>
      <button type="submit">Kirim Tautan Reset</button>
    </form>

    <p><a href="{{ route('login') }}">Kembali ke halaman masuk</a></p>
  </div>
</body>
</html>