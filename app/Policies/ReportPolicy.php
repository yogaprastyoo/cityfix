<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;

class ReportPolicy
{
    /**
     * Reporter hanya laporan miliknya, technician hanya task yang di-assign,
     * verifier dan admin dapat melihat seluruh laporan.
     */
    public function view(User $user, Report $report): bool
    {
        if (in_array($user->role, ['admin', 'verifier'])) {
            return true;
        }

        if ($user->role === 'reporter') {
            return $report->user_id === $user->id;
        }

        if ($user->role === 'technician') {
            return $report->assigned_to === $user->id;
        }

        return false;
    }

    /**
     * Technician tidak membuat laporan (Role Permission Matrix).
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'verifier', 'reporter']);
    }

    /**
     * Verifikasi + assign teknisi hanya untuk verifier dan admin.
     */
    public function assign(User $user, Report $report): bool
    {
        return in_array($user->role, ['admin', 'verifier'])
            && in_array($report->status, ['reported', 'verified']);
    }

    /**
     * Status yang boleh dipilih user untuk laporan ini.
     *
     * @return list<string>
     */
    public static function allowedStatuses(User $user, Report $report): array
    {
        $statuses = Report::TRANSITIONS[$report->status] ?? [];

        if (in_array($user->role, ['admin', 'verifier'])) {
            return $statuses;
        }

        if ($user->role === 'technician' && $report->assigned_to === $user->id) {
            return array_values(array_intersect($statuses, ['in_progress', 'waiting_material', 'completed']));
        }

        return [];
    }

    public function updateStatus(User $user, Report $report): bool
    {
        return in_array($user->role, ['admin', 'verifier'])
            || ($user->role === 'technician' && $report->assigned_to === $user->id);
    }
}
