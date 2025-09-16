<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>About</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
 <style>
  body { background:#f5f7fb; }
  .sidebar { min-height:100vh; background:#e9f0ff; }
  .content-card { background:white; padding:20px; border-radius:6px; }
  .img-profile { width:100%; max-width:150px; /* border-radius:50%; */ } /* hilangkan border-radius */
</style>

</head>
<body>
<div class="container-fluid">
  <div class="row">
    <nav class="col-2 sidebar p-3">
      <h5>Menu</h5>
      <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link" href="index.php">Arsip</a></li>
        <li class="nav-item"><a class="nav-link" href="kategori.php">Kategori Surat</a></li>
        <li class="nav-item"><a class="nav-link active" href="about.php">About</a></li>
      </ul>
    </nav>

    <main class="col-10 p-4">
      <div class="content-card" style="max-width:600px;">
        <h3>About</h3>
        <div class="row g-3 align-items-center">
          <div class="col-md-4 text-center">
            <img src="img/2231740021.jpg" alt="Aprintan Dwi Cahyani" class="img-profile">
          </div>
          <div class="col-md-8">
            <h5>APRINTAN DWI CAHYANI</h5>
            <p>NIM: 2231740021</p>
            <p>Proyek Arsip Surat ini dibuat sebagai syarat ujian sertifikasi </p>
            <p><small class="text-muted">Tanggal Pembuatan: 13 September 2025 </small></p>
          </div>
        </div>
        <a class="btn btn-secondary mt-3" href="index.php">Kembali</a>
      </div>
    </main>
  </div>
</div>
</body>
</html>
