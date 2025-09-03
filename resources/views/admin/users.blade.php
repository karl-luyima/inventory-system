@extends('layouts.admin')

@section('content')
<div class="p-8 space-y-6">
    <h1 class="text-2xl font-bold text-gray-700">👤 Manage Users</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white p-6 rounded-xl shadow-md">
        <table class="w-full border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 border">User ID</th>
                    <th class="p-2 border">Role</th>
                    <th class="p-2 border">Created At</th>
                    <th class="p-2 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td class="p-2 border">{{ $user->id }}</td>
                    <td class="p-2 border">{{ $user->role }}</td>
                    <td class="p-2 border">{{ $user->created_at->format('Y-m-d') }}</td>
                    <td class="p-2 border text-center">
                        <form action="{{ route('admin.deleteUser', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                            @csrf
                            @method('DELETE')
                            <button class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-4 text-center text-gray-500">No users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
