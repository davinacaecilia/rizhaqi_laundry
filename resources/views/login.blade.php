<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Login Admin</title>
<link rel="stylesheet" href="{{ asset('admin/css/login.css') }}" />

<style>
    .wrapper {
        width: 450px;
        height: auto;
        min-height: 400px;
        position: relative;
        left: 0;
        transform: none;
    }
    .form-container {
        width: 100%;
        position: static;
    }
    .slider-panel, 
    .signup-container {
        display: none; 
    }
    .logo-box {
        text-align: center;
        margin-bottom: 20px;
    }
    .logo-box img {
        width: 100px; 
    }
</style>
</head>

<body>

<div class="wrapper">

    <div class="form-container login-container">
        
        <form method="POST" action="{{ route('login.submit') }}">
            @csrf
            
            <input type="hidden" name="form_type" value="login">

            <div class="logo-box">
                <img src="logo.png" alt="Logo Laundry" />
                <h2>Rizhaqy Laundry</h2>
            </div>
            
            <h2>Login Akun</h2>

            @error('email')
                <p class="error" style="color: red; font-size: 14px; text-align: left;">
                    {{ $message }}
                </p>
            @enderror

            <div class="input-box">
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit">Masuk</button>
            
            </form>
    </div>

    </div>

</body>
</html>