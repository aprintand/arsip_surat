<?php
include 'koneksi.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$nama = '';

if($id){
    $stmt = $conn->prepare("SELECT * FROM kategori WHERE id=?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $r = $stmt->get_result()->fetch_assoc();
    if($r) $nama = $r['nama'];
}

if(isset($_POST['submit'])){
    $nama_in = $conn->real_escape_string($_POST['nama']);
    if($id){
        $stmt = $conn->prepare("UPDATE kategori SET nama=? WHERE id=?");
        $stmt->bind_param("si",$nama_in,$id);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("INSERT INTO kategori (nama) VALUES (?)");
        $stmt->bind_param("s",$nama_in);
        $stmt->execute();
    }
    header('Location: kategori.php'); exit;
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Form Kategori</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background:#f5f7fb }
    .sidebar { min-height:100vh; background:#e9f0ff }
    .form-wrap { background:white; padding:20px; border-radius:6px }
  </style>
</head>
<body>
<div class="container-fluid">
  <div class="row">
    <nav class="col-2 sidebar p-3">
      <h5>Menu</h5>
      <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link" href="index.php">Arsip</a></li>
        <li class="nav-item"><a class="nav-link active" href="kategori.php">Kategori Surat</a></li>
        <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
      </ul>
    </nav>

    <main class="col-10 p-4">
      <div class="form-wrap">
        <h3>Kategori Surat &gt;&gt; <?= $id ? 'Edit' : 'Tambah' ?></h3>
        <form method="POST">
          <div class="mb-3">
            <label class="form-label">ID (Auto Increment)</label>
            <input class="form-control" value="<?= $id ?>" readonly>
          </div>
          <div class="mb-3">
            <label class="form-label">Nama Kategori</label>
            <input class="form-control" name="nama" required value="<?= htmlspecialchars($nama) ?>">
          </div>
          <button class="btn btn-secondary" type="button" onclick="location.href='kategori.php'">&lt;&lt; Kembali</button>
          <button class="btn btn-primary" type="submit" name="submit">Simpan</button>
        </form>
      </div>
    </main>
  </div>
</div>
</body>
</html>
