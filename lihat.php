<?php
include 'koneksi.php';

if(!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];

// Prepared statement untuk keamanan
$stmt = $conn->prepare("SELECT s.*, k.nama AS kategori FROM surat s JOIN kategori k ON s.kategori_id=k.id WHERE s.id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if($res->num_rows == 0) {
    echo 'Data tidak ditemukan';
    exit;
}

$row = $res->fetch_assoc();

// Fallback supaya tidak error jika field kosong
$nomor = isset($row['nomor']) ? htmlspecialchars($row['nomor']) : '-';
$judul = isset($row['judul']) ? htmlspecialchars($row['judul']) : '-';
$kategori = isset($row['kategori']) ? htmlspecialchars($row['kategori']) : '-';
$created_at = isset($row['created_at']) ? htmlspecialchars($row['created_at']) : '-';
$file_pdf = isset($row['file_pdf']) ? htmlspecialchars(urlencode($row['file_pdf'])) : '';
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Lihat Surat</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <div class="row">
    <div class="col-3">
      <h5>Menu</h5>
      <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link" href="index.php">Arsip</a></li>
        <li class="nav-item"><a class="nav-link" href="kategori.php">Kategori Surat</a></li>
        <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
      </ul>
    </div>
    <div class="col-9">
      <h3>Arsip Surat &gt;&gt; Lihat</h3>
      <p>
        <strong>Nomor:</strong> <?= $nomor ?><br>
        <strong>Kategori:</strong> <?= $kategori ?><br>
        <strong>Judul:</strong> <?= $judul ?><br>
        <strong>Waktu Unggah:</strong> <?= $created_at ?>
      </p>

      <?php if($file_pdf): ?>
      <div style="border:1px solid #ddd; padding:10px; height:500px; overflow:auto; background:#fff">
        <embed src="uploads/<?= $file_pdf ?>" type="application/pdf" width="100%" height="480px">
      </div>
      <?php else: ?>
      <p><em>File PDF tidak tersedia</em></p>
      <?php endif; ?>

      <div class="mt-3">
        <a class="btn btn-secondary" href="index.php">&lt;&lt; Kembali</a>
        <?php if($file_pdf): ?>
        <a class="btn btn-warning" href="uploads/<?= $file_pdf ?>" download>Unduh</a>
        <?php endif; ?>
        <a class="btn btn-info" href="arsipkan.php?id=<?= $row['id'] ?>">Edit/Ganti File</a>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
