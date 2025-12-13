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

<body>>

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

                <button type="submit">Masuk</button>
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
        let sliderText = document.getElementById("sliderText");
        if (sliderText) {
            sliderText.textContent = "Silahkan login akun anda";
        }
    </script>

</body>

</html>