@extends('layouts.app')
@section('title', $report->report_number.' | CityFix')
@section('content-header')
    <div class="row">
        <div class="col">
            <h1>Detail Laporan <small class="text-muted">{{ $report->report_number }}</small></h1>
        </div>
        <div class="col-auto text-right">
            <a href="{{ route('reports.index') }}" class="btn btn-default"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-7">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Informasi Laporan</h3>
                    <div class="card-tools">
                        <span class="badge badge-{{ $report->status_badge }} p-2">{{ $report->status_label }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Nomor</dt>
                        <dd class="col-sm-8">{{ $report->report_number }}</dd>
                        <dt class="col-sm-4">Pelapor</dt>
                        <dd class="col-sm-8">{{ $report->reporter->name }}</dd>
                        <dt class="col-sm-4">Tanggal Lapor</dt>
                        <dd class="col-sm-8">{{ $report->reported_at?->format('d M Y H:i') }}</dd>
                        <dt class="col-sm-4">Area</dt>
                        <dd class="col-sm-8">{{ $report->area->name }}</dd>
                        <dt class="col-sm-4">Lokasi Detail</dt>
                        <dd class="col-sm-8">{{ $report->location_detail }}</dd>
                        <dt class="col-sm-4">Kategori</dt>
                        <dd class="col-sm-8">{{ $report->category->name }}</dd>
                        <dt class="col-sm-4">Urgensi</dt>
                        <dd class="col-sm-8"><span class="badge badge-{{ $report->urgency_badge }}">{{ $report->urgency_label }}</span></dd>
                        <dt class="col-sm-4">PIC / Teknisi</dt>
                        <dd class="col-sm-8">{{ $report->technician?->name ?? '-' }}</dd>
                        <dt class="col-sm-4">Mulai Dikerjakan</dt>
                        <dd class="col-sm-8">{{ $report->started_at?->format('d M Y H:i') ?? '-' }}</dd>
                        <dt class="col-sm-4">Selesai</dt>
                        <dd class="col-sm-8">{{ $report->completed_at?->format('d M Y H:i') ?? '-' }}</dd>
                        <dt class="col-sm-4">Deskripsi</dt>
                        <dd class="col-sm-8">{!! nl2br(e($report->description)) !!}</dd>
                    </dl>
                    @if ($report->photo)
                        <hr>
                        <p class="font-weight-bold mb-1">Foto Kerusakan</p>
                        <a href="{{ asset('storage/'.$report->photo) }}" target="_blank">
                            <img src="{{ asset('storage/'.$report->photo) }}" class="img-fluid img-thumbnail" style="max-height: 360px" alt="Foto kerusakan">
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            @can('assign', $report)
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Verifikasi & Assign Teknisi</h3>
                    </div>
                    <form method="POST" action="{{ route('reports.assign', $report) }}">
                        @csrf
                        <div class="card-body">
                            <div class="form-group mb-0">
                                <label for="assigned_to">Teknisi</label>
                                <select name="assigned_to" id="assigned_to" class="form-control @error('assigned_to') is-invalid @enderror" required>
                                    <option value="">Pilih Teknisi</option>
                                    @foreach ($technicians as $technician)
                                        <option value="{{ $technician->id }}" {{ old('assigned_to', $report->assigned_to) == $technician->id ? 'selected' : '' }}>
                                            {{ $technician->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-info btn-block-xs"><i class="fas fa-user-check"></i> Verifikasi & Assign</button>
                        </div>
                    </form>
                </div>
            @endcan

            @if (count($allowedStatuses) > 0)
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">Update Status</h3>
                    </div>
                    <form method="POST" action="{{ route('reports.status', $report) }}" enctype="multipart/form-data" id="status-form">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="status">Status Baru</label>
                                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                    @foreach ($allowedStatuses as $status)
                                        <option value="{{ $status }}" {{ old('status') === $status ? 'selected' : '' }}>{{ \App\Models\Report::STATUS_LABELS[$status] }}</option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="note">Catatan</label>
                                <textarea name="note" id="note" rows="2" class="form-control" placeholder="Opsional">{{ old('note') }}</textarea>
                            </div>
                            <div class="form-group mb-0" id="completion-photo-group">
                                <label for="completion_photo">Foto Bukti Perbaikan <span class="text-danger">(wajib untuk Selesai)</span></label>
                                <input type="file" name="completion_photo" id="completion_photo" accept="image/*"
                                    class="form-control @error('completion_photo') is-invalid @enderror">
                                @error('completion_photo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-warning btn-block-xs"><i class="fas fa-save"></i> Simpan Status</button>
                        </div>
                    </form>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Progress Tracker</h3>
                </div>
                <div class="card-body">
                    <div class="timeline mb-0">
                        @foreach ($report->histories->sortBy('created_at') as $history)
                            <div>
                                <i class="fas fa-circle bg-{{ \App\Models\Report::STATUS_BADGES[$history->status] ?? 'secondary' }}"></i>
                                <div class="timeline-item">
                                    <span class="time"><i class="fas fa-clock"></i> {{ $history->created_at->format('d M Y H:i') }}</span>
                                    <h3 class="timeline-header"><strong>{{ $history->status_label }}</strong>
                                        @if ($history->user)
                                            <small class="text-muted">oleh {{ $history->user->name }}</small>
                                        @endif
                                    </h3>
                                    @if ($history->note || $history->photo)
                                        <div class="timeline-body">
                                            @if ($history->note)
                                                <p class="mb-1">{{ $history->note }}</p>
                                            @endif
                                            @if ($history->photo)
                                                <a href="{{ asset('storage/'.$history->photo) }}" target="_blank">
                                                    <img src="{{ asset('storage/'.$history->photo) }}" class="img-fluid img-thumbnail" style="max-height: 200px" alt="Foto bukti">
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        <div><i class="fas fa-flag bg-gray"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const form = document.getElementById('status-form');
            if (! form) {
                return;
            }

            const status = document.getElementById('status');
            const photoGroup = document.getElementById('completion-photo-group');
            const togglePhoto = () => photoGroup.classList.toggle('d-none', status.value !== 'completed');

            status.addEventListener('change', togglePhoto);
            togglePhoto();

            form.addEventListener('submit', function (event) {
                if (status.value === 'completed' && ! confirm('Tandai laporan ini sebagai Selesai? Pastikan foto bukti sudah benar.')) {
                    event.preventDefault();
                }
            });
        })();
    </script>
@endpush
