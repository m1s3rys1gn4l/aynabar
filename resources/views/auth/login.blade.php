@extends('layouts.app')

@section('content')
    <div class="card" style="max-width:460px; margin: 20px auto;">
        <h1 style="margin:0;">Login</h1>
        <p class="muted">Access your barcode/QR SaaS dashboard.</p>

        <form action="{{ url('/login') }}" method="post">
            @csrf
            <label>Email
                <input type="email" name="email" required value="{{ old('email') }}">
            </label>
            <label>Password
                <input type="password" name="password" required>
            </label>
            <button class="btn" style="margin-top:12px;" type="submit">Login</button>
        </form>
        <p class="muted" style="margin-top:10px;">No account? <a href="{{ url('/register') }}">Register</a></p>
    </div>
@endsection
