<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Atur Ulang Kata Sandi - SIPPRES</title>
  <style>
    body { font-family: Arial, sans-serif; background:#f4f6f8; margin:0; }
    .container { max-width:380px; margin:60px auto; padding:24px; background:#fff;
                 border:1px solid #ddd; border-radius:8px; }
    .row { margin-bottom:14px; }
    label { display:block; font-weight:bold; margin-bottom:4px; }
    input[type=email], input[type=password] { width:100%; padding:8px; box-sizing:border-box; }
    button { width:100%; padding:10px; border:none; border-radius:5px; cursor:pointer; background:#007bff; color:#fff; }
    .alert-error { background:#fde2e2; padding:8px; border-radius:4px; margin-bottom:12px; }
  </style>
</head>
<body>
  <div class="container">
    <h3>Atur Ulang Kata Sandi</h3>

    @if (session('error')) <div class="alert-error">{{ session('error') }}</div> @endif

    <form method="POST" action="{{ route('password.update') }}">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">

      <div class="row">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email', $email) }}" required>
      </div>
      <div class="row">
        <label for="password">Kata sandi baru (min. 8 karakter)</label>
        <input type="password" id="password" name="password" minlength="8" required>
      </div>
      <div class="row">
        <label for="password_confirmation">Ulangi kata sandi baru</label>
        <input type="password" id="password_confirmation" name="password_confirmation" minlength="8" required>
      </div>
      <button type="submit">Simpan Kata Sandi Baru</button>
    </form>

    <p><a href="{{ route('login') }}">Kembali ke halaman masuk</a></p>
  </div>
</body>
</html>