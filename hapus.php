<?php
include 'koneksi.php';
header('Content-Type: application/json');

if(isset($_POST['id'])){
    $id = (int)$_POST['id'];

    // Ambil data file PDF
    $stmt = $conn->prepare("SELECT file_pdf FROM surat WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $data = $res->fetch_assoc();

    if($data){
        $filePath = "uploads/".$data['file_pdf'];
        if(file_exists($filePath)){
            unlink($filePath); // hapus file fisik
        }

        // Hapus data dari database
        $stmt = $conn->prepare("DELETE FROM surat WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        echo json_encode(['status'=>'success']);
        exit;
    }
}

echo json_encode(['status'=>'error']);
exit;
