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

    @if(isset($status_display))
      @php
        // Inisialisasi default untuk kasus TIDAK DITEMUKAN
        $bgColor = '#FBE4E4';
        $textColor = '#C62828';
        $borderColor = '#E57373';
        $statusMessage = 'Kode pesanan tidak ditemukan. Mohon periksa kembali kode transaksi Anda.';

        // Ambil status yang sudah dikelompokkan (hanya ada 5 nilai: diterima, diproses, batal, siap_diambil, selesai)
        $userStatus = $status_for_user ?? 'error';

        if ($isSuccess) {
          switch ($userStatus) {
            // KASUS DIPROSES & DITERIMA (Warna Kuning/Amber: #ffc107)
            case 'diproses':
            case 'diterima':
              $bgColor = '#FFF3CD';
              $textColor = '#856404'; // Dark Amber
              $borderColor = '#FFE082';
              // Pesan lebih deskriptif
              $statusMessage = 'Pesanan Anda saat ini ' . strtolower($status_display) . '. Harap bersabar, kami sedang memproses cucian Anda.';
              break;

            // KASUS SIAP DIAMBIL & SELESAI (Warna Hijau: Sukses)
            case 'siap_diambil':
            case 'selesai':
              $bgColor = '#E8F5E9';
              $textColor = '#2E7D32';  // Dark Green
              $borderColor = '#A5D6A7';
              $statusMessage = ($userStatus == 'selesai')
                ? 'Pesanan Anda telah selesai dan berhasil diambil. Terima kasih!'
                : 'Pesanan Anda sudah siap. Silakan datang ke gerai kami untuk pengambilan.';
              break;

            // KASUS DIBATALKAN (Warna Merah: Sesuai permintaan)
            case 'batal':
              $bgColor = '#FFEBEE';
              $textColor = '#C62828';  // Dark Red
              $borderColor = '#E57373';
              $statusMessage = 'Pesanan Anda telah dibatalkan. Silakan hubungi admin untuk informasi lebih lanjut.';
              break;

            default:
              // Fallback jika ada status baru dari DB yang belum dikelompokkan
              $bgColor = '#E0E0E0';
              $textColor = '#424242';
              $borderColor = '#BDBDBD';
              $statusMessage = 'Status pesanan Anda: ' . $status_display . '';
              break;
          }
        }
      @endphp

      {{-- Container hasil dengan styling dinamis --}}
      <div class="status-result fade-element" style="
              background: {{ $bgColor }}; 
              color: {{ $textColor }}; 
              border: 1px solid {{ $borderColor }};
          ">
        <h3>Kode Pesanan: {{ strtoupper($kode) }}</h3>
        {{-- Tampilkan status yang sudah diformat --}}
        <p>Status Laundry Anda: <strong>{{ $status_display }}</strong></p>

        <p style="color: {{ $textColor }}; font-size: 14px; margin-top: 10px;">
          {!! $statusMessage !!}
        </p>
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