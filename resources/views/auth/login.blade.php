<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Kasir App</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background: #f4f6fb; margin: 0; font-family: Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji"; }
        .wrap { min-height: 100vh; display: grid; place-items: center; padding: 20px; }
        .card { width: 100%; max-width: 1100px; border-radius: 22px; overflow: hidden; background: #fff; box-shadow: 0 24px 60px rgba(43,53,116,.15); display: grid; grid-template-columns: 1fr 1fr; }
        .left { padding: 60px 56px; }
        .brand { display:flex; align-items:center; gap:10px; color:#EA580C; font-weight:700; margin-bottom:36px; }
        .brand .dot { width:10px; height:10px; border-radius:50%; background:#EA580C; }
        .title { font-size: 44px; line-height: 1.15; font-weight: 800; color:#0f172a; letter-spacing:-.02em; }
        .subtitle { color:#6b7280; margin: 14px 0 32px; }
        .group { margin-bottom: 16px; }
        label { display:block; font-size:13px; font-weight:600; margin-bottom:8px; color:#111827; }
        input[type=email], input[type=password] { width:100%; padding:14px 16px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px; outline:none; transition:.2s; }
        input[type=email]:focus, input[type=password]:focus { border-color:#EA580C; box-shadow:0 0 0 4px rgba(234,88,12,.12); }
        .row { display:flex; align-items:center; justify-content:space-between; margin-top:6px; }
        .remember { display:flex; align-items:center; gap:8px; color:#6b7280; font-size:13px; }
        .remember input { width:18px; height:18px; accent-color:#EA580C; }
        .link { color:#EA580C; font-weight:600; text-decoration:none; }
        .link:hover { color:#c2410c; }
        .btn { width:100%; padding:14px 18px; border:none; border-radius:10px; background:#EA580C; color:#fff; font-weight:700; cursor:pointer; margin-top:22px; box-shadow:0 12px 30px rgba(234,88,12,.25); transition:.2s; }
        .btn:hover { background:#c2410c; transform: translateY(-1px); }
        .signup { text-align:center; font-size:13px; color:#6b7280; margin-top:22px; }
        .error { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:12px 14px; border-radius:10px; font-size:13px; margin-bottom:16px; }
        .right { background: radial-gradient(1200px 800px at 60% 40%, #EA580C, #fb923c 60%, #fdba74); position:relative; }
        .art { position:absolute; inset:0; display:flex; }
        .art img { width:100%; height:100%; object-fit:cover; }
        @media (max-width: 1024px){ .card{ grid-template-columns:1fr; } .right{ display:none; } .left{ padding:46px 28px; } }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            <div class="left">
                <div class="brand"><span class="dot"></span><span>KASIR</span></div>
                <h1 class="title">Halo,<br/>Selamat Datang</h1>
                <p class="subtitle">Silahkan masuk dengan akun</p>

                @if ($errors->any())
                    <div class="error">
                        <strong>Login gagal.</strong>
                        <ul style="margin:8px 0 0 18px; padding:0;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="group">
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="stanley@gmail.com" required autofocus autocomplete="username">
                    </div>
                    <div class="group">
                        <label for="password">Password</label>
                        <input id="password" type="password" name="password" placeholder="••••••••••" required autocomplete="current-password">
                    </div>
                    <div class="row">
                        <label class="remember">
                            <input type="checkbox" name="remember">
                            <span>Remember me</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a class="link" href="{{ route('password.request') }}">Forgot Password?</a>
                        @endif
                    </div>
                    <button class="btn" type="submit">Sign In</button>
                    <div class="signup">
                        Don't have an account?
                        @if (Route::has('register'))
                            <a class="link" href="{{ route('register') }}">Sign Up</a>
                        @endif
                    </div>
                </form>
            </div>
            <div class="right">
                <div class="art">
                    <img src="{{ asset('images/login.png') }}" alt="Login Illustration">
                </div>
            </div>
        </div>
    </div>
</body>
</html>
