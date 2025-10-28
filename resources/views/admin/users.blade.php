@extends('layouts.admin')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">👥 Users Management</h1>

    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="min-w-full border-collapse border border-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border-b">#</th>
                    <th class="px-4 py-2 border-b">Name</th>
                    <th class="px-4 py-2 border-b">Email</th>
                    <th class="px-4 py-2 border-b">Role</th>
                    <th class="px-4 py-2 border-b text-center">Action</th>
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
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-2 text-center">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
@endsection
