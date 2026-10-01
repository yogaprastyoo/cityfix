<?php

use App\Models\User;

test('user with temporary password is forced to change it', function () {
    $user = User::factory()->create(['must_change_password' => true]);

    $this->actingAs($user)->get('/dashboard')->assertRedirect('/password');
    $this->actingAs($user)->get('/password')->assertOk();
});

test('user can change temporary password', function () {
    $user = User::factory()->create(['password' => bcrypt('sementara123'), 'must_change_password' => true]);

    $this->actingAs($user)->put('/password', [
        'current_password' => 'sementara123',
        'password' => 'passwordbaru123',
        'password_confirmation' => 'passwordbaru123',
    ])->assertRedirect('/dashboard');

    $user->refresh();
    expect($user->must_change_password)->toBeFalse();
    expect(password_verify('passwordbaru123', $user->password))->toBeTrue();
    $this->actingAs($user)->get('/dashboard')->assertOk();
});

test('password change requires correct current password', function () {
    $user = User::factory()->create(['password' => bcrypt('sementara123'), 'must_change_password' => true]);

    $this->actingAs($user)->put('/password', [
        'current_password' => 'salah',
        'password' => 'passwordbaru123',
        'password_confirmation' => 'passwordbaru123',
    ])->assertSessionHasErrors('current_password');

    expect($user->fresh()->must_change_password)->toBeTrue();
});

test('user created by admin must change password on first login', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->post('/users', [
        'name' => 'Pelapor Baru',
        'email' => 'pelapor@cityfix.local',
        'role' => 'reporter',
        'password' => 'rahasia123',
        'password_confirmation' => 'rahasia123',
    ]);

    expect(User::where('email', 'pelapor@cityfix.local')->sole()->must_change_password)->toBeTrue();
});

test('create admin command creates admin that must change password', function () {
    $this->artisan('cityfix:create-admin', ['--name' => 'Admin Sekolah', '--email' => 'admin@sekolah.sch.id'])
        ->expectsQuestion('Password sementara (min. 12 karakter)', 'SangatKuat12345')
        ->assertSuccessful();

    $admin = User::where('email', 'admin@sekolah.sch.id')->sole();
    expect($admin->role)->toBe('admin');
    expect($admin->must_change_password)->toBeTrue();
});

test('create admin command rejects weak password', function () {
    $this->artisan('cityfix:create-admin', ['--name' => 'Admin Sekolah', '--email' => 'admin@sekolah.sch.id'])
        ->expectsQuestion('Password sementara (min. 12 karakter)', 'admin123')
        ->assertFailed();

    $this->assertDatabaseMissing('users', ['email' => 'admin@sekolah.sch.id']);
});
