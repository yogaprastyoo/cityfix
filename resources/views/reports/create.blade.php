@extends('layouts.app')
@section('title', 'Buat Laporan | CityFix')
@section('content-header')<h1>Buat Laporan Kerusakan</h1>@endsection
@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Informasi Kerusakan</h3>
                </div>
                <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="photo">Foto Kerusakan</label>
                            <input type="file" name="photo" id="photo" accept="image/*"
                                class="form-control @error('photo') is-invalid @enderror" required>
                            <small class="form-text text-muted">Format gambar (JPG/PNG), maksimal 5 MB. Di smartphone dapat langsung memakai kamera.</small>
                            @error('photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <img id="photo-preview" class="img-fluid img-thumbnail d-none mt-2" style="max-height: 240px" alt="Preview foto">
                        </div>
                        <div class="form-group">
                            <label for="area_id">Area</label>
                            <select name="area_id" id="area_id" class="form-control @error('area_id') is-invalid @enderror" required>
                                <option value="">Pilih Area</option>
                                @foreach ($areas as $area)
                                    <option value="{{ $area->id }}" {{ old('area_id') == $area->id ? 'selected' : '' }}>
                                        {{ $area->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('area_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="location_detail">Lokasi Detail</label>
                            <input type="text" name="location_detail" id="location_detail" value="{{ old('location_detail') }}"
                                class="form-control @error('location_detail') is-invalid @enderror" placeholder="Contoh: Toilet lantai 2" required>
                            @error('location_detail')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="category_id">Kategori</label>
                            <select name="category_id" id="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                                <option value="">Pilih Kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="urgency">Tingkat Urgensi</label>
                            <select name="urgency" id="urgency" class="form-control @error('urgency') is-invalid @enderror" required>
                                @foreach (\App\Models\Report::URGENCY_LABELS as $value => $label)
                                    <option value="{{ $value }}" {{ old('urgency', 'medium') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('urgency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="description">Deskripsi Masalah</label>
                            <textarea name="description" id="description" rows="4" required
                                class="form-control @error('description') is-invalid @enderror" placeholder="Jelaskan kondisi kerusakan...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-block-xs">
                            <i class="fas fa-paper-plane"></i> Kirim Laporan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('photo').addEventListener('change', function (event) {
            const preview = document.getElementById('photo-preview');
            const file = event.target.files[0];

            if (! file) {
                preview.classList.add('d-none');
                return;
            }

            preview.src = URL.createObjectURL(file);
            preview.classList.remove('d-none');
        });
    </script>
@endpush
