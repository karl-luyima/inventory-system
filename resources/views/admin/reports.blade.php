@extends('layouts.admin')

@section('content')
<div class="p-8 bg-gray-50 min-h-screen">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Reports Dashboard</h1>

        {{-- ✅ Fixed styled button --}}
        <a href="{{ route('admin.reports.generate') }}" 
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white font-medium
                  bg-gradient-to-r from-indigo-500 to-blue-600 hover:from-indigo-600 hover:to-blue-700
                  shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition duration-200">
            <i class="fas fa-plus-circle"></i>
            Generate Summary Report
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
        <div class="border-b border-gray-200 px-6 py-3">
            <h2 class="text-lg font-semibold text-gray-800">Available Reports</h2>
        </div>

        <div class="p-6">
            @if ($reports->count() > 0)
                <table class="min-w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700 text-left text-sm uppercase">
                            <th class="py-3 px-4">#</th>
                            <th class="py-3 px-4">Report Name</th>
                            <th class="py-3 px-4">Created By</th>
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $report)
                            <tr class="border-t hover:bg-gray-50">
                                <td class="py-3 px-4">{{ $loop->iteration }}</td>
                                <td class="py-3 px-4 font-medium">{{ $report->name }}</td>
                                <td class="py-3 px-4 capitalize">{{ $report->creator_type }}</td>
                                <td class="py-3 px-4">{{ $report->created_at->format('d M Y, H:i') }}</td>
                                <td class="py-3 px-4 flex justify-center gap-3">
                                    <a href="{{ route('admin.reports.view', $report->id) }}" 
                                       class="text-blue-600 hover:text-blue-800 font-semibold">View</a>
                                    <a href="{{ route('admin.reports.download', $report->id) }}" 
                                       class="text-green-600 hover:text-green-800 font-semibold">Download</a>
                                    <form action="{{ route('admin.reports.delete', $report->id) }}" method="POST" onsubmit="return confirm('Delete this report?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-semibold">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $reports->links() }}
                </div>
            @else
                <div class="text-center text-gray-500 py-12">
                    <p class="text-xl font-semibold mb-2">No reports available yet.</p>
                    <p>Click <strong>“Generate Summary Report”</strong> to create your first report.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
