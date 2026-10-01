@extends('layouts.app')
@section('title', 'Area Monitoring | CityFix')
@section('content-header')<h1>Area Monitoring</h1>@endsection
@section('content')
    <div class="row">
        @foreach (\App\Models\Report::STATUS_LABELS as $status => $label)
            <div class="col-lg-2 col-md-4 col-6">
                <div class="info-box">
                    <span class="info-box-icon bg-{{ \App\Models\Report::STATUS_BADGES[$status] }}"><i class="fas fa-flag"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ $label }}</span>
                        <span class="info-box-number">{{ $statusCounts[$status] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Laporan per Area</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table-hover table text-nowrap">
                <thead>
                    <tr>
                        <th>Area</th>
                        <th>Total</th>
                        <th>Open</th>
                        <th>Emergency Open</th>
                        <th>Menunggu Material</th>
                        <th>Selesai</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($areas as $area)
                        <tr>
                            <td>{{ $area->name }}</td>
                            <td>{{ $area->reports_count }}</td>
                            <td>{{ $area->open_count }}</td>
                            <td>
                                @if ($area->emergency_open_count > 0)
                                    <span class="badge badge-danger">{{ $area->emergency_open_count }}</span>
                                @else
                                    0
                                @endif
                            </td>
                            <td>{{ $area->waiting_material_count }}</td>
                            <td>{{ $area->completed_count }}</td>
                            <td><a href="{{ route('reports.index', ['area_id' => $area->id]) }}" class="btn btn-sm btn-info">Lihat Laporan</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
