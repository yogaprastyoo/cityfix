@extends('layouts.app')
@section('title', ($area->exists ? 'Edit' : 'Tambah').' Area | CityFix')
@section('content-header')<h1>{{ $area->exists ? 'Edit' : 'Tambah' }} Area</h1>@endsection
@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <form method="POST" action="{{ $area->exists ? route('areas.update', $area) : route('areas.store') }}">
                    @csrf
                    @if ($area->exists)
                        @method('PUT')
                    @endif
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Nama Area</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $area->name) }}"
                                class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="code">Kode</label>
                            <input type="text" name="code" id="code" value="{{ old('code', $area->code) }}"
                                class="form-control @error('code') is-invalid @enderror" placeholder="Opsional">
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="is_active" value="1" id="is_active" class="custom-control-input"
                                {{ old('is_active', $area->is_active) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">Aktif</label>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                        <a href="{{ route('areas.index') }}" class="btn btn-default">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
