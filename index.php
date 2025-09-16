<?php
include 'koneksi.php';
$q = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';
$sql = "SELECT s.*, k.nama AS kategori FROM surat s 
        JOIN kategori k ON s.kategori_id=k.id 
        WHERE s.judul LIKE '%$q%' 
        ORDER BY s.id DESC";
$res = $conn->query($sql);
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Arsip Surat</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body { background:#f5f7fb }
    .sidebar { min-height:100vh; background:#e9f0ff }
    .table-wrap { background:white; padding:20px; border-radius:6px }
    .btn-gap { margin-right:6px }
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
      <h2>Arsip Surat</h2>
      <p>Berikut ini adalah surat-surat yang telah terbit dan diarsipkan. Klik "Lihat" pada kolom aksi untuk menampilkan surat.</p>

      <div class="mb-3 d-flex align-items-center">
        <form class="d-flex" method="GET" action="index.php">
          <input class="form-control me-2" type="search" name="q" placeholder="Cari judul..." value="<?= htmlspecialchars($q) ?>">
          <button class="btn btn-primary" type="submit">Cari!</button>
        </form>
      </div>

      <div class="table-wrap">
        <table class="table table-bordered table-striped">
          <thead class="table-light">
            <tr>
              <th>Nomor Surat</th>
              <th>Kategori</th>
              <th>Judul</th>
              <th>Waktu Pengarsipan</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
<?php if($res->num_rows==0): ?>
  <tr><td colspan="5">Tidak ada data.</td></tr>
<?php else: while($row=$res->fetch_assoc()): ?>
  <tr data-id="<?= $row['id'] ?>">
    <td><?= htmlspecialchars($row['nomor']) ?></td>
    <td><?= htmlspecialchars($row['kategori']) ?></td>
    <td><?= htmlspecialchars($row['judul']) ?></td>
    <td><?= htmlspecialchars($row['tanggal']) ?></td>
    <td>
      <a href="uploads/<?= urlencode($row['file_pdf']) ?>" class="btn btn-sm btn-warning btn-gap" download>Unduh</a>
      <a href="lihat.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary btn-gap">Lihat &gt;&gt;</a>
      <button class="btn btn-sm btn-danger btn-hapus" data-id="<?= $row['id'] ?>">Hapus</button>
    </td>
  </tr>
<?php endwhile; endif; ?>
</tbody>

        </table>

        <button class="btn btn-secondary" onclick="location.href='arsipkan.php'">Arsipkan Surat..</button>
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
      title: 'Apakah Anda yakin ingin menghapus arsip surat ini?',
      showCancelButton: true,
      confirmButtonText: 'Ya!',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        fetch('hapus.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'id=' + id
        })
        .then(r => r.json())
        .then(data => {
          if(data.status === 'success'){
            const tr = document.querySelector('tr[data-id="'+id+'"]');
            if(tr) tr.remove();
            Swal.fire('Terhapus!', 'Data telah dihapus.', 'success');
          } else {
            Swal.fire('Error', 'Gagal menghapus.', 'error');
          }
        })
        .catch(e => {
          Swal.fire('Error', 'Gagal menghapus.', 'error');
        });
      }
    });
  });
});
</script>
</body>
</html>
