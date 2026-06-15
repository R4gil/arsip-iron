<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | IRON SMART</title>
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/font-awesome/css/all.min.css') }}">
    <style>
        body { min-height: 100vh; display: grid; place-items: center; background: linear-gradient(135deg, #f6f8fd 100%); }
        .login-box { max-width: 460px; width: 100%; background: #fff; border-radius: 1rem; box-shadow: 0 28px 75px rgba(0,0,0,.15); }
        .login-header { background: #1e3a8a; color: #fff; border-top-left-radius: 1rem; border-top-right-radius: 1rem; padding: 2rem; }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="login-header text-center">
            <h2 class="mb-1">IRON SMART</h2>
            <p class="mb-0 text-white-75">Imigrasi Cirebon - Sistem Monitoring Arsip Terintegtasi</p>
        </div>
        <div class="p-4">
            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login.perform') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" value="{{ old('username') }}" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-4 form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Ingat saya</label>
                </div>
                <button type="submit" class="btn w-100" style="background-color: #1e3a8a; color: white;">Masuk</button>
            </form>
        </div>
    </div>
</body>
</html>
