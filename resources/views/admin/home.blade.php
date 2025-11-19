@extends('layouts.admin')

@section('title', 'Admin Home')

@section('content')
<div class="max-w-6xl ml-10">

    <h2 class="text-2xl font-semibold mb-6">Dashboard</h2>

    <!-- Top Quick Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow text-center">
            <h2 class="text-gray-700 font-medium">Total Users</h2>
            <p class="text-3xl font-bold text-blue-600 mt-2">{{ $totalUsers }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow text-center">
            <h2 class="text-gray-700 font-medium">Active KPIs</h2>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ $activeKpis->count() }}</p>
        </div>
    </div>

    <h3 class="text-xl font-semibold mb-4">KPIs</h3>

    @php
        $colorMap = [
            'blue' => 'bg-gradient-to-r from-kpi-blue to-kpi-blue/80',
            'green' => 'bg-gradient-to-r from-kpi-green to-kpi-green/80',
            'yellow' => 'bg-gradient-to-r from-kpi-yellow to-kpi-yellow/80',
            'red' => 'bg-gradient-to-r from-kpi-red to-kpi-red/80',
            'purple' => 'bg-gradient-to-r from-kpi-purple to-kpi-purple/80',
        ];
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($activeKpis as $kpi)
            <div class="relative p-6 rounded-2xl shadow-lg text-white {{ $colorMap[$kpi->color] ?? 'bg-gray-500' }} flex flex-col justify-center items-center">
                <h3 class="text-base font-semibold drop-shadow-md">{{ $kpi->title }}</h3>
                <p class="text-2xl font-bold mt-2 drop-shadow-md">{{ $kpi->value }}</p>
            </div>
        @empty
            <p class="text-center text-gray-500 col-span-3">No KPIs added yet.</p>
        @endforelse
    </div>
</div>
@endsection
