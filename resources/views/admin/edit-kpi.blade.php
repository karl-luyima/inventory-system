@extends('layouts.admin')

@section('title', 'Edit KPI')
@section('page-title', '✎ Edit KPI')

@section('content')
<div class="p-8">
    <div class="bg-white p-6 rounded-xl shadow-md max-w-lg mx-auto">
        <h2 class="text-xl font-semibold mb-6 text-gray-800">Update KPI</h2>

        {{-- Display Validation Errors --}}
        @if ($errors->any())
            <div class="mb-4 bg-red-100 text-red-700 p-3 rounded-lg shadow-sm">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.kpis.update', $kpi->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="title" class="block text-gray-700 font-medium mb-1">Title</label>
                <input type="text" id="title" name="title" value="{{ old('title', $kpi->title) }}"
                       class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-blue-200" required>
            </div>

            <div>
                <label for="value" class="block text-gray-700 font-medium mb-1">Value</label>
                <input type="text" id="value" name="value" value="{{ old('value', $kpi->value) }}"
                       class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-blue-200" required>
            </div>

            <div>
                <label for="color" class="block text-gray-700 font-medium mb-1">Theme Color</label>
                <select id="color" name="color" 
                        class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-blue-200">
                    @foreach (['blue','green','yellow','red','purple'] as $color)
                        <option value="{{ $color }}" {{ $kpi->color === $color ? 'selected' : '' }}>
                            {{ ucfirst($color) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 shadow">
                    Update KPI
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
