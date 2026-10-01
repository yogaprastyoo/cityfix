<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::orderBy('name')->paginate(15);

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        return view('users.form', ['user' => new User(['role' => 'reporter'])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        User::create([...$validated, 'must_change_password' => true]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        return view('users.form', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        } elseif (! $user->is($request->user())) {
            $validated['must_change_password'] = true;
        }

        if ($user->is($request->user()) && $validated['role'] !== 'admin') {
            return back()->withErrors(['role' => 'Anda tidak dapat menurunkan role akun sendiri.']);
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->withErrors(['user' => 'Anda tidak dapat menghapus akun sendiri.']);
        }

        if ($user->reports()->exists()) {
            return back()->withErrors(['user' => 'User sudah memiliki laporan dan tidak dapat dihapus.']);
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
