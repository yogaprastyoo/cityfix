<?php

use App\Models\Area;
use App\Models\Category;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * @return array<string, mixed>
 */
function validReportPayload(array $overrides = []): array
{
    return [
        'area_id' => Area::factory()->create()->id,
        'category_id' => Category::factory()->create()->id,
        'location_detail' => 'Toilet lantai 2',
        'description' => 'Keran terus mengeluarkan air.',
        'urgency' => 'medium',
        'photo' => UploadedFile::fake()->create('keran-bocor.jpg', 200, 'image/jpeg'),
        ...$overrides,
    ];
}

beforeEach(function () {
    Storage::fake('public');
});

test('reporter can open create report page', function () {
    $user = User::factory()->reporter()->create();

    $this->actingAs($user)->get('/reports/create')->assertOk();
});

test('reporter can create report with photo', function () {
    $user = User::factory()->reporter()->create();
    $payload = validReportPayload();

    $response = $this->actingAs($user)->post('/reports', $payload);

    $response->assertSessionHasNoErrors();
    $report = Report::sole();
    $response->assertRedirect(route('reports.show', $report));

    expect($report)
        ->user_id->toBe($user->id)
        ->area_id->toBe($payload['area_id'])
        ->status->toBe('reported')
        ->report_number->toBe(sprintf('CF-%s-000001', now()->year));

    Storage::disk('public')->assertExists($report->photo);
    $this->assertDatabaseHas('report_histories', [
        'report_id' => $report->id,
        'status' => 'reported',
    ]);
});

test('report number increments sequentially per year', function () {
    $user = User::factory()->reporter()->create();
    Report::factory()->create(['report_number' => sprintf('CF-%s-000041', now()->year)]);

    $this->actingAs($user)->post('/reports', validReportPayload());

    $this->assertDatabaseHas('reports', ['report_number' => sprintf('CF-%s-000042', now()->year)]);
});

test('non image file is rejected', function () {
    $user = User::factory()->reporter()->create();

    $response = $this->actingAs($user)->post('/reports', validReportPayload([
        'photo' => UploadedFile::fake()->create('dokumen.pdf', 500, 'application/pdf'),
    ]));

    $response->assertSessionHasErrors('photo');
    $this->assertDatabaseCount('reports', 0);
});

test('photo larger than 5 MB is rejected', function () {
    $user = User::factory()->reporter()->create();

    $response = $this->actingAs($user)->post('/reports', validReportPayload([
        'photo' => UploadedFile::fake()->create('besar.jpg', 6000, 'image/jpeg'),
    ]));

    $response->assertSessionHasErrors('photo');
    $this->assertDatabaseCount('reports', 0);
});

test('report cannot be created with empty fields', function () {
    $user = User::factory()->reporter()->create();

    $response = $this->actingAs($user)->post('/reports', []);

    $response->assertSessionHasErrors([
        'area_id', 'category_id', 'location_detail',
        'description', 'urgency', 'photo',
    ]);
    $this->assertDatabaseCount('reports', 0);
});

test('invalid urgency value is rejected', function () {
    $user = User::factory()->reporter()->create();

    $response = $this->actingAs($user)->post('/reports', validReportPayload(['urgency' => 'super_urgent']));

    $response->assertSessionHasErrors('urgency');
    $this->assertDatabaseCount('reports', 0);
});

test('technician cannot create report', function () {
    $technician = User::factory()->technician()->create();

    $this->actingAs($technician)->get('/reports/create')->assertForbidden();
    $this->actingAs($technician)->post('/reports', validReportPayload())->assertForbidden();
    $this->assertDatabaseCount('reports', 0);
});

test('report list can be searched and filtered', function () {
    $admin = User::factory()->admin()->create();
    $match = Report::factory()->create(['report_number' => 'CF-2026-000123', 'status' => 'reported', 'urgency' => 'high']);
    $other = Report::factory()->create(['report_number' => 'CF-2026-000999', 'status' => 'completed', 'urgency' => 'low']);

    $this->actingAs($admin)->get('/reports?search=000123')
        ->assertSee($match->report_number)
        ->assertDontSee($other->report_number);

    $this->actingAs($admin)->get('/reports?status=completed')
        ->assertSee($other->report_number)
        ->assertDontSee($match->report_number);

    $this->actingAs($admin)->get('/reports?urgency=high&area_id='.$match->area_id)
        ->assertSee($match->report_number)
        ->assertDontSee($other->report_number);
});
