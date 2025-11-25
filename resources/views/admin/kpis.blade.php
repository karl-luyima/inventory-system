@extends('layouts.admin')

@section('title', 'KPIs')
@section('page-title', '📑 Manage KPIs')

@section('content')
<div class="p-8 space-y-8">

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded-lg shadow-sm">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded-lg shadow-sm">
            ❌ {{ session('error') }}
        </div>
    @endif

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded-lg shadow-sm">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- KPI Cards --}}
    @php
        $colorMap = [
            'blue'   => 'bg-gradient-to-r from-kpi-blue to-kpi-blue/80',
            'green'  => 'bg-gradient-to-r from-kpi-green to-kpi-green/80',
            'yellow' => 'bg-gradient-to-r from-kpi-yellow to-kpi-yellow/80',
            'red'    => 'bg-gradient-to-r from-kpi-red to-kpi-red/80',
            'purple' => 'bg-gradient-to-r from-kpi-purple to-kpi-purple/80',
        ];
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        @forelse($kpis as $kpi)
            <div class="relative p-6 rounded-2xl shadow-lg transition transform hover:scale-105 hover:shadow-xl {{ $colorMap[$kpi->color] ?? 'bg-gray-500' }} flex flex-col">

                {{-- KPI Title & Value --}}
                <div>
                    <h2 class="text-base font-semibold text-black break-words">{{ $kpi->title ?? 'No Title' }}</h2>
                    <p class="text-2xl font-bold mt-1 text-black">{{ $kpi->value ?? 'N/A' }}</p>
                </div>

                {{-- Action Buttons at bottom-right --}}
                <div class="flex gap-2 mt-auto justify-end">
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
    <div class="bg-white p-6 rounded-xl shadow-md mt-10 max-w-4xl mx-auto">
        <h2 class="text-xl font-semibold mb-4 text-gray-800">➕ Add New KPI</h2>
        <form action="{{ route('admin.kpis.add') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @csrf
            <div>
                <label class="block text-gray-700 font-medium mb-1">Title</label>
                <input type="text" name="title" value="{{ old('title') }}"
                       class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-blue-200" required>
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1">Value</label>
                <input type="text" name="value" value="{{ old('value') }}"
                       class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-blue-200" required>
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1">Theme Color</label>
                <select name="color" class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-blue-200">
                    @foreach(['blue','green','yellow','red','purple'] as $color)
                        <option value="{{ $color }}" {{ old('color') == $color ? 'selected' : '' }}>
                            {{ ucfirst($color) }}
                        </option>
                    @endforeach
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
