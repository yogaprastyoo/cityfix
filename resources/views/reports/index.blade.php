@extends('layouts.app')
@php($pageTitle = ['reporter' => 'Laporan Saya', 'technician' => 'My Task'][auth()->user()->role] ?? 'Daftar Laporan')
@section('title', $pageTitle.' | CityFix')
@section('content-header')
    <div class="row">
        <div class="col">
            <h1>{{ $pageTitle }}</h1>
        </div>
        @can('create', \App\Models\Report::class)
            <div class="col-auto text-right">
                <a href="{{ route('reports.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Laporan Baru</a>
            </div>
        @endcan
    </div>
@endsection
@section('content')
    <div class="card card-outline card-secondary">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.index') }}" class="row">
                <div class="col-md-3 col-12 mb-2">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nomor laporan (CF-...)">
                </div>
                <div class="col-md-3 col-6 mb-2">
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        @foreach (\App\Models\Report::STATUS_LABELS as $value => $label)
                            <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-6 mb-2">
                    <select name="area_id" class="form-control">
                        <option value="">Semua Area</option>
                        @foreach ($areas as $area)
                            <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-6 mb-2">
                    <select name="urgency" class="form-control">
                        <option value="">Semua Urgensi</option>
                        @foreach (\App\Models\Report::URGENCY_LABELS as $value => $label)
                            <option value="{{ $value }}" {{ request('urgency') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-6 mb-2">
                    <button type="submit" class="btn btn-secondary"><i class="fas fa-search"></i> Filter</button>
                    <a href="{{ route('reports.index') }}" class="btn btn-default">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Semua Laporan</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table-bordered table-hover table text-nowrap">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tanggal</th>
                        <th>Area</th>
                        <th>Masalah</th>
                        <th>Urgensi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reports as $report)
                        <tr>
                            <td>{{ $report->report_number }}</td>
                            <td>{{ $report->created_at->format('d M Y H:i') }}</td>
                            <td>{{ $report->area->name }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($report->description, 50) }}</td>
                            <td><span class="badge badge-{{ $report->urgency_badge }}">{{ $report->urgency_label }}</span></td>
                            <td><span class="badge badge-{{ $report->status_badge }}">{{ $report->status_label }}</span></td>
                            <td><a href="{{ route('reports.show', $report) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i> Detail</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-muted text-center">Tidak ada laporan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($reports->hasPages())
            <div class="card-footer clearfix">
                {{ $reports->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
@endsection
