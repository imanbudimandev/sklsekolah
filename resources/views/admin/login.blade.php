@extends('layouts.app')

@section('title', 'Login Admin')

@section('content')
<div class="login-container">
    <div class="login-card card glass fade-in">
        <div class="login-header">
            <div class="admin-icon">
                @if(!empty($settings['school_logo']) && file_exists(public_path($settings['school_logo'])))
                    <img src="{{ asset($settings['school_logo']) }}" alt="Logo Sekolah" style="max-height: 64px; max-width: 64px; border-radius: 8px; object-fit: contain;">
                @else
                    <i class="fa-solid fa-user-shield"></i>
                @endif
            </div>
            <h2>Admin Portal</h2>
            <p>Silakan login untuk mengelola data kelulusan siswa.</p>
        </div>

        <form action="{{ route('admin.login') }}" method="POST" class="login-form">
            @csrf

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}
                </div>
            @endif

            <div class="form-group">
                <label for="email"><i class="fa-solid fa-envelope"></i> Alamat Email</label>
                <input type="email" id="email" name="email" placeholder="admin@nurulihsan.sch.id" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="password"><i class="fa-solid fa-lock"></i> Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <div class="form-group-checkbox">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Ingat Saya</label>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg">
                <i class="fa-solid fa-right-to-bracket"></i> Masuk
            </button>
        </form>
        
        <div class="login-footer">
            <a href="{{ route('public.index') }}"><i class="fa-solid fa-arrow-left"></i> Kembali ke Portal Publik</a>
        </div>
    </div>
</div>
@endsection
