@extends('layouts.admin')

@section('title', 'Inventory Dashboard')
@section('page-title', '📦 Inventory Dashboard')

@section('content')
<div class="max-w-5xl mx-auto space-y-8">
    
    {{-- Inventory Overview --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-md text-center">
            <h2 class="text-lg font-semibold text-gray-700">Total Products</h2>
            <p id="totalProducts" class="text-3xl font-bold text-orange-600 mt-2">0</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-md">
            <h2 class="text-lg font-semibold text-gray-700">Low Stock Alerts</h2>
            <ul id="lowStockList" class="mt-3 text-red-600 list-disc pl-6 space-y-1">
                <li>Loading...</li>
            </ul>
        </div>
    </div>

</div>

<script>
    function fetchInventoryData() {
        fetch("{{ route('admin.inventory.data') }}")
            .then(res => res.json())
            .then(data => {
                document.getElementById('totalProducts').innerText = data.totalProducts;

                let list = document.getElementById('lowStockList');
                list.innerHTML = "";
                if (data.lowStockItems.length > 0) {
                    data.lowStockItems.forEach(item => {
                        let li = document.createElement('li');
                        li.innerText = `${item.name} (Stock: ${item.stock})`;
                        list.appendChild(li);
                    });
                } else {
                    list.innerHTML = "<li>No low stock items 🎉</li>";
                }
            })
            .catch(err => console.error("Error:", err));
    }

    fetchInventoryData();
    setInterval(fetchInventoryData, 10000);
</script>
@endsection
