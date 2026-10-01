<?php

use App\Models\Report;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
});

test('verifier can verify report and assign technician', function () {
    $verifier = User::factory()->verifier()->create();
    $technician = User::factory()->technician()->create();
    $report = Report::factory()->create();

    $this->actingAs($verifier)
        ->post(route('reports.assign', $report), ['assigned_to' => $technician->id])
        ->assertSessionHasNoErrors();

    expect($report->fresh())
        ->assigned_to->toBe($technician->id)
        ->status->toBe('verified');
    $this->assertDatabaseHas('report_histories', [
        'report_id' => $report->id,
        'user_id' => $verifier->id,
        'status' => 'verified',
    ]);
});

test('report can only be assigned to a technician', function () {
    $verifier = User::factory()->verifier()->create();
    $reporter = User::factory()->reporter()->create();
    $report = Report::factory()->create();

    $this->actingAs($verifier)
        ->post(route('reports.assign', $report), ['assigned_to' => $reporter->id])
        ->assertSessionHasErrors('assigned_to');

    expect($report->fresh())
        ->assigned_to->toBeNull()
        ->status->toBe('reported');
});

test('verifier can reject report', function () {
    $verifier = User::factory()->verifier()->create();
    $report = Report::factory()->create();

    $this->actingAs($verifier)
        ->post(route('reports.status', $report), ['status' => 'rejected', 'note' => 'Laporan duplikat'])
        ->assertSessionHasNoErrors();

    expect($report->fresh()->status)->toBe('rejected');
    $this->assertDatabaseHas('report_histories', [
        'report_id' => $report->id,
        'status' => 'rejected',
        'note' => 'Laporan duplikat',
    ]);
});

test('technician can start work on assigned task', function () {
    $technician = User::factory()->technician()->create();
    $report = Report::factory()->create(['assigned_to' => $technician->id, 'status' => 'verified']);

    $this->actingAs($technician)
        ->post(route('reports.status', $report), ['status' => 'in_progress'])
        ->assertSessionHasNoErrors();

    $report->refresh();
    expect($report->status)->toBe('in_progress');
    expect($report->started_at)->not->toBeNull();
});

test('technician can mark task as waiting material and resume', function () {
    $technician = User::factory()->technician()->create();
    $report = Report::factory()->create(['assigned_to' => $technician->id, 'status' => 'in_progress']);

    $this->actingAs($technician)
        ->post(route('reports.status', $report), ['status' => 'waiting_material', 'note' => 'Menunggu kran baru'])
        ->assertSessionHasNoErrors();
    expect($report->fresh()->status)->toBe('waiting_material');

    $this->actingAs($technician)
        ->post(route('reports.status', $report), ['status' => 'in_progress'])
        ->assertSessionHasNoErrors();
    expect($report->fresh()->status)->toBe('in_progress');
});

test('technician can complete task with completion photo', function () {
    $technician = User::factory()->technician()->create();
    $report = Report::factory()->create(['assigned_to' => $technician->id, 'status' => 'in_progress']);

    $this->actingAs($technician)
        ->post(route('reports.status', $report), [
            'status' => 'completed',
            'completion_photo' => UploadedFile::fake()->image('selesai.jpg'),
        ])
        ->assertSessionHasNoErrors();

    $report->refresh();
    expect($report->status)->toBe('completed');
    expect($report->completed_at)->not->toBeNull();

    $history = $report->histories()->where('status', 'completed')->sole();
    expect($history->photo)->not->toBeNull();
    Storage::disk('public')->assertExists($history->photo);
});

test('completing a task without completion photo is rejected', function () {
    $technician = User::factory()->technician()->create();
    $report = Report::factory()->create(['assigned_to' => $technician->id, 'status' => 'in_progress']);

    $this->actingAs($technician)
        ->post(route('reports.status', $report), ['status' => 'completed'])
        ->assertSessionHasErrors('completion_photo');

    expect($report->fresh()->status)->toBe('in_progress');
});

test('status cannot jump from reported to completed', function () {
    $verifier = User::factory()->verifier()->create();
    $report = Report::factory()->create(['status' => 'reported']);

    $this->actingAs($verifier)
        ->post(route('reports.status', $report), [
            'status' => 'completed',
            'completion_photo' => UploadedFile::fake()->image('selesai.jpg'),
        ])
        ->assertSessionHasErrors('status');

    expect($report->fresh()->status)->toBe('reported');
    $this->assertDatabaseMissing('report_histories', ['report_id' => $report->id, 'status' => 'completed']);
});

test('full workflow records complete history', function () {
    $reporter = User::factory()->reporter()->create();
    $verifier = User::factory()->verifier()->create();
    $technician = User::factory()->technician()->create();
    $report = Report::factory()->for($reporter, 'reporter')->create();
    $report->histories()->create(['user_id' => $reporter->id, 'status' => 'reported', 'note' => 'Laporan dibuat']);

    $this->actingAs($verifier)->post(route('reports.assign', $report), ['assigned_to' => $technician->id]);
    $this->actingAs($technician)->post(route('reports.status', $report), ['status' => 'in_progress']);
    $this->actingAs($technician)->post(route('reports.status', $report), [
        'status' => 'completed',
        'completion_photo' => UploadedFile::fake()->image('selesai.jpg'),
    ]);

    expect($report->histories()->orderBy('id')->pluck('status')->all())
        ->toBe(['reported', 'verified', 'in_progress', 'completed']);

    $this->actingAs($reporter)->get(route('reports.show', $report))
        ->assertOk()
        ->assertSee('Selesai')
        ->assertSee('storage/completion/', false);
});
