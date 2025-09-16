<?php
$host = "localhost";
$user = "root";   // default user xampp
$pass = "";       // default password kosong
$db   = "arsip_surat";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
