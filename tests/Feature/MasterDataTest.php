<?php

use App\Models\Area;
use App\Models\Category;
use App\Models\Report;
use App\Models\User;

test('non admin roles cannot access master data', function (string $role, string $uri) {
    $user = User::factory()->create(['role' => $role]);

    $this->actingAs($user)->get($uri)->assertForbidden();
})->with(['reporter', 'technician', 'verifier'])->with(['/areas', '/categories', '/users']);

test('admin can access master data', function (string $uri) {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get($uri)->assertOk();
})->with(['/areas', '/categories', '/users']);

test('monitoring is only available for admin and verifier', function () {
    $this->actingAs(User::factory()->verifier()->create())->get('/monitoring')->assertOk();
    $this->actingAs(User::factory()->admin()->create())->get('/monitoring')->assertOk();
    $this->actingAs(User::factory()->reporter()->create())->get('/monitoring')->assertForbidden();
    $this->actingAs(User::factory()->technician()->create())->get('/monitoring')->assertForbidden();
});

test('admin can create update and delete area', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->post('/areas', ['name' => 'Gedung C', 'code' => 'GC', 'is_active' => '1'])
        ->assertRedirect('/areas');
    $area = Area::where('name', 'Gedung C')->sole();
    expect($area->is_active)->toBeTrue();

    $this->actingAs($admin)->put("/areas/{$area->id}", ['name' => 'Gedung C Baru'])
        ->assertRedirect('/areas');
    expect($area->fresh())->name->toBe('Gedung C Baru')->is_active->toBeFalse();

    $this->actingAs($admin)->delete("/areas/{$area->id}")->assertRedirect('/areas');
    $this->assertModelMissing($area);
});

test('area with reports cannot be deleted', function () {
    $admin = User::factory()->admin()->create();
    $report = Report::factory()->create();

    $this->actingAs($admin)->delete("/areas/{$report->area_id}")->assertSessionHasErrors('area');

    $this->assertModelExists($report->area);
});

test('inactive area is not offered on report form', function () {
    $reporter = User::factory()->reporter()->create();
    Area::factory()->create(['name' => 'Area Aktif']);
    Area::factory()->create(['name' => 'Area Nonaktif', 'is_active' => false]);

    $this->actingAs($reporter)->get('/reports/create')
        ->assertSee('Area Aktif')
        ->assertDontSee('Area Nonaktif');
});

test('admin can create category', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->post('/categories', ['name' => 'Taman', 'is_active' => '1'])
        ->assertRedirect('/categories');

    $this->assertDatabaseHas('categories', ['name' => 'Taman', 'is_active' => true]);
});

test('category with reports cannot be deleted', function () {
    $admin = User::factory()->admin()->create();
    $report = Report::factory()->create();

    $this->actingAs($admin)->delete("/categories/{$report->category_id}")->assertSessionHasErrors('category');

    $this->assertModelExists(Category::find($report->category_id));
});

test('admin can create user with role', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->post('/users', [
        'name' => 'Teknisi Baru',
        'email' => 'teknisi@cityfix.local',
        'role' => 'technician',
        'password' => 'rahasia123',
        'password_confirmation' => 'rahasia123',
    ])->assertRedirect('/users');

    $this->assertDatabaseHas('users', ['email' => 'teknisi@cityfix.local', 'role' => 'technician']);
});

test('user role must be valid', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->post('/users', [
        'name' => 'Hacker',
        'email' => 'hacker@cityfix.local',
        'role' => 'superadmin',
        'password' => 'rahasia123',
        'password_confirmation' => 'rahasia123',
    ])->assertSessionHasErrors('role');

    $this->assertDatabaseMissing('users', ['email' => 'hacker@cityfix.local']);
});

test('admin cannot delete own account', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->delete("/users/{$admin->id}")->assertSessionHasErrors('user');

    $this->assertModelExists($admin);
});
