<?php
session_start();
include "koneksi.php";

// Folder upload
$upload_dir = "uploads/";
if(!is_dir($upload_dir)){
    mkdir($upload_dir, 0777, true); // buat folder otomatis
}

// Ambil kategori
$kategori_res = $conn->query("SELECT * FROM kategori ORDER BY nama ASC");

// Generate nomor surat otomatis
// Format: tahun/code/lembaga/angka
$code = "UND";       // kode surat, bisa diganti sesuai kebutuhan
$lembaga = "LEMBAGA"; // nama lembaga, bisa diganti
$res = $conn->query("SELECT nomor FROM surat WHERE YEAR(tanggal)=YEAR(NOW()) ORDER BY id DESC LIMIT 1");
$last = $res->fetch_assoc();
if($last){
    $parts = explode("/", $last['nomor']);
    $num = (int) end($parts);
    $next_number = $num + 1;
} else {
    $next_number = 1;
}
$nomor = date("Y") . "/$code/$lembaga/" . str_pad($next_number, 3, "0", STR_PAD_LEFT);

// Handle POST
if(isset($_POST['submit'])){
    $judul = $conn->real_escape_string($_POST['judul']);
    $kategori_id = (int)$_POST['kategori_id'];
    $error = '';

    if(isset($_FILES['file_pdf']) && $_FILES['file_pdf']['error'] === UPLOAD_ERR_OK){
        $tmp_name = $_FILES['file_pdf']['tmp_name'];
        $file_name = time().'_'.basename($_FILES['file_pdf']['name']);
        $target = $upload_dir . $file_name;

        if(!move_uploaded_file($tmp_name, $target)){
            $error = "Gagal upload file PDF!";
        }
    } else {
        $error = "File PDF wajib diupload!";
    }

    if(empty($error)){
        $stmt = $conn->prepare("INSERT INTO surat (nomor, judul, kategori_id, file_pdf, tanggal) VALUES (?, ?, ?, ?, NOW())");
        if(!$stmt){
            die("Prepare failed: ".$conn->error);
        }
        $stmt->bind_param("ssis", $nomor, $judul, $kategori_id, $file_name);
        $stmt->execute();

        $_SESSION['success'] = "Surat berhasil diarsipkan!";
        header("Location: arsipkan.php"); exit;
    } else {
        $_SESSION['error'] = $error;
        header("Location: arsipkan.php"); exit;
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Arsipkan Surat</title>
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
        <li class="nav-item"><a class="nav-link" href="kategori.php">Kategori Surat</a></li>
        <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
      </ul>
    </nav>

    <main class="col-10 p-4">
      <div class="form-wrap">
        <h3>📄 Arsipkan Surat</h3>

        <?php if(isset($_SESSION['success'])): ?>
          <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if(isset($_SESSION['error'])): ?>
          <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form action="arsipkan.php" method="POST" enctype="multipart/form-data">
          <div class="mb-3">
            <label class="form-label">Nomor Surat</label>
            <input type="text" name="nomor" class="form-control" value="<?= $nomor ?>" readonly>
          </div>

          <div class="mb-3">
            <label class="form-label">Judul</label>
            <input type="text" name="judul" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Kategori</label>
            <select name="kategori_id" class="form-select" required>
              <option value="">-- Pilih Kategori --</option>
              <?php while($k=$kategori_res->fetch_assoc()): ?>
                <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama']) ?></option>
              <?php endwhile; ?>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Upload File PDF</label>
            <input type="file" name="file_pdf" class="form-control" accept="application/pdf" required>
          </div>

          <button type="submit" class="btn btn-primary" name="submit">Simpan</button>
          <a href="index.php" class="btn btn-secondary">Kembali</a>
        </form>
      </div>
    </main>
  </div>
</div>
</body>
</html>
