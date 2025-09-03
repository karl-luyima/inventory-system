@extends('layouts.admin')

@section('title', 'KPIs')
@section('page-title', '📑 Manage KPIs')

@section('content')
<div class="p-8 space-y-6">

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($kpis as $kpi)
            <div class="bg-gradient-to-r from-{{ $kpi->color }}-500 to-{{ $kpi->color }}-600 text-white p-6 rounded-2xl shadow-lg relative">
                <h2 class="text-lg font-semibold">{{ $kpi->title }}</h2>
                <p class="text-3xl font-bold mt-2">{{ $kpi->value }}</p>

                <div class="absolute top-3 right-3 flex gap-2">
                    <a href="{{ route('admin.kpis.edit', $kpi->id) }}" class="bg-yellow-400 text-white px-2 py-1 rounded text-xs hover:bg-yellow-500">✎</a>
                    <form action="{{ route('admin.kpis.delete', $kpi->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="bg-red-600 text-white px-2 py-1 rounded text-xs hover:bg-red-700">✖</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <div class="bg-white p-6 rounded-xl shadow-md mt-8">
        <h2 class="text-xl font-semibold mb-4">➕ Add New KPI</h2>
        <form action="{{ route('admin.kpis.add') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-gray-700">Title</label>
                <input type="text" name="title" class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-blue-200" required>
            </div>
            <div>
                <label class="block text-gray-700">Value</label>
                <input type="text" name="value" class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-blue-200" required>
            </div>
            <div>
                <label class="block text-gray-700">Theme Color</label>
                <select name="color" class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-blue-200">
                    <option value="blue">Blue</option>
                    <option value="green">Green</option>
                    <option value="yellow">Yellow</option>
                    <option value="red">Red</option>
                    <option value="purple">Purple</option>
                </select>
            </div>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                Add KPI
            </button>
        </form>
    </div>
</div>
@endsection
