@extends('layouts.app')
@section('title', 'Master Area | CityFix')
@section('content-header')
    <div class="row">
        <div class="col">
            <h1>Master Area</h1>
        </div>
        <div class="col-auto text-right">
            <a href="{{ route('areas.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Area</a>
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
                        <th>Kode</th>
                        <th>Status</th>
                        <th>Jumlah Laporan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($areas as $area)
                        <tr>
                            <td>{{ $area->name }}</td>
                            <td>{{ $area->code ?? '-' }}</td>
                            <td><span class="badge badge-{{ $area->is_active ? 'success' : 'secondary' }}">{{ $area->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                            <td>{{ $area->reports_count }}</td>
                            <td>
                                <a href="{{ route('areas.edit', $area) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                <form method="POST" action="{{ route('areas.destroy', $area) }}" class="d-inline" onsubmit="return confirm('Hapus area ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-muted text-center">Belum ada area.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($areas->hasPages())
            <div class="card-footer clearfix">{{ $areas->links('pagination::bootstrap-4') }}</div>
        @endif
    </div>
@endsection
