@extends('layouts.app')
@section('title', ($user->exists ? 'Edit' : 'Tambah').' User | CityFix')
@section('content-header')<h1>{{ $user->exists ? 'Edit' : 'Tambah' }} User</h1>@endsection
@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <form method="POST" action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}">
                    @csrf
                    @if ($user->exists)
                        @method('PUT')
                    @endif
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Nama</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                                class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                                class="form-control @error('email') is-invalid @enderror" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select name="role" id="role" class="form-control @error('role') is-invalid @enderror" required>
                                @foreach (\App\Models\User::ROLES as $value => $label)
                                    <option value="{{ $value }}" {{ old('role', $user->role) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="password">Password {{ $user->exists ? '(kosongkan jika tidak diubah)' : '' }}</label>
                            <input type="password" name="password" id="password"
                                class="form-control @error('password') is-invalid @enderror" {{ $user->exists ? '' : 'required' }}>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-0">
                            <label for="password_confirmation">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                        <a href="{{ route('users.index') }}" class="btn btn-default">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
