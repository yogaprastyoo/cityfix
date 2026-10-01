@extends('layouts.app')
@section('title', 'Master Kategori | CityFix')
@section('content-header')
    <div class="row">
        <div class="col">
            <h1>Master Kategori</h1>
        </div>
        <div class="col-auto text-right">
            <a href="{{ route('categories.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Kategori</a>
        </div>
    </div>
@endsection
@section('content')
    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table-hover table text-nowrap">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Icon (Font Awesome)</th>
                        <th>Status</th>
                        <th>Jumlah Laporan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td>{{ $category->name }}</td>
                            <td>@if ($category->icon)<i class="{{ $category->icon }}"></i> {{ $category->icon }}@else - @endif</td>
                            <td><span class="badge badge-{{ $category->is_active ? 'success' : 'secondary' }}">{{ $category->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                            <td>{{ $category->reports_count }}</td>
                            <td>
                                <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                <form method="POST" action="{{ route('categories.destroy', $category) }}" class="d-inline" onsubmit="return confirm('Hapus kategori ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-muted text-center">Belum ada kategori.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($categories->hasPages())
            <div class="card-footer clearfix">{{ $categories->links('pagination::bootstrap-4') }}</div>
        @endif
    </div>
@endsection
