<?php
// 1. Menghubungkan ke database
// Pastikan file 'koneksi.php' Anda berisi variabel koneksi $conn.
include 'koneksi.php';

// 2. Validasi ID dari URL
// Memastikan parameter 'id' ada di URL.
if (!isset($_GET['id'])) {
    die('KESALAHAN: ID pendaftaran tidak ditemukan.');
}

// Mengambil ID dan mengubahnya menjadi integer untuk keamanan.
$id = intval($_GET['id']);
if ($id <= 0) {
    die('KESALAHAN: ID pendaftaran tidak valid.');
}


// 3. Mengambil data dari database
// Query menggunakan JOIN untuk mengambil nama event dari tabel 'events'.
$query = "SELECT p.*, e.nama_event FROM peserta p JOIN events e ON p.event_id = e.id WHERE p.id = $id";
$result = mysqli_query($conn, $query);

// Menangani jika query gagal
if (!$result) {
    die("Query Gagal: " . mysqli_error($conn));
}

// Mengambil data sebagai array asosiatif.
$data = mysqli_fetch_assoc($result);

// Menangani jika data dengan ID tersebut tidak ditemukan.
if (!$data) {
    die('Data pendaftaran untuk ID ' . htmlspecialchars($id) . ' tidak ditemukan.');
}

// 4. Fungsi Helper untuk Status
// Fungsi untuk menentukan kelas CSS berdasarkan status pembayaran.
function getStatusClass($status)
{
    // Mengubah status menjadi huruf kecil untuk perbandingan yang konsisten.
    switch (strtolower($status)) {
        case 'lunas':
        case 'valid':
            return 'status-valid';
        case 'menunggu pembayaran':
        case 'pending':
            return 'status-pending';
        case 'gagal':
        case 'batal':
            return 'status-gagal';
        default:
            return ''; // Kelas default jika status tidak dikenali.
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <title>Bukti Pendaftaran - <?= htmlspecialchars($data['nama']) ?> - XYZTour</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #005A9C;
            --secondary-blue: #4F98CA;
            --accent-orange: #F57C00;
            --bg-light: #f4f7f9;
            --text-dark: #333;
            --text-light: #777;
            --border-color: #e9ecef;
            --status-valid-bg: #e7f5e9;
            --status-valid-text: #2e7d32;
            --status-pending-bg: #fff8e1;
            --status-pending-text: #ff8f00;
            --status-gagal-bg: #fdecea;
            --status-gagal-text: #d32f2f;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            margin: 0;
            padding: 40px 20px;
        }

        .container {
            max-width: 500px;
            margin: 0 auto;
        }

        .receipt-box {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .receipt-header {
            background: var(--primary-blue);
            padding: 25px 30px;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .receipt-header .logo {
            font-size: 1.8em;
            font-weight: 700;
        }

        .receipt-header .logo i {
            color: var(--accent-orange);
        }

        .receipt-header .receipt-title {
            text-align: right;
            font-size: 0.9em;
            line-height: 1.4;
        }

        .receipt-body {
            padding: 30px;
        }

        .section-title {
            font-size: 1.1em;
            font-weight: 600;
            color: var(--primary-blue);
            margin-bottom: 20px;
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 10px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            margin-bottom: 18px;
            font-size: 0.95em;
        }

        .detail-item i {
            color: var(--secondary-blue);
            width: 25px;
            font-size: 1.1em;
            margin-right: 15px;
        }

        .detail-item .label {
            color: var(--text-light);
            width: 150px;
        }

        .detail-item .value {
            font-weight: 500;
            flex: 1;
        }

        .status {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85em;
            text-align: center;
        }

        .status-valid {
            background-color: var(--status-valid-bg);
            color: var(--status-valid-text);
        }

        .status-pending {
            background-color: var(--status-pending-bg);
            color: var(--status-pending-text);
        }

        .status-gagal {
            background-color: var(--status-gagal-bg);
            color: var(--status-gagal-text);
        }

        .qr-section {
            text-align: center;
            padding: 20px;
            border-top: 2px dashed var(--border-color);
            margin-top: 20px;
        }

        .qr-section img {
            border: 5px solid var(--border-color);
            border-radius: 8px;
        }

        .qr-section p {
            font-size: 0.8em;
            color: var(--text-light);
            margin-top: 10px;
        }

        .receipt-footer {
            background-color: var(--bg-light);
            padding: 20px 30px;
            text-align: center;
            font-size: 0.85em;
            color: var(--text-light);
        }

        .actions {
            text-align: center;
            margin-top: 30px;
        }

        .print-btn {
            display: inline-block;
            background: var(--accent-orange);
            color: #fff;
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1em;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(245, 124, 0, 0.3);
        }

        .print-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(245, 124, 0, 0.4);
        }

        @media print {
            body {
                padding: 0;
                background-color: #fff;
            }

            .actions {
                display: none;
            }

            .container {
                max-width: 100%;
                margin: 0;
            }

            .receipt-box {
                box-shadow: none;
                border: 1px solid var(--border-color);
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="receipt-box">
            <div class="receipt-header">
                <div class="logo"><i class="fas fa-paper-plane"></i> XYZTour</div>
                <div class="receipt-title">
                    <strong>BUKTI PENDAFTARAN</strong><br>
                    (E-TICKET)
                </div>
            </div>
            <div class="receipt-body">
                <div class="section-title">Detail Peserta</div>
                <div class="detail-item">
                    <i class="fas fa-user fa-fw"></i>
                    <div class="label">Nama Lengkap</div>
                    <div class="value">: <?= htmlspecialchars($data['nama']) ?></div>
                </div>
                <div class="detail-item">
                    <i class="fas fa-envelope fa-fw"></i>
                    <div class="label">Email</div>
                    <div class="value">: <?= htmlspecialchars($data['email']) ?></div>
                </div>
                <div class="detail-item">
                    <i class="fab fa-whatsapp fa-fw"></i>
                    <div class="label">No. WhatsApp</div>
                    <div class="value">: <?= htmlspecialchars($data['no_wa']) ?></div>
                </div>

                <div class="section-title" style="margin-top: 30px;">Detail Perjalanan</div>
                <div class="detail-item">
                    <i class="fas fa-map-marked-alt fa-fw"></i>
                    <div class="label">Destinasi</div>
                    <div class="value">: <?= htmlspecialchars($data['nama_event']) ?></div>
                </div>
                <div class="detail-item">
                    <i class="fas fa-calendar-alt fa-fw"></i>
                    <div class="label">Waktu Daftar</div>
                    <div class="value">: <?= date('d M Y, H:i', strtotime($data['waktu_daftar'])) ?></div>
                </div>
                <div class="detail-item">
                    <i class="fas fa-receipt fa-fw"></i>
                    <div class="label">Status</div>
                    <div class="value">: <span class="status <?= getStatusClass($data['status_pembayaran']) ?>"><?= htmlspecialchars($data['status_pembayaran']) ?></span></div>
                </div>

                <div class="qr-section">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data=XYZTour-Receipt-ID-<?= $data['id'] ?>-<?= urlencode($data['nama']) ?>" alt="QR Code">
                    <p>Pindai kode QR ini untuk verifikasi saat keberangkatan.</p>
                </div>
            </div>
            <div class="receipt-footer">
                Terima kasih telah memilih XYZTour sebagai partner perjalanan Anda.
            </div>
        </div>
        <div class="actions">
            <a href="#" class="print-btn" onclick="window.print(); return false;"><i class="fas fa-print"></i> Cetak / Simpan PDF</a>
        </div>
    </div>
</body>

</html>