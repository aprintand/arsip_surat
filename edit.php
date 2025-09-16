<?php
session_start();
include "koneksi.php";

$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM surat WHERE id='$id'"));
$kategori = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");

if (isset($_POST['update'])) {
    $judul = $_POST['judul'];
    $kategori_id = $_POST['kategori'];
    $tanggal = $_POST['tanggal'];

    // update file jika ada
    if ($_FILES['file']['name'] != "") {
        $file_name = $_FILES['file']['name'];
        $file_tmp  = $_FILES['file']['tmp_name'];
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        if ($ext != "pdf") {
            $_SESSION['success'] = "File harus PDF!";
            header("Location: index.php"); exit;
        }
        $new_name = time() . "_" . $file_name;
        move_uploaded_file($file_tmp, "upload/" . $new_name);
        mysqli_query($conn, "UPDATE surat SET judul='$judul', kategori_id='$kategori_id', tanggal='$tanggal', file_pdf='$new_name' WHERE id='$id'");
    } else {
        mysqli_query($conn, "UPDATE surat SET judul='$judul', kategori_id='$kategori_id', tanggal='$tanggal' WHERE id='$id'");
    }

    $_SESSION['success'] = "Surat berhasil diperbarui!";
    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Arsip Surat</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
  <h3 class="mb-3">✏️ Edit Arsip Surat</h3>
  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label>Judul</label>
      <input type="text" name="judul" class="form-control" value="<?= $data['judul'] ?>" required>
    </div>
    <div class="mb-3">
      <label>Kategori</label>
      <select name="kategori" class="form-control" required>
        <?php while($k=mysqli_fetch_assoc($kategori)): ?>
          <option value="<?= $k['id'] ?>" <?= $k['id']==$data['kategori_id']?'selected':'' ?>>
            <?= $k['nama_kategori'] ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="mb-3">
      <label>Tanggal</label>
      <input type="date" name="tanggal" class="form-control" value="<?= $data['tanggal'] ?>" required>
    </div>
    <div class="mb-3">
      <label>File (PDF, optional)</label>
      <input type="file" name="file" class="form-control" accept="application/pdf">
    </div>
    <button type="submit" name="update" class="btn btn-primary">Update</button>
    <a href="index.php" class="btn btn-secondary">Kembali</a>
  </form>
</div>
</body>
</html>
