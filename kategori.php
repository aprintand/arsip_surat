<?php
include 'koneksi.php';

// ==== Jika request hapus dari fetch (AJAX) ====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_id'])) {
    header('Content-Type: application/json');
    $hapus_id = intval($_POST['hapus_id']);
    if ($hapus_id > 0) {
        $stmt = $conn->prepare("DELETE FROM kategori WHERE id = ?");
        $stmt->bind_param("i", $hapus_id);
        if ($stmt->execute()) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "msg" => "Gagal eksekusi query."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "msg" => "ID tidak valid."]);
    }
    exit;
}

// ==== Ambil keyword pencarian ====
$q = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';

// ==== Query ambil data ====
$stmt = $q != '' 
    ? $conn->prepare("SELECT * FROM kategori WHERE nama LIKE CONCAT('%', ?, '%') ORDER BY id")
    : $conn->prepare("SELECT * FROM kategori ORDER BY id");

if($q != '') $stmt->bind_param("s", $q);
$stmt->execute();
$res = $stmt->get_result();
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kategori Surat</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body { background:#f5f7fb; }
    .sidebar { min-height:100vh; background:#e9f0ff; }
    .table-wrap { background:white; padding:20px; border-radius:6px }
    .btn-gap { margin-right:6px; }
    .nav-link.active { font-weight:bold; box-shadow: inset 3px 0 0 0 #0d6efd; }
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
      <h2>Kategori Surat</h2>
      <p>Berikut ini adalah kategori yang bisa digunakan untuk memberi label surat. Klik "Tambah" untuk menambahkan kategori baru.</p>

      <div class="mb-3 d-flex">
        <form class="d-flex" method="GET" action="kategori.php">
          <input class="form-control me-2" type="search" name="q" placeholder="Cari kategori..." value="<?= htmlspecialchars($q) ?>">
          <button class="btn btn-primary" type="submit">Cari!</button>
        </form>
      </div>

      <a class="btn btn-success mb-3" href="kategori_form.php">[ + ] Tambah Kategori Baru</a>

      <div class="table-wrap">
        <table class="table table-bordered table-striped">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Nama Kategori</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if($res->num_rows==0): ?>
              <tr><td colspan="3">Tidak ada data.</td></tr>
            <?php else: while($r=$res->fetch_assoc()): ?>
              <tr data-id="<?= $r['id'] ?>">
                <td><?= $r['id'] ?></td>
                <td><?= htmlspecialchars($r['nama']) ?></td>
                <td>
                  <a class="btn btn-sm btn-primary btn-gap" href="kategori_form.php?id=<?= $r['id'] ?>">Edit</a>
                  <button class="btn btn-sm btn-danger btn-hapus" data-id="<?= $r['id'] ?>">Hapus</button>
                </td>
              </tr>
            <?php endwhile; endif; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.btn-hapus').forEach(btn => {
  btn.addEventListener('click', function(){
    const id = this.dataset.id;
    Swal.fire({
      title: 'Apakah Anda yakin ingin menghapus kategori ini?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ya!',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if(result.isConfirmed){
        fetch('kategori.php', {
          method: 'POST',
          headers: {'Content-Type':'application/x-www-form-urlencoded'},
          body: 'hapus_id=' + id
        })
        .then(r => r.json())
        .then(data => {
          if(data.status === 'success'){
            const tr = document.querySelector('tr[data-id="'+id+'"]');
            if(tr) tr.remove();
            Swal.fire('Terhapus!', 'Kategori telah dihapus.', 'success');
          } else {
            Swal.fire('Error', data.msg || 'Gagal menghapus kategori.', 'error');
          }
        })
        .catch(e=>{
          Swal.fire('Error', 'Gagal menghapus kategori.', 'error');
        });
      }
    });
  });
});
</script>
</body>
</html>
