@extends('layouts.inventoryclerk')

@section('page-title', 'Dashboard Metrics')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

    @forelse($kpis as $kpi)
        <div class="bg-white p-6 rounded-lg shadow" style="border-top: 4px solid {{ $kpi->color }};">
            <h3 class="text-lg font-semibold mb-2">{{ $kpi->title }}</h3>
            <p class="text-3xl font-bold text-gray-700">{{ $kpi->value }}</p>
        </div>
    @empty
        <div class="col-span-full bg-white p-6 rounded-lg shadow">
            <p class="text-gray-600 text-center">No KPIs have been defined by the admin yet.</p>
        </div>
    @endforelse

</div> 
@endsection
