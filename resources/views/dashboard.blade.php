@extends('layouts.app')
@section('title', 'Dashboard | CityFix')
@section('content-header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Dashboard</h1>
        </div>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalReports }}</h3>
                    <p>Total Laporan</p>
                </div>
                <div class="icon"><i class="fas fa-clipboard-list"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3>{{ $reported }}</h3>
                    <p>Dilaporkan (Belum Diverifikasi)</p>
                </div>
                <div class="icon"><i class="fas fa-inbox"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $inProgress }}</h3>
                    <p>Dalam Penanganan</p>
                </div>
                <div class="icon"><i class="fas fa-tools"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $completed }}</h3>
                    <p>Selesai</p>
                </div>
                <div class="icon"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg col-md-4 col-6">
            <div class="info-box">
                <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Emergency Open</span>
                    <span class="info-box-number">{{ $emergencyOpen }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg col-md-4 col-6">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-box-open"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Waiting Material</span>
                    <span class="info-box-number">{{ $waitingMaterial }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg col-md-4 col-6">
            <div class="info-box">
                <span class="info-box-icon bg-secondary"><i class="fas fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Rata-rata Penyelesaian</span>
                    <span class="info-box-number">{{ $averageResolutionHours }} jam</span>
                </div>
            </div>
        </div>
        <div class="col-lg col-md-6 col-6">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-percentage"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Completion Rate</span>
                    <span class="info-box-number">{{ $completionRate }}%</span>
                </div>
            </div>
        </div>
        <div class="col-lg col-md-6 col-12">
            <div class="info-box">
                <span class="info-box-icon bg-dark"><i class="fas fa-hourglass-end"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Overdue (melewati SLA)</span>
                    <span class="info-box-number">{{ $overdue }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Laporan Terbaru</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table-hover table text-nowrap">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Area</th>
                        <th>Masalah</th>
                        <th>Urgensi</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($latestReports as $report)
                        <tr>
                            <td><a href="{{ route('reports.show', $report) }}">{{ $report->report_number }}</a></td>
                            <td>{{ $report->area->name }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($report->description, 50) }}</td>
                            <td><span class="badge badge-{{ $report->urgency_badge }}">{{ $report->urgency_label }}</span></td>
                            <td><span class="badge badge-{{ $report->status_badge }}">{{ $report->status_label }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-muted text-center">Belum ada laporan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
