@extends('layouts.app')
@section('title', 'Ganti Password | CityFix')
@section('content-header')<h1>Ganti Password</h1>@endsection
@section('content')
    <div class="row">
        <div class="col-md-6">
            @if (auth()->user()->must_change_password)
                <div class="alert alert-warning"><i class="icon fas fa-exclamation-triangle"></i> Anda masih memakai password sementara. Ganti password sebelum melanjutkan.</div>
            @endif
            <div class="card card-primary">
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-group">
                            <label for="current_password">Password Saat Ini</label>
                            <input type="password" name="current_password" id="current_password"
                                class="form-control @error('current_password') is-invalid @enderror" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="password">Password Baru</label>
                            <input type="password" name="password" id="password"
                                class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-0">
                            <label for="password_confirmation">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-key"></i> Simpan Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
