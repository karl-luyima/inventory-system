@extends('layouts.admin')

@section('content')
<div class="p-8 space-y-6">
    <h1 class="text-2xl font-bold text-gray-700">👥 Manage Users</h1>

    {{-- Flash message --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Users Overview --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-blue-100 text-blue-700 p-4 rounded shadow">
            <h2 class="font-bold text-lg">Total Users</h2>
            <p class="text-2xl">{{ $totalUsers }}</p>
        </div>
        <div class="bg-green-100 text-green-700 p-4 rounded shadow">
            <h2 class="font-bold text-lg">Active Users</h2>
            <p class="text-2xl">{{ $activeUsers }}</p>
        </div>
        <div class="bg-red-100 text-red-700 p-4 rounded shadow">
            <h2 class="font-bold text-lg">Inactive Users</h2>
            <p class="text-2xl">{{ $inactiveUsers }}</p>
        </div>
    </div>

    {{-- Status Filter --}}
    <div class="mt-6">
        <form method="GET" action="{{ route('admin.users') }}" class="flex items-center space-x-2">
            <label for="status" class="text-gray-600">Filter by Status:</label>
            <select name="status" id="status" onchange="this.form.submit()" class="border rounded px-3 py-2">
                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
                <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </form>
    </div>

    {{-- Users Table --}}
    <div class="bg-white p-6 rounded-xl shadow-md mt-4">
        <table class="w-full border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 border">User ID</th>
                    <th class="p-2 border">Role</th>
                    <th class="p-2 border">Status</th>
                    <th class="p-2 border">Created At</th>
                    <th class="p-2 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td class="p-2 border">{{ $user->id }}</td>
                    <td class="p-2 border">{{ $user->role }}</td>
                    <td class="p-2 border">
                        @if($user->active)
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-sm">Active</span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-sm">Inactive</span>
                        @endif
                    </td>
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
                    <td colspan="5" class="p-4 text-center text-gray-500">No users found.</td>
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
