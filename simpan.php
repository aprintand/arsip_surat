<?php
session_start();
include "koneksi.php";

// Folder upload
$upload_dir = "uploads/";
if(!is_dir($upload_dir)){
    mkdir($upload_dir, 0777, true); // buat folder jika belum ada
}

// Tangkap data POST
$judul = isset($_POST['judul']) ? $conn->real_escape_string($_POST['judul']) : '';
$kategori = isset($_POST['kategori']) ? (int)$_POST['kategori'] : 0;
$tanggal = isset($_POST['tanggal']) ? $_POST['tanggal'] : '';

$file_pdf = '';
if(isset($_FILES['file_pdf']) && $_FILES['file_pdf']['error'] === UPLOAD_ERR_OK){
    $tmp_name = $_FILES['file_pdf']['tmp_name'];
    $file_name = time().'_'.basename($_FILES['file_pdf']['name']);
    $file_pdf = $upload_dir . $file_name;
    if(!move_uploaded_file($tmp_name, $file_pdf)){
        $_SESSION['success'] = "Gagal upload file PDF!";
        header("Location: arsipkan.php");
        exit;
    }
}

// Insert ke database
$stmt = $conn->prepare("INSERT INTO surat (judul, kategori_id, tanggal, file_pdf) VALUES (?, ?, ?, ?)");
if(!$stmt){
    die("Error prepare statement: ".$conn->error);
}
$stmt->bind_param("siss", $judul, $kategori, $tanggal, $file_name);
$stmt->execute();

$_SESSION['success'] = "Surat berhasil diarsipkan!";
header("Location: arsipkan.php");
exit;
