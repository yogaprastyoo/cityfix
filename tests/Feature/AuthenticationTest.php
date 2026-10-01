<?php

use App\Models\User;

test('login page can be opened', function () {
    $this->get('/login')->assertOk();
});

test('user can login with valid credentials', function () {
    $user = User::factory()->create(['password' => bcrypt('password123')]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($user);
});

test('user cannot login with wrong password', function () {
    $user = User::factory()->create(['password' => bcrypt('password123')]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('user can logout', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/logout')->assertRedirect('/login');

    $this->assertGuest();
});

test('guest cannot open dashboard', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

test('authenticated user can open dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/dashboard')->assertOk();
});
