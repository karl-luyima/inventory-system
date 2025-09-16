@extends('layouts.admin')

@section('title', 'Reports Overview')
@section('page-title', '📑 Reports Overview')

@section('content')
<div class="p-6 bg-white rounded-xl shadow-md">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Sales Analyst Reports</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <table class="w-full table-auto border border-gray-200">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 border-b">#</th>
                <th class="px-4 py-2 border-b text-left">Report Name</th>
                <th class="px-4 py-2 border-b text-left">Created By</th>
                <th class="px-4 py-2 border-b text-left">Created At</th>
                <th class="px-4 py-2 border-b">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $index => $report)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-2 border-b">{{ $index + $reports->firstItem() }}</td>
                <td class="px-4 py-2 border-b">{{ $report->name }}</td>
                <td class="px-4 py-2 border-b">{{ ucfirst($report->creator_type) }} ID: {{ $report->creator_id }}</td>
                <td class="px-4 py-2 border-b">{{ $report->created_at->format('d M Y, H:i') }}</td>
                <td class="px-4 py-2 border-b text-center space-x-2">
                    <a href="{{ route('admin.reports.view', $report->id) }}" class="text-blue-600 hover:underline">👁 View</a>
                    <form action="{{ route('admin.reports.delete', $report->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">✖ Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center py-4 text-gray-500">No reports found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $reports->links() }}
    </div>
</div>
@endsection
