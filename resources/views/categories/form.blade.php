@extends('layouts.app')
@section('title', ($category->exists ? 'Edit' : 'Tambah').' Kategori | CityFix')
@section('content-header')<h1>{{ $category->exists ? 'Edit' : 'Tambah' }} Kategori</h1>@endsection
@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <form method="POST" action="{{ $category->exists ? route('categories.update', $category) : route('categories.store') }}">
                    @csrf
                    @if ($category->exists)
                        @method('PUT')
                    @endif
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Nama Kategori</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}"
                                class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="icon">Icon (Font Awesome)</label>
                            <input type="text" name="icon" id="icon" value="{{ old('icon', $category->icon) }}"
                                class="form-control @error('icon') is-invalid @enderror" placeholder="Contoh: fas fa-bolt (opsional)">
                            @error('icon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="is_active" value="1" id="is_active" class="custom-control-input"
                                {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">Aktif</label>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                        <a href="{{ route('categories.index') }}" class="btn btn-default">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
