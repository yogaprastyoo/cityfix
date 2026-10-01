<?php

use App\Models\Report;
use App\Models\User;

test('dashboard counts match database', function () {
    $admin = User::factory()->admin()->create();
    Report::factory()->count(3)->create(['status' => 'reported']);
    Report::factory()->count(2)->create(['status' => 'in_progress']);
    Report::factory()->count(4)->create(['status' => 'completed']);
    Report::factory()->create(['status' => 'rejected']);

    $this->assertDatabaseCount('reports', 10);

    $this->actingAs($admin)->get('/dashboard')
        ->assertOk()
        ->assertViewHas('totalReports', 10)
        ->assertViewHas('reported', 3)
        ->assertViewHas('inProgress', 2)
        ->assertViewHas('completed', 4)
        ->assertViewHas('completionRate', 44.4);
});

test('dashboard counts overdue reports by urgency sla', function () {
    $admin = User::factory()->admin()->create();
    Report::factory()->create(['urgency' => 'emergency', 'status' => 'verified', 'reported_at' => now()->subHours(3)]);
    Report::factory()->create(['urgency' => 'emergency', 'status' => 'verified', 'reported_at' => now()->subHour()]);
    Report::factory()->create(['urgency' => 'high', 'status' => 'completed', 'reported_at' => now()->subDays(5)]);
    Report::factory()->create(['urgency' => 'low', 'status' => 'reported', 'reported_at' => now()->subDays(30)]);

    $this->actingAs($admin)->get('/dashboard')
        ->assertViewHas('overdue', 1)
        ->assertViewHas('emergencyOpen', 2);
});

test('dashboard latest reports are limited to reporter own reports', function () {
    $reporter = User::factory()->reporter()->create();
    $own = Report::factory()->for($reporter, 'reporter')->create();
    $other = Report::factory()->create();

    $this->actingAs($reporter)->get('/dashboard')
        ->assertSee($own->report_number)
        ->assertDontSee($other->report_number);
});
