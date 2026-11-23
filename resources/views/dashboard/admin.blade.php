<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin</title>
</head>
<body>
  <h1>Dashboard Admin</h1>
  <p>Halo, {{ Auth::user()->nama }}!</p>
  <p><a href="{{ route('perkuliahan.daftar') }}">Kelola Data Perkuliahan</a></p>
  <form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit">Logout</button>
  </form>
</body>
</html>