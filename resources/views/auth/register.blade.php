@extends('layouts.app')

@section('content')
    <div class="card" style="max-width:460px; margin: 20px auto;">
        <h1 style="margin:0;">Create account</h1>
        <p class="muted">Start generating static and dynamic codes.</p>

        <form action="{{ url('/register') }}" method="post">
            @csrf
            <label>Name
                <input type="text" name="name" required value="{{ old('name') }}">
            </label>
            <label>Email
                <input type="email" name="email" required value="{{ old('email') }}">
            </label>
            <label>Password
                <input type="password" name="password" required minlength="8">
            </label>
            <label>Confirm password
                <input type="password" name="password_confirmation" required minlength="8">
            </label>
            <button class="btn" style="margin-top:12px;" type="submit">Register</button>
        </form>
    </div>
@endsection
