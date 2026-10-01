<?php

use App\Models\Report;
use App\Models\User;

test('reporter can view own report', function () {
    $reporter = User::factory()->reporter()->create();
    $report = Report::factory()->for($reporter, 'reporter')->create();

    $this->actingAs($reporter)->get(route('reports.show', $report))->assertOk();
});

test('reporter cannot view other reporter report', function () {
    $reporter = User::factory()->reporter()->create();
    $report = Report::factory()->create();

    $this->actingAs($reporter)->get(route('reports.show', $report))->assertForbidden();
});

test('reporter list only shows own reports', function () {
    $reporter = User::factory()->reporter()->create();
    $own = Report::factory()->for($reporter, 'reporter')->create();
    $other = Report::factory()->create();

    $this->actingAs($reporter)->get('/reports')
        ->assertSee($own->report_number)
        ->assertDontSee($other->report_number);
});

test('technician can view assigned task only', function () {
    $technician = User::factory()->technician()->create();
    $assigned = Report::factory()->create(['assigned_to' => $technician->id, 'status' => 'verified']);
    $notAssigned = Report::factory()->create();

    $this->actingAs($technician)->get(route('reports.show', $assigned))->assertOk();
    $this->actingAs($technician)->get(route('reports.show', $notAssigned))->assertForbidden();

    $this->actingAs($technician)->get('/reports')
        ->assertSee($assigned->report_number)
        ->assertDontSee($notAssigned->report_number);
});

test('verifier can view all reports', function () {
    $verifier = User::factory()->verifier()->create();
    $reports = Report::factory()->count(2)->create();

    $this->actingAs($verifier)->get('/reports')
        ->assertSee($reports[0]->report_number)
        ->assertSee($reports[1]->report_number);
    $this->actingAs($verifier)->get(route('reports.show', $reports[0]))->assertOk();
});

test('reporter cannot assign technician', function () {
    $reporter = User::factory()->reporter()->create();
    $technician = User::factory()->technician()->create();
    $report = Report::factory()->for($reporter, 'reporter')->create();

    $this->actingAs($reporter)
        ->post(route('reports.assign', $report), ['assigned_to' => $technician->id])
        ->assertForbidden();

    expect($report->fresh()->assigned_to)->toBeNull();
});

test('reporter cannot update status', function () {
    $reporter = User::factory()->reporter()->create();
    $report = Report::factory()->for($reporter, 'reporter')->create();

    $this->actingAs($reporter)
        ->post(route('reports.status', $report), ['status' => 'verified'])
        ->assertForbidden();

    expect($report->fresh()->status)->toBe('reported');
});

test('technician cannot update status of task assigned to someone else', function () {
    $technician = User::factory()->technician()->create();
    $report = Report::factory()->create([
        'assigned_to' => User::factory()->technician(),
        'status' => 'verified',
    ]);

    $this->actingAs($technician)
        ->post(route('reports.status', $report), ['status' => 'in_progress'])
        ->assertForbidden();

    expect($report->fresh()->status)->toBe('verified');
});

test('technician cannot reject or verify reports', function () {
    $technician = User::factory()->technician()->create();
    $report = Report::factory()->create(['assigned_to' => $technician->id, 'status' => 'verified']);

    $this->actingAs($technician)
        ->post(route('reports.status', $report), ['status' => 'rejected'])
        ->assertForbidden();

    expect($report->fresh()->status)->toBe('verified');
});
