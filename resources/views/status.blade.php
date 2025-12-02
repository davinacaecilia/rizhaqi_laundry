<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cek Status | Rizhaqi Laundry</title>
  <link rel="stylesheet" href="{{ asset('css/home.css') }}">
  <script src="https://kit.fontawesome.com/a2d9d6a8f0.js" crossorigin="anonymous"></script>
</head>

<body>

  <!-- NAVBAR MENYATU DENGAN HERO -->
  <div class="hero-nav">
      <div class="logo">
          <img src="{{ asset('/images/logo.jpg') }}" class="logo-img">
          <span>Rizhaqi Laundry</span>
      </div>

      <ul class="nav-links">
           <li><a href="{{ url('/') }}" class="{{ Request::is('/') ? 'active' : '' }}">Home</a></li>
      <li><a href="{{ url('/status') }}" class="{{ Request::is('status') ? 'active' : '' }}">Cek Status</a></li>
      </ul>
  </div>

  <!-- SECTION CEK STATUS -->
  <section class="status-section fade-element">
      <h2 class="section-title">Cek Status Laundry Anda</h2>
      <p class="section-subtitle">Masukkan kode transaksi untuk mengetahui progres laundry Anda.</p>

      <form action="{{ route('status.check') }}" method="POST" class="status-form">
          @csrf
          <input type="text" name="kode" placeholder="Masukkan Kode Transaksi" required>
          <button type="submit" class="btn-primary">Cek Sekarang</button>
      </form>

      @if(isset($status))
      <div class="status-result fade-element">
          <h3>Status Laundry Anda:</h3>
          <p>{{ $status }}</p>
      </div>
      @endif
  </section>
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
