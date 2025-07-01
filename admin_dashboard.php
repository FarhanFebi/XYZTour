<?php
session_start();
// Jika tidak ada session admin, redirect ke halaman login
if(!isset($_SESSION['admin'])){
    header("Location: admin_login.php");
    exit;
}
// Sertakan file koneksi database
include 'koneksi.php';

// --- PROSES FILTER DATA ---
$where = "1=1"; // Kondisi dasar untuk query
// Ambil nilai filter dari URL, jika ada
$filter_event = isset($_GET['event']) ? intval($_GET['event']) : 0;
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Bangun klausa WHERE berdasarkan filter yang dipilih
if ($filter_event > 0) {
    $where .= " AND p.event_id = $filter_event";
}
if ($filter_status != '') {
    // Gunakan mysqli_real_escape_string untuk keamanan
    $where .= " AND p.status_pembayaran = '".mysqli_real_escape_string($conn, $filter_status)."'";
}
if ($search != '') {
    $safe_search = mysqli_real_escape_string($conn, $search);
    $where .= " AND (LOWER(p.nama) LIKE LOWER('%$safe_search%') OR LOWER(p.email) LIKE LOWER('%$safe_search%'))";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - XYZTour</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* CSS Variables untuk tema warna yang mudah disesuaikan */
        :root {
            --blue-primary: #0D47A1; /* Biru tua sebagai warna utama */
            --blue-light: #E3F2FD;
            --blue-hover: #0a3a82;
            --orange-accent: #FF6F00; /* Oranye sebagai warna aksen */
            --orange-hover: #e66400;
            --green-valid: #2E7D32;
            --red-invalid: #C62828;
            --yellow-pending: #FFA000;
            --light-bg: #f5f7fa; /* Latar belakang body yang lebih netral */
            --white-card: #ffffff;
            --text-dark: #333;
            --text-light: #666;
            --border-color: #e0e0e0;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        body {
            font-family: 'Poppins', 'Segoe UI', Arial, sans-serif;
            background-color: var(--light-bg);
            margin: 0;
            color: var(--text-dark);
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
        }

        /* Header Dashboard */
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        .dashboard-header h2 {
            color: var(--blue-primary);
            margin: 0;
            font-weight: 700;
        }
        .dashboard-header .logout-link {
            background-color: var(--blue-light);
            color: var(--blue-primary);
            padding: 8px 15px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .dashboard-header .logout-link:hover {
            background-color: var(--blue-primary);
            color: var(--white-card);
        }

        /* Card Style Umum */
        .card {
            background-color: var(--white-card);
            border-radius: 16px;
            padding: 25px;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
        }
        .card h3 {
            color: var(--blue-primary);
            margin-top: 0;
            margin-bottom: 20px;
            font-weight: 600;
        }

        /* Filter Bar */
        .filter-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
        }
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .filter-bar label {
            color: var(--text-light);
            font-weight: 500;
            font-size: 0.9em;
        }
        .filter-bar select, .filter-bar input[type="text"] {
            padding: 10px 12px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.95em;
            font-family: 'Poppins', sans-serif;
            transition: border-color 0.3s ease;
        }
        .filter-bar select:focus, .filter-bar input[type="text"]:focus {
            outline: none;
            border-color: var(--orange-accent);
        }
        .filter-bar .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-family: 'Poppins', sans-serif;
            align-self: flex-end; /* Sejajarkan dengan bagian bawah input */
        }
        .btn-primary {
            background-color: var(--orange-accent);
            color: var(--white-card);
        }
        .btn-primary:hover {
            background-color: var(--orange-hover);
        }
        .btn-reset {
            background: none;
            color: var(--text-light);
            text-decoration: underline;
            align-self: flex-end;
            padding: 10px;
        }

        /* Tabel Data */
        .table-wrapper {
            overflow-x: auto;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            background-color: var(--white-card);
        }
        th, td {
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
            text-align: left;
        }
        th {
            background-color: var(--blue-primary);
            color: var(--white-card);
            font-weight: 600;
            font-size: 0.9em;
            text-transform: uppercase;
        }
        tr:last-child td {
            border-bottom: none;
        }
        tr:hover {
            background-color: var(--blue-light);
        }

        /* Badge untuk Status */
        .status-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
            color: var(--white-card);
            font-size: 0.8em;
            text-transform: capitalize;
        }
        .status-valid { background-color: var(--green-valid); }
        .status-pending { background-color: var(--yellow-pending); }
        .status-invalid { background-color: var(--red-invalid); }

        /* Link Aksi */
        .aksi-links a {
            color: var(--text-dark);
            font-weight: 500;
            text-decoration: none;
            margin-right: 12px;
            padding: 4px 8px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        .aksi-links a:hover {
            opacity: 0.8;
            color: var(--white-card);
        }
        a.link-valid { background-color: #d4edda; color: var(--green-valid); }
        a.link-valid:hover { background-color: var(--green-valid); }
        a.link-invalid { background-color: #f8d7da; color: var(--red-invalid); }
        a.link-invalid:hover { background-color: var(--red-invalid); }
        a.link-receipt { background-color: #cce5ff; color: var(--blue-primary); }
        a.link-receipt:hover { background-color: var(--blue-primary); }
        a.link-hapus { background-color: #f5c6cb; color: var(--red-invalid); }
        a.link-hapus:hover { background-color: var(--red-invalid); }
        a.link-lihat-bukti { color: var(--orange-accent); font-weight: 600; }

        /* Pesan Not Found */
        .notfound {
            color: var(--red-invalid);
            text-align: center;
            font-weight: 500;
            padding: 40px;
        }
        
        /* Export Button */
        .export-link {
            display: inline-block;
            margin-bottom: 20px;
            background-color: var(--blue-primary);
            color: var(--white-card);
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .export-link:hover {
            background-color: var(--blue-hover);
        }

        /* Responsiveness */
        @media(max-width: 800px) {
            .filter-bar { flex-direction: column; align-items: stretch; }
            .filter-bar .btn, .filter-bar .btn-reset { align-self: auto; }
            .dashboard-header { flex-direction: column; align-items: flex-start; gap: 10px; }
            th, td { padding: 10px; }
        }
    </style>
</head>
<body>
<div class="container">
    <header class="dashboard-header">
        <h2>Admin Dashboard - XYZTour</h2>
        <a class="logout-link" href="logout.php">Logout</a>
    </header>

    <div class="card">
        <h3>Filter & Pencarian Peserta</h3>
        <form method="GET" autocomplete="off">
            <div class="filter-bar">
                <div class="filter-group">
                    <label for="event">Filter Destinasi:</label>
                    <select name="event" id="event">
                        <option value="0">Semua Destinasi</option>
                        <?php
                        $ev_opt = mysqli_query($conn, "SELECT id, nama_event FROM events ORDER BY nama_event");
                        while($e = mysqli_fetch_assoc($ev_opt)){
                            $sel = ($filter_event == $e['id']) ? 'selected' : '';
                            echo "<option value='{$e['id']}' $sel>" . htmlspecialchars($e['nama_event']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="status">Status Pembayaran:</label>
                    <select name="status" id="status">
                        <option value="">Semua Status</option>
                        <option value="pending" <?= ($filter_status == 'pending' ? 'selected' : '') ?>>Pending</option>
                        <option value="valid" <?= ($filter_status == 'valid' ? 'selected' : '') ?>>Valid</option>
                        <option value="invalid" <?= ($filter_status == 'invalid' ? 'selected' : '') ?>>Invalid</option>
                    </select>
                </div>
                <div class="filter-group" style="flex-grow:1;">
                    <label for="search">Cari Nama/Email:</label>
                    <input type="text" name="search" id="search" value="<?= htmlspecialchars($search) ?>" placeholder="Ketik nama atau email..." />
                </div>
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="admin_dashboard.php" class="btn-reset">Reset</a>
            </div>
        </form>
    </div>

    <div class="card">
        <h3>Daftar Peserta & Validasi Pembayaran</h3>
        <a href="export_excel.php?<?= $_SERVER['QUERY_STRING'] ?>" class="export-link" target="_blank">Export ke Excel</a>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No</th><th>Nama</th><th>Email & WA</th>
                        <th>Destinasi</th><th>Status</th><th>Bukti</th><th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "
                        SELECT p.*, e.nama_event
                        FROM peserta p JOIN events e ON p.event_id = e.id
                        WHERE $where
                        ORDER BY p.waktu_daftar DESC
                    ";
                    $query = mysqli_query($conn, $sql);
                    $no = 1;
                    if(mysqli_num_rows($query) > 0) {
                        while($row = mysqli_fetch_assoc($query)){
                            $status_class = 'status-' . strtolower($row['status_pembayaran']);
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td>
                            <?= htmlspecialchars($row['email']) ?><br>
                            <small style="color:var(--text-light);"><?= htmlspecialchars($row['no_wa']) ?></small>
                        </td>
                        <td><?= htmlspecialchars($row['nama_event']) ?></td>
                        <td>
                            <span class="status-badge <?= $status_class ?>">
                                <?= $row['status_pembayaran'] ?>
                            </span>
                        </td>
                        <td>
                            <?php if($row['bukti_bayar']): ?>
                                <a href="uploads/<?= $row['bukti_bayar'] ?>" target="_blank" class="link-lihat-bukti">Lihat</a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="aksi-links">
                            <?php if($row['status_pembayaran'] == 'pending'){ ?>
                                <a class="link-valid" href="validasi.php?id=<?= $row['id'] ?>&aksi=valid" onclick="return confirm('Validasi pembayaran peserta ini?')">Valid</a>
                                <a class="link-invalid" href="validasi.php?id=<?= $row['id'] ?>&aksi=invalid" onclick="return confirm('Tandai pembayaran tidak valid?')">Invalid</a>
                            <?php } ?>
                            <a class="link-receipt" href="receipt.php?id=<?= $row['id'] ?>" target="_blank">Receipt</a>
                            <a class="link-hapus" href="hapus.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus data peserta ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php }
                    } else {
                        echo '<tr><td colspan="7" class="notfound">Tidak ada data ditemukan sesuai filter.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <h3>Monitoring Kuota Event</h3>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Destinasi</th><th>Total Kuota</th><th>Pendaftar Valid</th><th>Sisa Kuota</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $ev_query = mysqli_query($conn, "SELECT id, nama_event, kuota FROM events");
                    while($e = mysqli_fetch_assoc($ev_query)){
                        $event_id = $e['id'];
                        $kuota = $e['kuota'];
                        // Query untuk menghitung pendaftar valid untuk event ini
                        $pendaftar_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM peserta WHERE event_id=$event_id AND status_pembayaran='valid'");
                        $pendaftar_data = mysqli_fetch_assoc($pendaftar_result);
                        $pendaftar = $pendaftar_data['total'];
                        $sisa_kuota = $kuota - $pendaftar;
                        echo "<tr>
                                <td>" . htmlspecialchars($e['nama_event']) . "</td>
                                <td>$kuota</td>
                                <td>$pendaftar</td>
                                <td style='font-weight:bold; color:" . ($sisa_kuota <= 5 ? 'var(--orange-accent)' : 'var(--green-valid)') . ";'>$sisa_kuota</td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>