<?php
include 'koneksi.php';
if(!isset($_GET['id'])){ die('ID tidak ditemukan'); }
$id = intval($_GET['id']);
// Optional: hapus file bukti jika ingin
$namaFile = mysqli_fetch_assoc(mysqli_query($conn,"SELECT bukti_bayar FROM peserta WHERE id=$id"))['bukti_bayar'];
if($namaFile && file_exists("uploads/".$namaFile)) unlink("uploads/".$namaFile);
mysqli_query($conn, "DELETE FROM peserta WHERE id=$id");
header("Location: admin_dashboard.php");
?>
