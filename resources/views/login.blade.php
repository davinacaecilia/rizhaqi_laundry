<?php
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    /* ========== LOGIN ========== */
    if ($_POST["form_type"] == "login") {
        $email = $_POST["email"];
        $password = $_POST["password"];

        if ($email == "admin@laundry.com" && $password == "admin123") {
            $message = "Login berhasil! (dummy)";
        } else {
            $message = "Email atau password salah!";
        }

    /* ========== RESET PASSWORD ========== */
    } elseif ($_POST["form_type"] == "reset") {
        $message = "Link reset password telah dikirim ke email Anda! (dummy)";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Login Admin - Rizhaqi Laundry</title>
<link rel="stylesheet" href="{{ asset('admin/css/login.css') }}" />
</head>

<body>

<?php if ($message != ""): ?>
<p class="<?= (str_contains($message, 'berhasil')) ? 'success' : 'error' ?>">
    <?= $message; ?>
</p>
<?php endif; ?>

<div class="wrapper">

    <!-- LOGIN -->
    <div class="form-container login-container">
        <form method="POST">
            <input type="hidden" name="form_type" value="login">

            <h2>Login Akun</h2>

            <div class="input-box">
                <input type="email" name="email" placeholder="Email" required>
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

    <!-- RESET PASSWORD -->
    <div class="form-container reset-container">
        <form method="POST">
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

// Cek dulu supaya tidak error saat goLogin belum ada
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
