<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Rizhaqi Laundry | Home</title>
  <link rel="stylesheet" href="{{ asset('css/home.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://kit.fontawesome.com/a2d9d6a8f0.js" crossorigin="anonymous"></script>

  <style>
    html { scroll-behavior: smooth; }
  </style>
</head>

<body>

    <main>

      <!-- HERO SECTION -->
      <section class="hero"
        style="
          background:
            linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)),
            url('{{ asset('images/bg-home.jpg') }}') center/cover no-repeat;
        ">
        
        <!-- NAVBAR -->
        <div class="hero-nav">
          <div class="logo">
            <img src="{{ asset('/images/logo.jpg') }}" class="logo-img">
            <span>Rizhaqi Laundry</span>
          </div>

          <ul class="nav-link">
              <li><a href="#home" class="active">Home</a></li>
              <li><a href="{{ url('/status') }}">Cek Status</a></li>
              <li><a href="{{ url('/login') }}">Login Admin</a></li>
          </ul>
        </div>

        <div class="content" id="home">
          <h1 style="font-size: 50px">Selamat Datang di </h1>
          <h2 style="font-size: 100px">RIZHAQI LAUNDRY</h2><br>
          <p>Layanan laundry cepat, bersih, dan terpercaya.</p>

          <div class="home-buttons">
            <a href="{{ route('status') }}" class="btn-outline">Cek Status</a>
          </div>
        </div>

      </section>

      <section class="why-section fade-element">
        <h2 class="section-home">Mengapa Memilih Rizhaqi Laundry?</h2>
        <p class="section-subtitle">Kami memberikan layanan terbaik untuk setiap pelanggan.</p>

        <div class="card-list">
          <div class="card fade-element">
            <i class="fa-solid fa-wind icon" style="font-size: 30px; margin-bottom: 10px;"></i>
            <h3>Bersih & Wangi</h3>
            <p>Pakaian dicuci menggunakan deterjen premium dan pewangi tahan lama.</p>
          </div>
          <div class="card fade-element">
            <i class="fas fa-bolt icon" style="font-size: 30px; margin-bottom: 10px;"></i>
            <h3>Cepat & Tepat</h3>
            <p>Layanan express untuk Anda yang butuh hasil kilat dengan kualitas terbaik.</p>
          </div>
          <div class="card fade-element">
            <i class="fas fa-tags icon" style="font-size: 30px; margin-bottom: 10px;"></i>
            <h3>Harga Terjangkau</h3>
            <p>Harga ramah di kantong tanpa mengorbankan hasil maksimal.</p>
          </div>
        </div>
      </section>

      <!-- daftar harga-->
      <section id="harga-section" class="harga-section fade-element" style="padding-top: 5rem;">
        <h2 class="section-title2">Daftar Harga Rizhaqi Laundry</h2><br>

        <div class="card-list">

          <!-- Regular Services -->
          <div class="card fade-element">
            <h3>Regular Services</h3>
            <ul>
              <li><span>Cuci Kering Setrika - Pakaian</span><span>Rp10.000 / Kg</span></li>
              <li><span>CKS - Pakaian Dalam</span><span>Rp14.500 / Kg</span></li>
              <li><span>CKS - Sprei / Selimut / B.Cover</span><span>Rp14.000 / Kg</span></li>
              <li><span>CKS - Fitrasi / Gordyn</span><span>Rp14.000 / Kg</span></li>
              <li><span>Setrika</span><span>Rp6.000 / Kg</span></li>
            </ul>
          </div>

          <!-- Cuci Satuan -->
          <div class="card fade-element">
            <h3>Cuci Satuan & Karpet</h3>
            <ul>
              <li><span>Pakaian</span><span>Rp15.000 / Pcs</span></li>
              <li><span>Jas</span><span>Rp20.000 – 35.000 / Pcs</span></li>
              <li><span>Kebaya / Gaun</span><span>Rp30.000 – 50.000 / Pcs</span></li>
              <li><span>Karpet Tipis</span><span>Rp18.500 / m²</span></li>
              <li><span>Karpet Tebal / Berbulu</span><span>Rp20.000 / m²</span></li>
            </ul>
          </div>

          <!-- Package -->
          <div class="card fade-element">
            <h3>Package Services</h3>
            <ul>
              <li><span>Cuci Kering</span><span>Rp20.000 / 6Kg</span></li>
              <li><span>Cuci Kering Lipat</span><span>Rp25.000 / 6Kg</span></li>
              <li><span>Setrika</span><span>Rp250.000 / 50Kg</span></li>
            </ul>
          </div>

          <!-- Add On -->
          <div class="card fade-element">
            <h3>Add On</h3>
            <ul>
              <li><span>Ekspress</span><span>Rp5.000 / Kg</span></li>
              <li><span>Hanger</span><span>Rp3.000 / Pcs</span></li>
              <li><span>Plastik</span><span>Rp3.000 / Pcs</span></li>
              <li><span>Hanger + Plastik</span><span>Rp5.000 / Pcs</span></li>
            </ul>
          </div>

          <!-- Discount -->
          <div class="card fade-element">
            <h3>Discount Selasa Ceria</h3>
            <ul>
              <li><span>CKS - Pakaian</span><span>Rp9.500</span></li>
              <li><span>CKS - Sprei/Selimut/B.Cover</span><span>Rp13.500</span></li>
            </ul>
          </div>

          <div class="card fade-element">
            <h3>Discount Jumat Berkah</h3>
            <ul>
              <li><span>CKS - Pakaian</span><span>Rp9.000</span></li>
              <li><span>CKS - Sprei/Selimut/B.Cover</span><span>Rp12.000</span></li>
              <li><span>Setrika</span><span>Rp5.000</span></li>
            </ul>
          </div>

        </div>
      </section>

    </main>

    <!-- footer -->
    <footer>
      <div class="footer-container">
        <div class="footer-left">
          <p>&copy; 2025 Rizhaqi Laundry. All rights reserved.</p>
        </div>

        <div class="footer-right">
          <p>
            <i class="fas fa-envelope"></i>
            <a href="mailto:rizhaqilaundry@gmail.com"> rizhaqilaundry@gmail.com</a> |
            <i class="fab fa-whatsapp"></i>
            <a href="https://wa.me/6281234567890"> +62 812-3456-7890</a> |
            <i class="fab fa-instagram"></i>
            <a href="https://instagram.com/rizhaqilaundry"> @rizhaqilaundry</a>
          </p>
        </div>
      </div>
    </footer>

    <!-- fade in animation -->
    <script>
      const fadeElements = document.querySelectorAll('.fade-element');
      const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
          if (entry.isIntersecting) entry.target.classList.add('fade-in');
        });
      });
      fadeElements.forEach(el => observer.observe(el));
    </script>


</body>

</html>
