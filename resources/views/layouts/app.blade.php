<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aynabar Laravel</title>
    <style>
        :root { color-scheme: light; }
        body { margin: 0; font-family: Arial, Helvetica, sans-serif; background: #f5f5f4; color: #1c1917; }
        .wrap { max-width: 1100px; margin: 0 auto; padding: 16px; }
        .top { background: #fff; border-bottom: 1px solid #e7e5e4; }
        .nav { display: flex; justify-content: space-between; align-items: center; gap: 12px; }
        .nav a, .nav button { font-size: 14px; text-decoration: none; color: #27272a; }
        .nav .btn { background: #18181b; color: #fff; border: 0; border-radius: 8px; padding: 8px 12px; cursor: pointer; }
        .card { background: #fff; border: 1px solid #e7e5e4; border-radius: 12px; padding: 16px; }
        .grid { display: grid; gap: 14px; }
        .grid.cols-3 { grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); }
        input, select, textarea { width: 100%; border: 1px solid #d6d3d1; border-radius: 8px; padding: 9px 10px; box-sizing: border-box; }
        label { display: block; font-size: 14px; margin-top: 10px; }
        .btn { display: inline-block; border-radius: 8px; border: 1px solid #27272a; background: #18181b; color: #fff; padding: 8px 12px; text-decoration: none; }
        .muted { color: #57534e; font-size: 13px; }
        .error { background: #fef2f2; color: #b91c1c; padding: 10px 12px; border-radius: 8px; margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #e7e5e4; text-align: left; padding: 9px; font-size: 13px; }
    </style>
</head>
<body>
    <header class="top">
        <div class="wrap nav">
            <a href="{{ url('/') }}" style="font-weight:700">Aynabar Laravel</a>
            <div style="display:flex; align-items:center; gap:10px;">
                @auth
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <a href="{{ url('/dashboard/create') }}">Create</a>
                    @if (auth()->user()->isSuperadmin())
                        <a href="{{ url('/admin/users') }}">Superadmin</a>
                    @endif
                    <form action="{{ url('/logout') }}" method="post" style="display:inline;">
                        @csrf
                        <button class="btn" type="submit">Logout</button>
                    </form>
                @else
                    <a href="{{ url('/login') }}">Login</a>
                    <a class="btn" href="{{ url('/register') }}">Register</a>
                @endauth
            </div>
        </div>
    </header>
    <main class="wrap">
        @if ($errors->any())
            <div class="error">
                {{ $errors->first() }}
            </div>
        @endif
        @if (session('status'))
            <div class="card" style="margin-top:10px;">{{ session('status') }}</div>
        @endif
        @yield('content')
    </main>
</body>
</html>
