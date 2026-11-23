<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Dosen</title>
</head>
<body>
  <h1>Dashboard Dosen</h1>
  <p>Halo, {{ Auth::user()->nama }}!</p>
  <form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit">Logout</button>
  </form>
</body>
</html>