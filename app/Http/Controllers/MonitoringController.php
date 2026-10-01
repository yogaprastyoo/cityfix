<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Report;
use Illuminate\View\View;

class MonitoringController extends Controller
{
    /**
     * Area Monitoring: jumlah laporan per area per status.
     */
    public function index(): View
    {
        $areas = Area::orderBy('name')
            ->withCount([
                'reports',
                'reports as open_count' => fn ($query) => $query->whereNotIn('status', ['completed', 'rejected']),
                'reports as emergency_open_count' => fn ($query) => $query->whereNotIn('status', ['completed', 'rejected'])->where('urgency', 'emergency'),
                'reports as waiting_material_count' => fn ($query) => $query->where('status', 'waiting_material'),
                'reports as completed_count' => fn ($query) => $query->where('status', 'completed'),
            ])
            ->get();

        $statusCounts = Report::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('monitoring.index', compact('areas', 'statusCounts'));
    }
}
