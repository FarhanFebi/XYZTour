<?php
session_start();
if(!isset($_SESSION['admin'])){ header("Location: admin_login.php"); exit; }
include 'koneksi.php';
if(isset($_GET['id']) && isset($_GET['aksi'])){
    $id = intval($_GET['id']);
    $aksi = $_GET['aksi'];
    $status = ($aksi == 'valid') ? 'valid' : 'invalid';
    mysqli_query($conn, "UPDATE peserta SET status_pembayaran='$status' WHERE id=$id");
}
header('Location: admin_dashboard.php');
exit;
?>
