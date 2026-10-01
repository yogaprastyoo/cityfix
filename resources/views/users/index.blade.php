@extends('layouts.app')
@section('title', 'Master User | CityFix')
@section('content-header')
    <div class="row">
        <div class="col">
            <h1>Master User</h1>
        </div>
        <div class="col-auto text-right">
            <a href="{{ route('users.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah User</a>
        </div>
    </div>
@endsection
@section('content')
    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table-hover table text-nowrap">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td><span class="badge badge-info">{{ \App\Models\User::ROLES[$user->role] ?? $user->role }}</span></td>
                            <td>
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                @unless ($user->is(auth()->user()))
                                    <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline" onsubmit="return confirm('Hapus user ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                @endunless
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if ($users->hasPages())
            <div class="card-footer clearfix">{{ $users->links('pagination::bootstrap-4') }}</div>
        @endif
    </div>
@endsection
