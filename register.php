<?php
// Diasumsikan 'koneksi.php' sudah ada dan berfungsi dengan benar.
// include 'koneksi.php'; 

// Dummy connection for demonstration if koneksi.php is not available
$conn = @mysqli_connect('localhost', 'root', 'farhan762', 'xyztour');
if (!$conn) {
    // echo "Koneksi ke database gagal. Menggunakan data dummy.";
    // This is a fallback to prevent fatal errors if DB connection fails.
    // The main logic will show errors but the page will load.
}

$msg = "";
if(isset($_POST['submit']) && $conn){
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $no_wa = mysqli_real_escape_string($conn, $_POST['no_wa']);
    $event_id = (int)$_POST['event_id'];
    
    // File Upload Handling
    $bukti = $_FILES['bukti']['name'];
    $tmp = $_FILES['bukti']['tmp_name'];
    $folder = "uploads/".$bukti;
    
    // Cek kuota dari database
    $cek_kuota_query = mysqli_query($conn, "SELECT kuota FROM events WHERE id=$event_id");
    $event_data = mysqli_fetch_assoc($cek_kuota_query);
    $kuota = $event_data ? $event_data['kuota'] : 0;

    $cek_peserta_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM peserta WHERE event_id=$event_id AND status_pembayaran='valid'");
    $total_peserta = mysqli_fetch_assoc($cek_peserta_query)['total'];

    if($total_peserta >= $kuota){
        $msg = "Mohon maaf, kuota untuk destinasi ini sudah penuh!";
    } else {
        if(move_uploaded_file($tmp, $folder)){
            $query = "INSERT INTO peserta (nama, email, no_wa, event_id, bukti_bayar) VALUES ('$nama', '$email', '$no_wa', $event_id, '$bukti')";
            if(mysqli_query($conn, $query)) {
                $msg = "Pendaftaran berhasil! Silakan tunggu konfirmasi pembayaran dari tim kami.";
            } else {
                $msg = "Terjadi kesalahan saat menyimpan data ke database.";
            }
        } else {
            $msg = "Upload bukti pembayaran gagal. Silakan coba lagi.";
        }
    }
}

// Data harga, durasi, dan itinerary dengan gambar
$info = [
  1 => [
    'nama' => 'Bandung',
    'harga' => 'Rp 2.000.000',
    'durasi' => '3 hari 2 malam',
    'image' => 'https://images.unsplash.com/photo-1470770841072-f978cf4d019e?auto=format&fit=crop&w=800&q=80', // Gambar diperbaiki
    'rating' => '4.8',
    'discount' => '20%',
    'original_price' => 'Rp 2.500.000',
    'itinerary' => [
      '🌿 Dusun Bambu',
      '🛥️ Floating Market Lembang',
      '🐄 Farmhouse Susu Lembang',
      '🏛️ Jalan Braga',
      '🛍️ Belanja di Cihampelas',
      '🍜 Kuliner khas Bandung'
    ]
  ],
  2 => [
    'nama' => 'Yogyakarta',
    'harga' => 'Rp 3.500.000',
    'durasi' => '4 hari 3 malam',
    'image' => 'https://images.unsplash.com/photo-1596402184320-417e7178b2cd?w=500&q=80',
    'rating' => '4.9',
    'discount' => '15%',
    'original_price' => 'Rp 4.100.000',
    'itinerary' => [
      '🏛️ Candi Borobudur',
      '👑 Keraton Yogyakarta',
      '🛣️ Malioboro',
      '🏖️ Pantai Parangtritis',
      '🌲 Hutan Pinus Mangunan',
      '🍲 Wisata kuliner Gudeg',
      '🥧 Belanja oleh-oleh Bakpia'
    ]
  ]
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>XYZTour - Jelajahi Indonesia Bersama Kami</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #005A9C;
            --secondary-blue: #4F98CA;
            --accent-orange: #F57C00;
            --light-orange: #FF9800;
            --text-dark: #333;
            --text-light: #f8f9fa;
            --bg-light: #FFFFFF;
            --bg-grey: #f1f5f9;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Poppins', sans-serif; 
            background: var(--bg-grey);
            color: var(--text-dark);
        }

        /* HEADER */
        header {
            background: var(--bg-light);
            padding: 15px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .logo {
            font-weight: 700;
            font-size: 1.5em;
            color: var(--primary-blue);
        }
        .logo i { color: var(--accent-orange); }
        .nav-links a {
            margin: 0 15px;
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .nav-links a:hover { color: var(--accent-orange); }
        .header-btn {
            background: linear-gradient(45deg, var(--accent-orange), var(--light-orange));
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(245, 124, 0, 0.3);
            transition: all 0.3s ease;
        }
        .header-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(245, 124, 0, 0.4); }

        .hero-section {
            text-align: center;
            color: white;
            background: linear-gradient(135deg, var(--secondary-blue) 0%, var(--primary-blue) 100%);
            padding: 60px 20px;
        }
        .hero-section h1 { font-size: 2.8em; font-weight: 700; margin-bottom: 10px; text-shadow: 0 2px 4px rgba(0,0,0,0.2); }
        .hero-section p { font-size: 1.2em; opacity: 0.9; font-weight: 300; }
        
        .container {
            background: var(--bg-light);
            max-width: 950px;
            margin: 40px auto;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .form-container { display: grid; grid-template-columns: 1fr 1fr; min-height: 600px; }
        
        .info-section {
            background: linear-gradient(45deg, #0288D1 0%, #01579B 100%);
            padding: 40px 30px;
            color: white;
        }
        
        .section-title { font-size: 1.8em; font-weight: 600; margin-bottom: 25px; }
        
        .destinasi-tabs { display: flex; gap: 15px; margin-bottom: 25px; }
        
        .destinasi-tab {
            cursor: pointer; padding: 12px 20px; border-radius: 25px;
            border: 2px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.1);
            color: white; font-weight: 500; transition: all 0.3s ease; backdrop-filter: blur(10px);
        }
        .destinasi-tab:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .destinasi-tab.active { background: white; color: var(--primary-blue); border-color: white; }
        
        .destination-card {
            background: rgba(255,255,255,0.15); border-radius: 15px; padding: 20px;
            backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);
        }
        .destination-image { width: 100%; height: 150px; border-radius: 10px; object-fit: cover; margin-bottom: 15px; }
        
        .price-section { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
        .current-price { font-size: 1.5em; font-weight: 700; color: #FFD54F; }
        .original-price { text-decoration: line-through; opacity: 0.7; font-size: 0.9em; }
        .discount-badge { background: var(--accent-orange); color: white; padding: 4px 10px; border-radius: 12px; font-size: 0.8em; font-weight: 600; }
        .rating { display: flex; align-items: center; gap: 5px; margin-bottom: 15px; }
        .stars { color: #FFD700; }
        .duration { font-size: 1em; margin-bottom: 15px; opacity: 0.9; }
        .itinerary { list-style: none; }
        .itinerary li { padding: 5px 0; font-size: 0.9em; opacity: 0.9; }
        
        .form-section { padding: 40px 30px; background: white; }
        .form-title { font-size: 1.8em; color: var(--text-dark); margin-bottom: 10px; font-weight: 600; }
        .form-subtitle { color: #666; margin-bottom: 30px; }
        
        .form-group { margin-bottom: 25px; }
        .form-group label { display: block; margin-bottom: 8px; color: var(--text-dark); font-weight: 500; }
        .form-group input, .form-group select {
            width: 100%; padding: 15px; border: 2px solid #e1e5e9; border-radius: 10px;
            font-size: 1em; transition: all 0.3s ease; background: var(--bg-grey);
        }
        .form-group input:focus, .form-group select:focus {
            outline: none; border-color: var(--secondary-blue); background: white;
            box-shadow: 0 5px 15px rgba(79, 152, 202, 0.2);
        }
        
        .file-upload-label {
            display: flex; align-items: center; justify-content: center; gap: 10px; padding: 20px;
            border: 2px dashed var(--secondary-blue); border-radius: 10px; background: #f5fafe;
            color: var(--primary-blue); cursor: pointer; transition: all 0.3s ease;
        }
        .file-upload-label:hover { background: var(--secondary-blue); color: white; }
        .file-upload input[type="file"] { display: none; }
        
        .submit-btn {
            width: 100%; background: linear-gradient(45deg, var(--accent-orange), var(--light-orange)); color: white;
            border: none; border-radius: 10px; padding: 18px 0; font-size: 1.1em;
            font-weight: 600; cursor: pointer; transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(245, 124, 0, 0.3);
        }
        .submit-btn:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(245, 124, 0, 0.4); }
        
        .success-msg, .error-msg {
            color: white; padding: 15px; border-radius: 10px; margin-bottom: 20px;
            text-align: center; font-weight: 500;
        }
        .success-msg { background: linear-gradient(45deg, #2E7D32, #66BB6A); }
        .error-msg { background: linear-gradient(45deg, #D32F2F, #F44336); }
        
        /* FEATURES SECTION */
        .features-section { padding: 50px 5%; text-align: center; }
        .features-title { font-size: 2.2em; margin-bottom: 15px; color: var(--text-dark); }
        .features-subtitle { font-size: 1.1em; color: #666; margin-bottom: 40px; max-width: 600px; margin-left: auto; margin-right: auto;}
        .features { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; }
        .feature-card {
            background: var(--bg-light); padding: 30px 20px; border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.07); transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .feature-card:hover { transform: translateY(-10px); box-shadow: 0 15px 40px rgba(0,0,0,0.1); }
        .feature-icon { font-size: 3em; color: var(--primary-blue); margin-bottom: 15px; }
        .feature-title { font-size: 1.2em; font-weight: 600; color: var(--text-dark); margin-bottom: 10px; }
        .feature-desc { color: #666; font-size: 0.9em; line-height: 1.6; }

        /* TESTIMONIALS SECTION */
        .testimonials-section { padding: 50px 5%; background-color: #f8f9fa; }
        .features-title {
            text-align: center;
            font-size: 2em;
            margin-bottom: 20px;
        }
        .features-subtitle {
            text-align: center;
            font-size: 1.1em;
            color: #666;
            margin-bottom: 40px;
        }
        .testimonials-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; }
        .testimonial-card {
            background: var(--bg-light); padding: 30px; border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.07); text-align: center;
        }
        .testimonial-text { font-style: italic; font-size: 1em; color: #555; margin-bottom: 20px; line-height: 1.6; }
        .testimonial-author { font-weight: 600; color: var(--primary-blue); }
        .testimonial-author span { font-size: 0.9em; color: #777; font-weight: 400; display: block; }
        .testimonial-avatar { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-bottom: 15px; border: 4px solid var(--secondary-blue); }


        /* FOOTER */
        footer {
            background-color: var(--primary-blue);
            color: var(--text-light);
            padding: 40px 5%;
        }
        .footer-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 30px;
        }
        .footer-col h4 { font-size: 1.2em; margin-bottom: 20px; position: relative; }
        .footer-col h4::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -5px;
            background-color: var(--accent-orange);
            height: 2px;
            width: 50px;
        }
        .footer-col p, .footer-col a {
            color: #ccc;
            text-decoration: none;
            line-height: 1.8;
            transition: color 0.3s ease;
        }
        .footer-col a:hover { color: var(--accent-orange); padding-left: 5px; }
        .social-links a {
            display: inline-block;
            height: 40px; width: 40px;
            background-color: rgba(255, 255, 255, 0.2);
            margin-right: 10px;
            text-align: center;
            line-height: 40px;
            border-radius: 50%;
            color: white;
            transition: all 0.3s ease;
        }
        .social-links a:hover { background-color: var(--accent-orange); }
        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            margin-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            font-size: 0.9em;
            color: #ccc;
        }
        
        @media (max-width: 992px) {
            .form-container { grid-template-columns: 1fr; }
            .info-section { order: 2; }
            .form-section { order: 1; }
            header { flex-direction: column; gap: 10px; }
        }

        @media (max-width: 768px) {
            .hero-section h1 { font-size: 2em; }
            .container { margin: 20px 15px; }
            .features-section, .testimonials-section, footer { padding: 40px 20px; }
            .nav-links { display: none; } /* Simple hide for mobile, can be replaced with hamburger menu logic */
        }
    </style>
</head>
<body>
    <header>
        <div class="logo"><i class="fas fa-paper-plane"></i> XYZTour</div>
        <nav class="nav-links">
            <a href="#home">Home</a>
            <a href="#destinasi">Destinasi</a>
            <a href="#fitur">Fitur</a>
            <a href="#testimoni">Testimoni</a>
        </nav>
        <a href="#form-pendaftaran" class="header-btn">Daftar Sekarang</a>
    </header>

    <div id="home" class="hero-section">
        <h1>Jelajahi Keindahan Indonesia</h1>
        <p>Pengalaman wisata tak terlupakan menanti Anda</p>
    </div>
    
    <section id="fitur" class="features-section">
        <h2 class="features-title">Kenapa Memilih Kami?</h2>
        <p class="features-subtitle">Kami memberikan lebih dari sekedar perjalanan. Kami memberikan pengalaman, keamanan, dan kenangan terbaik untuk Anda.</p>
        <div class="features">
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                <div class="feature-title">Aman & Terpercaya</div>
                <div class="feature-desc">Tour guide berpengalaman dan berlisensi, serta perlindungan asuransi perjalanan untuk ketenangan Anda.</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-star"></i></div>
                <div class="feature-title">Destinasi Terbaik</div>
                <div class="feature-desc">Kami telah memilih destinasi wisata terpopuler dan terindah di seluruh penjuru Indonesia.</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-wallet"></i></div>
                <div class="feature-title">Harga Kompetitif</div>
                <div class="feature-desc">Dapatkan penawaran harga terbaik dengan fasilitas lengkap tanpa biaya tersembunyi.</div>
            </div>
        </div>
    </section>

    <div id="destinasi" class="container">
        <div class="form-container">
            <div class="info-section">
                <h2 class="section-title"><i class="fas fa-map-marker-alt"></i> Pilih Destinasi Impian Anda</h2>
                
                <div class="destinasi-tabs">
                    <div class="destinasi-tab active" onclick="showInfo(1)">
                        <i class="fas fa-mountain"></i> Bandung
                    </div>
                    <div class="destinasi-tab" onclick="showInfo(2)">
                        <i class="fas fa-gopuram"></i> Yogyakarta
                    </div>
                </div>
                
                <?php foreach($info as $id => $data): ?>
                <div id="detail<?php echo $id; ?>" class="destination-card" style="display:<?php echo ($id == 1) ? 'block' : 'none'; ?>">
                    <img src="<?php echo $data['image']; ?>" alt="<?php echo $data['nama']; ?>" class="destination-image">
                    <div class="price-section">
                        <span class="current-price"><?php echo $data['harga']; ?></span>
                        <span class="original-price"><?php echo $data['original_price']; ?></span>
                        <span class="discount-badge"><?php echo $data['discount']; ?> OFF</span>
                    </div>
                    <div class="rating">
                        <span class="stars">
                            <?php for($i=0; $i<5; $i++) echo ($i < floor((float)$data['rating'])) ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>'; ?>
                        </span>
                        <span><?php echo $data['rating']; ?> (Berdasarkan ulasan)</span>
                    </div>
                    <div class="duration">
                        <i class="fas fa-clock"></i> <?php echo $data['durasi']; ?>
                    </div>
                    <div class="itinerary-title" style="font-weight: 600; margin-bottom: 10px;">
                        <i class="fas fa-route"></i> Rencana Perjalanan:
                    </div>
                    <ul class="itinerary">
                        <?php foreach($data['itinerary'] as $item): ?>
                            <li><?php echo $item; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div id="form-pendaftaran" class="form-section">
                <h2 class="form-title">Daftar Sekarang Juga!</h2>
                <p class="form-subtitle">Isi form di bawah untuk memulai petualangan Anda.</p>
                
                <?php if($msg != ""): ?>
                    <div class="<?php echo (strpos($msg, 'berhasil') !== false || strpos($msg, 'berhasil') !== false) ? 'success-msg' : 'error-msg'; ?>">
                        <i class="fas fa-<?php echo (strpos($msg, 'berhasil') !== false) ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                        <?php echo $msg; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data" id="tourForm">
                    <div class="form-group">
                        <label for="nama"><i class="fas fa-user"></i> Nama Lengkap</label>
                        <input type="text" id="nama" name="nama" placeholder="Masukkan nama lengkap Anda" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" id="email" name="email" placeholder="contoh@email.com" required>
                    </div>
                    
                    <div class="form-group"> 
                        <label for="no_wa"><i class="fab fa-whatsapp"></i> No. WhatsApp</label>
                        <input type="tel" id="no_wa" name="no_wa" placeholder="08xxxxxxxxxx" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="event_id"><i class="fas fa-map-marked-alt"></i> Pilih Destinasi</label>
                        <select name="event_id" id="event_id" required>
                            <?php
                            // Logic for populating select options from DB
                            if ($conn) {
                                $ev = mysqli_query($conn, "SELECT id, nama_event, kuota FROM events");
                                if(mysqli_num_rows($ev) > 0) {
                                    while($e = mysqli_fetch_assoc($ev)){
                                        // Fetch current participants to calculate remaining quota
                                        $cq = mysqli_query($conn, "SELECT COUNT(*) as total FROM peserta WHERE event_id={$e['id']} AND status_pembayaran='valid'");
                                        $cp = mysqli_fetch_assoc($cq)['total'];
                                        $sisa_kuota = $e['kuota'] - $cp;
                                        echo "<option value='{$e['id']}'>{$e['nama_event']} (Sisa Kuota: {$sisa_kuota})</option>";
                                    }
                                } else {
                                     echo "<option value=''>- Tidak ada event -</option>";
                                }
                            } else {
                                // Fallback if no DB connection
                                echo "<option value='1'>Bandung</option>";
                                echo "<option value='2'>Yogyakarta</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="bukti"><i class="fas fa-receipt"></i> Upload Bukti Pembayaran</label>
                        <div class="file-upload">
                            <input type="file" name="bukti" id="bukti" accept="image/*" required>
                            <label for="bukti" class="file-upload-label">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span id="file-name">Klik untuk upload atau seret file</span>
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" name="submit" class="submit-btn">
                        <i class="fas fa-paper-plane"></i> Kirim Pendaftaran
                    </button>
                </form>
            </div>
        </div>
    </div>

    <section id="testimoni" class="testimonials-section">
         <h2 class="features-title ">Apa Kata Mereka?</h2>
        <p class="features-subtitle">Kepuasan dan kebahagiaan pelanggan adalah prioritas utama bagi kami. Lihat pengalaman mereka yang telah berpetualang bersama XYZTour.</p>
        <div class="testimonials-container">
            <div class="testimonial-card">
                <img src="https://i.pravatar.cc/150?img=1" alt="Author" class="testimonial-avatar">
                <p class="testimonial-text">"Pengalaman tour terbaik yang pernah saya rasakan! Tour guide sangat ramah dan destinasinya luar biasa indah. Pasti akan ikut lagi dengan XYZTour!"</p>
                <div class="testimonial-author">Satria Basudara <span>Jakarta</span></div>
            </div>
            <div class="testimonial-card">
                <img src="https://i.pravatar.cc/150?img=32" alt="Author" class="testimonial-avatar">
                <p class="testimonial-text">"Sangat terorganisir dan profesional. Dari awal pendaftaran sampai akhir tour, semuanya mulus. Anak-anak saya sangat senang. Terima kasih XYZTour!"</p>
                <div class="testimonial-author">Erika Chintya <span>Surabaya</span></div>
            </div>
            <div class="testimonial-card">
                <img src="https://i.pravatar.cc/150?img=45" alt="Author" class="testimonial-avatar">
                <p class="testimonial-text">"Harga yang ditawarkan sangat sepadan dengan fasilitas yang didapat. Pilihan hotel dan makanannya juga enak-enak. Recommended!"</p>
                <div class="testimonial-author">Rina Wijaya <span>Malang</span></div>
            </div>
        </div>
    </section>

    <footer>
        <div class="footer-container">
            <div class="footer-col">
                <h4>Tentang XYZTour</h4>
                <p>XYZTour adalah partner perjalanan Anda untuk menjelajahi keindahan alam dan budaya Indonesia. Kami berkomitmen memberikan pengalaman liburan yang aman, nyaman, dan tak terlupakan.</p>
            </div>
            <div class="footer-col">
                <h4>Navigasi Cepat</h4>
                <p><a href="#home">Home</a></p>
                <p><a href="#destinasi">Destinasi</a></p>
                <p><a href="#fitur">Fitur</a></p>
                <p><a href="#testimoni">Testimoni</a></p>
            </div>
            <div class="footer-col">
                <h4>Hubungi Kami</h4>
                <p><i class="fas fa-map-marker-alt"></i> Jl. Merdeka No. 10, Jakarta</p>
                <p><i class="fas fa-phone"></i> (021) 123-4567</p>
                <p><i class="fas fa-envelope"></i> info@xyztour.com</p>
            </div>
            <div class="footer-col">
                <h4>Ikuti Kami</h4>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; <?php echo date("Y"); ?> XYZTour. All Rights Reserved.
        </div>
    </footer>

    <script>
        function showInfo(id) {
            // Tab highlight
            const tabs = document.querySelectorAll('.destinasi-tab');
            tabs.forEach(tab => tab.classList.remove('active'));
            document.querySelector(`.destinasi-tab:nth-child(${id})`).classList.add('active');

            // Detail info
            const details = document.querySelectorAll('.destination-card');
            details.forEach(detail => detail.style.display = 'none');
            document.getElementById('detail' + id).style.display = 'block';
            
            // Sync select dropdown
            const select = document.getElementById('event_id');
            if (select && select.value != id) {
                select.value = id;
            }
        }

        document.getElementById('event_id').addEventListener('change', function(){
            showInfo(this.value);
        });

        document.getElementById('bukti').addEventListener('change', function(e) {
            const fileName = document.getElementById('file-name');
            if(e.target.files.length > 0) {
                fileName.textContent = e.target.files[0].name;
            } else {
                fileName.textContent = 'Klik untuk upload atau seret file';
            }
        });

        document.getElementById('tourForm').addEventListener('submit', function(e) {
            const phoneInput = document.getElementById('no_wa');
            const phonePattern = /^(^\+62|62|^08)(\d{3,4}-?){2}\d{3,4}$/; // Validasi nomor HP Indonesia
            if(!phonePattern.test(phoneInput.value)) {
                e.preventDefault();
                alert('Format nomor WhatsApp tidak valid! Contoh: 081234567890');
                phoneInput.focus();
                return false;
            }
        });

        // Initialize first tab on load
        window.addEventListener('load', function() {
            showInfo(document.getElementById('event_id').value);
        });
    </script>
</body>
</html>