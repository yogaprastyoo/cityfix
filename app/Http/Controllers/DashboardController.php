<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Target SLA per urgency dalam jam (low = pemeliharaan rutin, tanpa SLA).
     *
     * @var array<string, int>
     */
    public const SLA_HOURS = [
        'emergency' => 2,
        'high' => 24,
        'medium' => 72,
    ];

    public function index(Request $request): View
    {
        $totalReports = Report::count();
        $inProgress = Report::where('status', 'in_progress')->count();
        $completed = Report::where('status', 'completed')->count();
        $reported = Report::where('status', 'reported')->count();

        $emergencyOpen = Report::open()->where('urgency', 'emergency')->count();
        $waitingMaterial = Report::where('status', 'waiting_material')->count();

        $relevantReports = Report::where('status', '!=', 'rejected')->count();
        $completionRate = $relevantReports > 0 ? round($completed / $relevantReports * 100, 1) : 0;

        $averageResolutionHours = round(
            Report::where('status', 'completed')
                ->whereNotNull('reported_at')
                ->whereNotNull('completed_at')
                ->get(['reported_at', 'completed_at'])
                ->avg(fn (Report $report) => $report->reported_at->diffInMinutes($report->completed_at) / 60) ?? 0,
            1
        );

        $overdue = 0;
        foreach (self::SLA_HOURS as $urgency => $hours) {
            $overdue += Report::open()
                ->where('urgency', $urgency)
                ->where('reported_at', '<', now()->subHours($hours))
                ->count();
        }

        $latestReports = Report::with(['area', 'category'])
            ->visibleTo($request->user())
            ->latest()->limit(10)->get();

        return view('dashboard', compact(
            'totalReports', 'inProgress', 'completed', 'reported', 'latestReports',
            'emergencyOpen', 'waitingMaterial', 'completionRate', 'averageResolutionHours', 'overdue'
        ));
    }
}
