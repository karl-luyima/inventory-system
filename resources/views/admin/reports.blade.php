@extends('layouts.admin')

@section('title', 'Reports Overview')
@section('page-title', '📑 Reports Overview')

@section('content')
<div class="p-8 space-y-6">

    {{-- Reports Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-md text-center">
            <h2 class="text-lg font-semibold text-gray-700">Total Reports</h2>
            <p id="reportCount" class="text-3xl font-bold text-blue-600 mt-2">0</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-md text-center">
            <h2 class="text-lg font-semibold text-gray-700">Sales Reports</h2>
            <p id="salesReports" class="text-3xl font-bold text-green-600 mt-2">0</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-md text-center">
            <h2 class="text-lg font-semibold text-gray-700">Inventory Reports</h2>
            <p id="inventoryReports" class="text-3xl font-bold text-purple-600 mt-2">0</p>
        </div>
    </div>

    {{-- Reports Table --}}
    <div class="bg-white p-6 rounded-xl shadow-md mt-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">📋 Latest Reports</h2>
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="p-3 text-left">Report Name</th>
                    <th class="p-3 text-left">Type</th>
                    <th class="p-3 text-left">Created At</th>
                    <th class="p-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody id="reportTable"></tbody>
        </table>
    </div>

</div>

<script>
    function fetchReportsData() {
        fetch("{{ route('admin.reports.data') }}")
            .then(res => res.json())
            .then(data => {
                document.getElementById('reportCount').innerText = data.totalReports;
                document.getElementById('salesReports').innerText = data.salesReports;
                document.getElementById('inventoryReports').innerText = data.inventoryReports;

                let table = document.getElementById('reportTable');
                table.innerHTML = "";
                data.reports.forEach(r => {
                    let row = `<tr class="border-b">
                        <td class="p-3">${r.name}</td>
                        <td class="p-3">${r.type}</td>
                        <td class="p-3">${r.created_at}</td>
                        <td class="p-3">
                            <a href="/admin/reports/view/${r.id}" class="text-blue-600 hover:underline">👁 View</a>
                            <a href="/admin/reports/delete/${r.id}" class="text-red-600 hover:underline ml-2">✖ Delete</a>
                        </td>
                    </tr>`;
                    table.innerHTML += row;
                });
            })
            .catch(err => console.error("Error:", err));
    }

    fetchReportsData();
    setInterval(fetchReportsData, 10000);
</script>
@endsection
