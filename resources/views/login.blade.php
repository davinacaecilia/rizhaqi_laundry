<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Admin - Rizhaqi Laundry</title>
    <link rel="stylesheet" href="{{ asset('admin/css/login.css') }}" />
    
    <style>
        /* CSS Tambahan untuk Notifikasi Laravel */
        .alert {
            padding: 12px;
            margin: 10px 0 20px 0;
            border-radius: 5px;
            text-align: center;
            font-size: 14px;
            width: 100%;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>

<body>

    <div class="wrapper">

        <!-- LOGIN FORM -->
        <div class="form-container login-container">
            <!-- Perhatikan action dan method ini -->
            <form action="{{ route('login.submit') }}" method="POST">
                @csrf <!-- Wajib ada di Laravel untuk keamanan -->
                <input type="hidden" name="form_type" value="login">

                <h2>Login Akun</h2>

                <!-- Menampilkan Pesan Error/Sukses di sini -->
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-error">{{ session('error') }}</div>
                @endif

                <div class="input-box">
                    <input type="email" name="email" placeholder="Email" required value="{{ old('email') }}">
                </div>
                <div class="input-box">
                    <input type="password" name="password" placeholder="Password" required>
                </div>

                <p class="small-link">
                    Lupa password? <span id="goReset">Reset Password</span>
                </p>

                <button type="submit">Masuk</button>
            </form>
        </div>

        <!-- RESET PASSWORD FORM -->
        <div class="form-container reset-container">
            <!-- Action mengarah ke route yang sama -->
            <form action="{{ route('login.submit') }}" method="POST">
                @csrf <!-- Wajib ada -->
                <input type="hidden" name="form_type" value="reset">

                <h2>Reset Password</h2>

                <div class="input-box">
                    <input type="email" name="email" placeholder="Masukkan Email untuk reset" required>
                </div>

                <button type="submit" class="reset-btn">Kirim Link Reset</button>

                <p class="small-link">Kembali ke login? <span id="goLogin">Login</span></p>
            </form>
        </div>

        <!-- SLIDER PANEL -->
        <div class="slider-panel">
            <div class="logo-box">
                <img src="{{ asset('admin/img/logoo.png') }}" alt="Logo Laundry" />
            </div>
            <h2>Selamat Datang!</h2>
            <p id="sliderText">Silahkan login akun anda</p>
        </div>

    </div>

    <script>
        let wrapper = document.querySelector(".wrapper");
        let goReset = document.getElementById("goReset");
        let goLogin = document.getElementById("goLogin");
        let sliderText = document.getElementById("sliderText");

        // Cek dulu supaya tidak error saat element belum ada
        if (goReset) {
            goReset.onclick = () => {
                wrapper.classList.add("active");
                sliderText.textContent = "Silahkan reset password anda";
            };
        }

        if (goLogin) {
            goLogin.onclick = () => {
                wrapper.classList.remove("active");
                sliderText.textContent = "Silahkan login akun anda";
            };
        }
    </script>

</body>
</html>