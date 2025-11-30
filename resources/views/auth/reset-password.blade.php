<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password - Kasir App</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        body { background:#f4f6fb; margin:0; font-family:Inter, ui-sans-serif, system-ui; }
        .wrap { min-height:100vh; display:grid; place-items:center; padding:20px; }
        .card { width:100%; max-width:1000px; background:#fff; border-radius:22px; overflow:hidden; box-shadow:0 24px 60px rgba(43,53,116,.15); display:grid; grid-template-columns:1fr 1fr; }
        .left { padding:60px 56px; }
        .brand { display:flex; align-items:center; gap:10px; font-weight:700; color:#6d28d9; margin-bottom:36px; }
        .brand .dot { width:10px; height:10px; border-radius:50%; background:#7c3aed; }
        h1 { font-size:40px; font-weight:800; margin:0 0 18px; letter-spacing:-.02em; color:#0f172a; }
        p.desc { color:#6b7280; margin:0 0 26px; font-size:14px; }
        label { display:block; font-size:13px; font-weight:600; margin-bottom:8px; color:#111827; }
        input { width:100%; padding:14px 16px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px; outline:none; transition:.2s; margin-bottom:14px; }
        input:focus { border-color:#7c3aed; box-shadow:0 0 0 4px rgba(124,58,237,.12); }
        .btn { width:100%; padding:14px 18px; border:none; border-radius:10px; background:#EA580C; color:#fff; font-weight:700; cursor:pointer; margin-top:8px; box-shadow:0 12px 30px rgba(124,58,237,.25); transition:.2s; }
        .btn:hover { background:#6d28d9; }
        .error { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:12px 14px; border-radius:10px; font-size:13px; margin-bottom:18px; }
        .right { background:radial-gradient(1200px 800px at 60% 40%,  #EA580C, #fb923c 60%, #fdba74); position:relative; }
        .art { position:absolute; inset:0; }
        .art img { width:100%; height:100%; object-fit:cover; }
        @media (max-width:1024px){ .card{ grid-template-columns:1fr; } .right{ display:none; } .left{ padding:46px 28px; } }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            <div class="left">
                <div class="brand"><span class="dot"></span><span>KASIR</span></div>
                <h1>Reset Password</h1>
                <p class="desc">Masukkan password baru Anda. Pastikan kuat dan mudah diingat.</p>
                @if ($errors->any())
                    <div class="error">
                        <strong>Terjadi kesalahan:</strong>
                        <ul style="margin:8px 0 0 18px; padding:0;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('password.store') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
                    <label for="password">Password Baru</label>
                    <input id="password" type="password" name="password" required autocomplete="new-password">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
                    <button class="btn" type="submit">Simpan Password</button>
                    <div style="text-align:center; margin-top:24px; font-size:13px; color:#6b7280;">
                        <a href="{{ route('login') }}" style="color:#7c3aed; font-weight:600; text-decoration:none;">Kembali ke Login</a>
                    </div>
                </form>
            </div>
            <div class="right"><div class="art"><img src="{{ asset('images/login.png') }}" alt="Reset Illustration"></div></div>
        </div>
    </div>
</body>
</html>
