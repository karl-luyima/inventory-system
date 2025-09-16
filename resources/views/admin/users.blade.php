@extends('layouts.admin')

@section('title', 'Users')
@section('page-title', '👥 Users Management')

@section('content')
<div class="p-6 bg-white rounded-xl shadow-md">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Users List</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <table class="w-full table-auto border border-gray-200">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 border-b">#</th>
                <th class="px-4 py-2 border-b text-left">Name</th>
                <th class="px-4 py-2 border-b text-left">Email</th>
                <th class="px-4 py-2 border-b text-left">Role</th>
                <th class="px-4 py-2 border-b">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $index => $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 border-b">{{ $index + $users->firstItem() }}</td>
                    <td class="px-4 py-2 border-b">{{ $user->name }}</td>
                    <td class="px-4 py-2 border-b">{{ $user->email ?? 'N/A' }}</td>
                    <td class="px-4 py-2 border-b">{{ $user->role }}</td>
                    <td class="px-4 py-2 border-b text-center">
                        <form action="{{ route('admin.deleteUser', $user->id) }}?type={{ $user->type }}" method="POST"
                              onsubmit="return confirm('Are you sure you want to delete this user?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-gray-500">No users found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
@endsection
