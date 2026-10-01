<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Category;
use App\Models\Report;
use App\Models\ReportHistory;
use App\Models\User;
use App\Policies\ReportPolicy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => 'nullable|string|max:50',
            'status' => 'nullable|in:'.implode(',', array_keys(Report::STATUS_LABELS)),
            'area_id' => 'nullable|integer',
            'urgency' => 'nullable|in:'.implode(',', array_keys(Report::URGENCY_LABELS)),
        ]);

        $reports = Report::with(['area', 'category', 'reporter', 'technician'])
            ->visibleTo($request->user())
            ->when($filters['search'] ?? null, fn ($query, $search) => $query->where('report_number', 'like', "%{$search}%"))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['area_id'] ?? null, fn ($query, $areaId) => $query->where('area_id', $areaId))
            ->when($filters['urgency'] ?? null, fn ($query, $urgency) => $query->where('urgency', $urgency))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $areas = Area::orderBy('name')->get();

        return view('reports.index', compact('reports', 'areas'));
    }

    public function create(): View
    {
        Gate::authorize('create', Report::class);

        $areas = Area::where('is_active', true)->orderBy('name')->get();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('reports.create', compact('areas', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Report::class);

        $validated = $request->validate([
            'area_id' => 'required|exists:areas,id',
            'category_id' => 'required|exists:categories,id',
            'location_detail' => 'required|string|max:255',
            'description' => 'required|string',
            'urgency' => 'required|in:low,medium,high,emergency',
            'photo' => 'required|image|max:5120',
        ]);

        $photoPath = $request->file('photo')->store('reports', 'public');

        $report = DB::transaction(function () use ($validated, $photoPath) {
            $report = Report::create([
                'report_number' => Report::generateReportNumber(),
                'user_id' => auth()->id(),
                'area_id' => $validated['area_id'],
                'category_id' => $validated['category_id'],
                'location_detail' => $validated['location_detail'],
                'description' => $validated['description'],
                'urgency' => $validated['urgency'],
                'status' => 'reported',
                'photo' => $photoPath,
                'reported_at' => now(),
            ]);

            ReportHistory::create([
                'report_id' => $report->id,
                'user_id' => auth()->id(),
                'status' => 'reported',
                'note' => 'Laporan dibuat',
            ]);

            return $report;
        });

        return redirect()->route('reports.show', $report)
            ->with('success', 'Laporan berhasil dibuat.');
    }

    public function show(Request $request, Report $report): View
    {
        Gate::authorize('view', $report);

        $report->load(['area', 'category', 'reporter', 'technician', 'histories.user']);

        $technicians = $request->user()->can('assign', $report)
            ? User::where('role', 'technician')->orderBy('name')->get()
            : collect();

        $allowedStatuses = ReportPolicy::allowedStatuses($request->user(), $report);

        return view('reports.show', compact('report', 'technicians', 'allowedStatuses'));
    }

    public function assign(Request $request, Report $report): RedirectResponse
    {
        Gate::authorize('assign', $report);

        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $technician = User::where('id', $validated['assigned_to'])
            ->where('role', 'technician')
            ->first();

        if (! $technician) {
            return back()->withErrors(['assigned_to' => 'PIC harus user dengan role technician.']);
        }

        $report->update([
            'assigned_to' => $technician->id,
            'status' => 'verified',
        ]);

        ReportHistory::create([
            'report_id' => $report->id,
            'user_id' => auth()->id(),
            'status' => 'verified',
            'note' => "Laporan diverifikasi dan teknisi ditugaskan: {$technician->name}.",
        ]);

        return back()->with('success', 'Teknisi berhasil ditugaskan.');
    }

    public function updateStatus(Request $request, Report $report): RedirectResponse
    {
        Gate::authorize('updateStatus', $report);

        $validated = $request->validate([
            'status' => 'required|in:verified,in_progress,waiting_material,completed,rejected',
            'note' => 'nullable|string',
            'completion_photo' => 'nullable|image|max:5120',
        ]);

        if (! $report->canTransitionTo($validated['status'])) {
            return back()->withErrors(['status' => 'Perubahan status tidak diperbolehkan.']);
        }

        if (! in_array($validated['status'], ReportPolicy::allowedStatuses($request->user(), $report))) {
            abort(403);
        }

        if ($validated['status'] === 'completed') {
            $request->validate([
                'completion_photo' => 'required|image|max:5120',
            ]);
        }

        $completionPhoto = null;
        if ($request->hasFile('completion_photo')) {
            $completionPhoto = $request->file('completion_photo')
                ->store('completion', 'public');
        }

        $data = ['status' => $validated['status']];

        if ($validated['status'] === 'in_progress' && ! $report->started_at) {
            $data['started_at'] = now();
        }

        if ($validated['status'] === 'completed') {
            $data['completed_at'] = now();
        }

        DB::transaction(function () use ($report, $data, $validated, $completionPhoto) {
            $report->update($data);

            ReportHistory::create([
                'report_id' => $report->id,
                'user_id' => auth()->id(),
                'status' => $validated['status'],
                'note' => $validated['note'] ?? null,
                'photo' => $completionPhoto,
            ]);
        });

        return back()->with('success', 'Status berhasil diperbarui.');
    }
}
