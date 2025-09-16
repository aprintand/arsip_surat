<?php
session_start();
include "koneksi.php";

// Ambil kategori
$kategori = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");

// Proses simpan
if (isset($_POST['simpan'])) {
    $judul = $_POST['judul'];
    $kategori_id = $_POST['kategori'];
    $tanggal = $_POST['tanggal'];

    // upload file
    $file_name = $_FILES['file']['name'];
    $file_tmp  = $_FILES['file']['tmp_name'];
    $ext = pathinfo($file_name, PATHINFO_EXTENSION);

    if ($ext != "pdf") {
        $_SESSION['success'] = "File harus PDF!";
        header("Location: index.php");
        exit;
    }

    $new_name = time() . "_" . $file_name;
    move_uploaded_file($file_tmp, "upload/" . $new_name);

    $q = mysqli_query($conn, "INSERT INTO surat (judul,kategori_id,tanggal,file_pdf) VALUES ('$judul','$kategori_id','$tanggal','$new_name')");

    if ($q) {
        $_SESSION['success'] = "Surat berhasil disimpan!";
    } else {
        $_SESSION['success'] = "Gagal menyimpan surat!";
    }
    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Arsip Surat</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
  <h3 class="mb-3">📌 Arsipkan Surat Baru</h3>
  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label>Judul</label>
      <input type="text" name="judul" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Kategori</label>
      <select name="kategori" class="form-control" required>
        <option value="">-- Pilih Kategori --</option>
        <?php while($k=mysqli_fetch_assoc($kategori)): ?>
          <option value="<?= $k['id'] ?>"><?= $k['nama_kategori'] ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="mb-3">
      <label>Tanggal</label>
      <input type="date" name="tanggal" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>File (PDF)</label>
      <input type="file" name="file" class="form-control" accept="application/pdf" required>
    </div>
    <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
    <a href="index.php" class="btn btn-secondary">Kembali</a>
  </form>
</div>
</body>
</html>
