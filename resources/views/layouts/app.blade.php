<!doctype html>
<html lang="en" class="h-full bg-neutral-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ShortV1</title>
    <link rel="stylesheet" href="/css/app.css">
    <style>
        :root { --bg:#0a0a0a; --fg:#e5e7eb; --muted:#9ca3af; --card:#111827; --accent:#14b8a6; }
        body{background:var(--bg);color:var(--fg);font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, Noto Sans, Arial, "Apple Color Emoji", "Segoe UI Emoji";}
        a{color:var(--accent);text-decoration:none}
        .container{max-width:1000px;margin:0 auto;padding:2rem}
        .card{background:var(--card);border:1px solid #232323;border-radius:12px;padding:1.5rem;box-shadow:0 10px 30px rgba(0,0,0,.35)}
        .btn{background:var(--accent);color:#001; padding:.6rem 1rem; border-radius:8px; font-weight:600;display:inline-block}
        .btn.outline{background:transparent;color:var(--fg);border:1px solid #2a2a2a}
        input,select,textarea{background:#0f172a;border:1px solid #262b36;color:var(--fg);padding:.6rem .8rem;border-radius:8px;width:100%}
        label{display:block; color:var(--muted); font-size:.9rem; margin-bottom:.35rem}
        .row{display:grid; gap:1rem}
        @media(min-width:768px){ .row.cols-2{grid-template-columns:1fr 1fr} .row.cols-3{grid-template-columns:repeat(3,1fr)} }
        .table{width:100%;border-collapse:collapse}
        .table th,.table td{border-bottom:1px solid #232323;padding:.6rem;text-align:left}
        .muted{color:var(--muted)}
        .topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem}
    </style>
</head>
<body class="h-full">
<div class="container">
    <div class="topbar">
        <div><strong>ShortV1</strong></div>
        <div>
            @if(session('authed'))
            <a href="{{ route('dashboard') }}" class="btn outline">Dashboard</a>
            <a href="{{ route('links.create') }}" class="btn outline">Create</a>
            <form method="post" action="{{ route('logout') }}" style="display:inline">@csrf<button class="btn" style="background:#ef4444;color:#fff">Logout</button></form>
            @endif
        </div>
    </div>
    <div class="card">
        @yield('content')
    </div>
</div>
</body>
</html>