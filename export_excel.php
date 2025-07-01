<?php
include 'koneksi.php';
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=peserta_xyzTour.xls");

// Filtering sesuai dashboard
$where = "1=1";
$filter_event = isset($_GET['event']) ? intval($_GET['event']) : 0;
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if ($filter_event > 0) $where .= " AND p.event_id = $filter_event";
if ($filter_status != '') $where .= " AND p.status_pembayaran = '".mysqli_real_escape_string($conn,$filter_status)."'";
if ($search != '') {
    $safe_search = mysqli_real_escape_string($conn, $search);
    $where .= " AND (LOWER(p.nama) LIKE LOWER('%$safe_search%') OR LOWER(p.email) LIKE LOWER('%$safe_search%'))";
}

echo "Nama\tEmail\tNo WA\tDestinasi\tStatus Pembayaran\tWaktu Daftar\n";

$sql = "SELECT p.*, e.nama_event FROM peserta p JOIN events e ON p.event_id = e.id WHERE $where ORDER BY p.waktu_daftar DESC";
$res = mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($res)){
    echo "{$row['nama']}\t{$row['email']}\t{$row['no_wa']}\t{$row['nama_event']}\t{$row['status_pembayaran']}\t{$row['waktu_daftar']}\n";
}
?>
