@extends('layouts.admin')

@section('title', 'Edit KPI')
@section('page-title', '✎ Edit KPI')

@section('content')
<div class="p-8">
    <div class="bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-xl font-semibold mb-4">Update KPI</h2>
        <form action="{{ route('admin.kpis.update', $kpi->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-gray-700">Title</label>
                <input type="text" name="title" value="{{ $kpi->title }}" 
                       class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-blue-200" required>
            </div>
            <div>
                <label class="block text-gray-700">Value</label>
                <input type="text" name="value" value="{{ $kpi->value }}" 
                       class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-blue-200" required>
            </div>
            <div>
                <label class="block text-gray-700">Theme Color</label>
                <select name="color" class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-blue-200">
                    <option value="blue" {{ $kpi->color == 'blue' ? 'selected' : '' }}>Blue</option>
                    <option value="green" {{ $kpi->color == 'green' ? 'selected' : '' }}>Green</option>
                    <option value="yellow" {{ $kpi->color == 'yellow' ? 'selected' : '' }}>Yellow</option>
                    <option value="red" {{ $kpi->color == 'red' ? 'selected' : '' }}>Red</option>
                    <option value="purple" {{ $kpi->color == 'purple' ? 'selected' : '' }}>Purple</option>
                </select>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Update KPI
            </button>
        </form>
    </div>
</div>
@endsection
