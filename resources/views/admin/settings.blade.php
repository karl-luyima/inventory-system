@extends('layouts.admin')

@section('content')
<div class="p-8 space-y-6">
    <h1 class="text-2xl font-bold text-gray-700">⚙️ Settings</h1>

    <div class="bg-white p-6 rounded-xl shadow-md space-y-4">
        <form>
            <div>
                <label class="block text-gray-700">Company Name</label>
                <input type="text" value="InventoryPro" class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-blue-200">
            </div>
            <div>
                <label class="block text-gray-700">Email Notifications</label>
                <select class="w-full px-3 py-2 border rounded-lg">
                    <option>Enabled</option>
                    <option>Disabled</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Save Changes
            </button>
        </form>
    </div>
</div>
@endsection
