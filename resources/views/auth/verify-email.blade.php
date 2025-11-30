<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verifikasi Email - Kasir App</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        body { background:#f4f6fb; margin:0; font-family:Inter, ui-sans-serif, system-ui; }
        .wrap { min-height:100vh; display:grid; place-items:center; padding:20px; }
        .card { width:100%; max-width:900px; background:#fff; border-radius:22px; overflow:hidden; box-shadow:0 24px 60px rgba(43,53,116,.15); display:grid; grid-template-columns:1fr 1fr; }
        .left { padding:60px 56px; }
        .brand { display:flex; align-items:center; gap:10px; font-weight:700; color:#6d28d9; margin-bottom:36px; }
        .brand .dot { width:10px; height:10px; border-radius:50%; background:#7c3aed; }
        h1 { font-size:36px; font-weight:800; margin:0 0 18px; letter-spacing:-.02em; color:#0f172a; }
        p.desc { color:#6b7280; margin:0 0 26px; font-size:14px; }
        .status { background:#ecfdf5; border:1px solid #d1fae5; color:#065f46; padding:12px 14px; border-radius:10px; font-size:13px; margin-bottom:18px; }
        .btn { width:100%; padding:14px 18px; border:none; border-radius:10px; background:#EA580C; color:#fff; font-weight:700; cursor:pointer; margin-top:8px; box-shadow:0 12px 30px rgba(124,58,237,.25); transition:.2s; }
        .btn:hover { background:#6d28d9; }
        .logout { margin-top:20px; display:inline-block; font-size:13px; color:#6b7280; text-decoration:none; }
        .logout:hover { color:#374151; }
        form button.btn-inline { background:#7c3aed; color:#fff; padding:10px 16px; border-radius:8px; font-weight:600; border:none; cursor:pointer; }
        form button.btn-inline:hover { background:#6d28d9; }
        .right { background:radial-gradient(1200px 800px at 60% 40%,  #EA580C, #fb923c 60%, #fdba74); position:relative; }
        .art { position:absolute; inset:0; }
        .art img { width:100%; height:100%; object-fit:cover; }
        @media (max-width:900px){ .card{ grid-template-columns:1fr; } .right{ display:none; } .left{ padding:46px 28px; } }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            <div class="left">
                <div class="brand"><span class="dot"></span><span>KASIR</span></div>
                <h1>Verifikasi Email</h1>
                <p class="desc">Terima kasih telah mendaftar! Klik link verifikasi yang sudah kami kirim ke email Anda. Jika belum menerima, kirim ulang di bawah.</p>
                @if (session('status') == 'verification-link-sent')
                    <div class="status">Link verifikasi baru telah dikirim ke email Anda.</div>
                @endif
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="btn">Kirim Ulang Email Verifikasi</button>
                </form>
                <form method="POST" action="{{ route('logout') }}" style="margin-top:26px;">
                    @csrf
                    <button type="submit" class="btn-inline">Logout</button>
                </form>
            </div>
            <div class="right"><div class="art"><img src="{{ asset('images/login.png') }}" alt="Verify Illustration"></div></div>
        </div>
    </div>
</body>
</html>
