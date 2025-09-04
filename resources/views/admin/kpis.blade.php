@extends('layouts.admin')

@section('title', 'KPIs')
@section('page-title', '📑 Manage KPIs')

@section('content')
<div class="p-8 space-y-6">

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded-lg shadow-sm">
            ✅ {{ session('success') }}
        </div>
    @endif

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @forelse($kpis as $kpi)
            <div class="relative bg-gradient-to-r from-{{ $kpi->color }}-500 to-{{ $kpi->color }}-600 text-white p-6 rounded-2xl shadow-lg transition transform hover:scale-105 hover:shadow-xl">
                <h2 class="text-lg font-semibold">{{ $kpi->title }}</h2>
                <p class="text-4xl font-bold mt-2">{{ $kpi->value }}</p>

                {{-- Action Buttons --}}
                <div class="absolute top-3 right-3 flex gap-2">
                    <a href="{{ route('admin.kpis.edit', $kpi->id) }}" 
                       class="bg-yellow-400 text-white px-2 py-1 rounded text-xs hover:bg-yellow-500 shadow">
                        ✎
                    </a>
                    <form action="{{ route('admin.kpis.delete', $kpi->id) }}" method="POST" onsubmit="return confirm('Delete this KPI?');">
                        @csrf
                        @method('DELETE')
                        <button class="bg-red-600 text-white px-2 py-1 rounded text-xs hover:bg-red-700 shadow">✖</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="col-span-3 text-center text-gray-500">No KPIs yet. Add one below 👇</p>
        @endforelse
    </div>

    {{-- Add KPI Form --}}
    <div class="bg-white p-6 rounded-xl shadow-md mt-10">
        <h2 class="text-xl font-semibold mb-4 text-gray-800">➕ Add New KPI</h2>
        <form action="{{ route('admin.kpis.add') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @csrf
            <div>
                <label class="block text-gray-700 font-medium mb-1">Title</label>
                <input type="text" name="title" class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-blue-200" required>
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1">Value</label>
                <input type="text" name="value" class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-blue-200" required>
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1">Theme Color</label>
                <select name="color" class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-blue-200">
                    <option value="blue">Blue</option>
                    <option value="green">Green</option>
                    <option value="yellow">Yellow</option>
                    <option value="red">Red</option>
                    <option value="purple">Purple</option>
                </select>
            </div>
            <div class="md:col-span-3 flex justify-end">
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 shadow">
                    ➕ Add KPI
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
