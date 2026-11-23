<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Mahasiswa</title>
</head>
<body>
  <h1>Dashboard Mahasiswa</h1>
  <p>Halo, {{ Auth::user()->nama }}!</p>
  <form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit">Logout</button>
  </form>
</body>
</html>