@extends('layouts.app')

@section('content')
    <h1 style="margin-top:12px;">Superadmin Users</h1>

    <div class="card" style="margin-top:12px;">
        <form action="{{ url('/admin/users') }}" method="post" class="grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
            @csrf
            <label>Name
                <input name="name" required>
            </label>
            <label>Email
                <input name="email" type="email" required>
            </label>
            <label>Password
                <input name="password" type="password" minlength="8" required>
            </label>
            <label>Role
                <select name="role">
                    <option value="USER">USER</option>
                    <option value="SUPERADMIN">SUPERADMIN</option>
                </select>
            </label>
            <div style="align-self:end;">
                <button class="btn" type="submit">Create User</button>
            </div>
        </form>
    </div>

    <div class="card" style="margin-top:14px;">
        <table>
            <thead>
                <tr><th>Name</th><th>Email</th><th>Role</th><th>Codes</th></tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->role }}</td>
                        <td>{{ $user->codes_count }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
