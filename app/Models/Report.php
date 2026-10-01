<?php

namespace App\Models;

use Database\Factories\ReportFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    /** @use HasFactory<ReportFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'report_number', 'user_id', 'area_id', 'category_id',
        'location_detail', 'description', 'urgency', 'status',
        'photo', 'assigned_to', 'reported_at', 'started_at', 'completed_at',
    ];

    /**
     * @var array<string, string>
     */
    public const STATUS_LABELS = [
        'reported' => 'Dilaporkan',
        'verified' => 'Diverifikasi',
        'in_progress' => 'Dalam Penanganan',
        'waiting_material' => 'Menunggu Material',
        'completed' => 'Selesai',
        'rejected' => 'Ditolak',
    ];

    /**
     * Badge class per status (Tahap 1 - Standar Status).
     *
     * @var array<string, string>
     */
    public const STATUS_BADGES = [
        'reported' => 'secondary',
        'verified' => 'info',
        'in_progress' => 'primary',
        'waiting_material' => 'warning',
        'completed' => 'success',
        'rejected' => 'danger',
    ];

    /**
     * @var array<string, string>
     */
    public const URGENCY_LABELS = [
        'low' => 'Rendah',
        'medium' => 'Sedang',
        'high' => 'Tinggi',
        'emergency' => 'Darurat',
    ];

    /**
     * @var array<string, string>
     */
    public const URGENCY_BADGES = [
        'low' => 'success',
        'medium' => 'warning',
        'high' => 'danger',
        'emergency' => 'dark',
    ];

    /**
     * Allowed status transitions (Tahap 3 - Workflow Transition).
     *
     * @var array<string, list<string>>
     */
    public const TRANSITIONS = [
        'reported' => ['verified', 'rejected'],
        'verified' => ['in_progress', 'rejected'],
        'in_progress' => ['waiting_material', 'completed'],
        'waiting_material' => ['in_progress', 'completed'],
        'completed' => [],
        'rejected' => [],
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'reported_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * @return BelongsTo<Area, $this>
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return HasMany<ReportHistory, $this>
     */
    public function histories(): HasMany
    {
        return $this->hasMany(ReportHistory::class);
    }

    /**
     * Batasi laporan sesuai hak akses role.
     *
     * @param  Builder<Report>  $query
     */
    #[Scope]
    protected function visibleTo(Builder $query, User $user): void
    {
        if ($user->role === 'reporter') {
            $query->where('user_id', $user->id);
        } elseif ($user->role === 'technician') {
            $query->where('assigned_to', $user->id);
        }
    }

    /**
     * Report yang belum completed/rejected.
     *
     * @param  Builder<Report>  $query
     */
    #[Scope]
    protected function open(Builder $query): void
    {
        $query->whereNotIn('status', ['completed', 'rejected']);
    }

    public function canTransitionTo(string $newStatus): bool
    {
        return in_array($newStatus, self::TRANSITIONS[$this->status] ?? []);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function getStatusBadgeAttribute(): string
    {
        return self::STATUS_BADGES[$this->status] ?? 'secondary';
    }

    public function getUrgencyLabelAttribute(): string
    {
        return self::URGENCY_LABELS[$this->urgency] ?? $this->urgency;
    }

    public function getUrgencyBadgeAttribute(): string
    {
        return self::URGENCY_BADGES[$this->urgency] ?? 'secondary';
    }

    /**
     * Generate a sequential yearly report number, e.g. CF-2026-000001.
     */
    public static function generateReportNumber(): string
    {
        $year = now()->year;
        $lastReport = self::whereYear('created_at', $year)->latest('id')->first();
        $sequence = $lastReport ? ((int) substr($lastReport->report_number, -6)) + 1 : 1;

        return sprintf('CF-%s-%06d', $year, $sequence);
    }
}
