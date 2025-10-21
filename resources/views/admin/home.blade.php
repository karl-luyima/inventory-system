@extends('layouts.admin')

@section('title', 'Admin Home')

@section('content')
<div class="max-w-6xl ml-10">
    <!-- Quick Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow text-center">
            <h2 class="text-gray-700 font-medium">Total Users</h2>
            <p class="text-3xl font-bold text-blue-600 mt-2">{{ $totalUsers }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow text-center">
            <h2 class="text-gray-700 font-medium">Active KPIs</h2>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ $activeKpis }}</p>
        </div>
    </div>
</div>
@endsection
