@extends('layouts.admin')

@section('title', 'Sales Dashboard')
@section('page-title', '💰 Sales Dashboard')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">

    {{-- Sale Entry Form --}}
    <div class="bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Record a New Sale</h2>

        <form id="saleForm" class="space-y-4">
            @csrf
            <div>
                <label class="block text-gray-700 font-medium">Product Name</label>
                <input type="text" name="pdt_name" id="pdt_name" placeholder="Enter product name"
                    class="w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500" required>
            </div>

            <div>
                <label class="block text-gray-700 font-medium">Quantity</label>
                <input type="number" name="quantity" id="quantity" placeholder="Enter quantity"
                    class="w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500" required>
            </div>

            <div>
                <label class="block text-gray-700 font-medium">Total Amount (Ksh)</label>
                <input type="number" step="0.01" name="totalAmount" id="totalAmount" placeholder="Enter total amount"
                    class="w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500" required>
            </div>

            <button type="submit"
                class="flex items-center justify-center gap-2 bg-orange-600 hover:bg-orange-700 text-white px-5 py-2.5 rounded-lg shadow-md transition duration-200">
                💾 <span>Save Sale</span>
            </button>
        </form>

        <p id="responseMsg" class="text-center mt-4 text-sm text-gray-600"></p>
    </div>

    {{-- Recent Sales --}}
    <div class="bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Recent Sales</h2>
        <table class="min-w-full table-auto text-left border">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-4 py-2 border">Product</th>
                    <th class="px-4 py-2 border">Quantity</th>
                    <th class="px-4 py-2 border">Total (Ksh)</th>
                    <th class="px-4 py-2 border">Date</th>
                </tr>
            </thead>
            <tbody id="salesTable" class="text-gray-600">
                <tr>
                    <td colspan="4" class="text-center py-3">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>

</div>

<script>
    document.getElementById('saleForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch("{{ route('sales.store') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                const msg = document.getElementById('responseMsg');
                if (data.success) {
                    msg.innerText = "✅ " + data.message;
                    msg.classList.remove('text-red-600');
                    msg.classList.add('text-green-600');
                    loadSales(data.sales);
                    this.reset();
                } else {
                    msg.innerText = "❌ Something went wrong!";
                    msg.classList.add('text-red-600');
                }
            })
            .catch(err => {
                console.error(err);
                document.getElementById('responseMsg').innerText = "⚠️ Something went wrong, check console for details.";
            });
    });

    function loadSales(sales) {
        const table = document.getElementById('salesTable');
        table.innerHTML = "";
        if (sales.length === 0) {
            table.innerHTML = "<tr><td colspan='4' class='text-center py-3'>No sales recorded yet.</td></tr>";
            return;
        }

        sales.forEach(s => {
            let row = `<tr>
            <td class='border px-4 py-2'>${s.product?.pdt_name || 'N/A'}</td>
            <td class='border px-4 py-2'>${s.quantity}</td>
            <td class='border px-4 py-2'>Ksh ${s.totalAmount}</td>
            <td class='border px-4 py-2'>${new Date(s.date).toLocaleString()}</td>
        </tr>`;
            table.insertAdjacentHTML('beforeend', row);
        });
    }

    // Load initial sales
    fetch("{{ route('sales.dashboard.data') }}")
        .then(res => res.json())
        .then(data => loadSales(data.sales))
        .catch(err => console.error(err));
</script>
@endsection