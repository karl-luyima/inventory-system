@extends('layouts.salesanalyst')

@section('title', 'Sales Management')
@section('page-title', '💰 Record Sales & View Insights')

@section('content')
<div class="max-w-4xl mx-auto space-y-16">

    {{-- 💾 Record New Sale --}}
    <div class="bg-white p-8 rounded-2xl shadow-md space-y-6">
        <h2 class="text-2xl font-semibold text-gray-800 border-b pb-3">🛒 Record a New Sale</h2>

        <form id="salesForm" class="space-y-6">
            @csrf
            <div class="space-y-2">
                <label for="pdt_name" class="block text-gray-700 font-semibold">Product Name</label>
                <input type="text" id="pdt_name" name="pdt_name"
                    placeholder="Enter product name"
                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                    required>
            </div>

            <div class="space-y-2">
                <label for="quantity" class="block text-gray-700 font-semibold">Quantity Sold</label>
                <input type="number" id="quantity" name="quantity"
                    placeholder="Enter quantity sold"
                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                    required>
            </div>

            <div class="space-y-2">
                <label for="totalAmount" class="block text-gray-700 font-semibold">Total Amount (Ksh)</label>
                <input type="number" id="totalAmount" name="totalAmount"
                    placeholder="Enter total amount"
                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                    required>
            </div>

            <div class="text-center mt-6">
                <button type="submit" id="saveBtn"
                    class="bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2 px-6 rounded-lg flex items-center justify-center mx-auto gap-2 transition duration-200">
                    <span>Save Sale</span> <i class="fas fa-save"></i>
                </button>
            </div>
        </form>
    </div>

    {{-- 🧾 Recent Sales Table --}}
    <div class="bg-white p-8 rounded-2xl shadow-md space-y-5">
        <h2 class="text-2xl font-semibold text-gray-800 border-b pb-3">📋 Recent Sales</h2>
        <div class="overflow-x-auto">
            <table class="w-full border border-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="border p-3 text-center">#</th>
                        <th class="border p-3 text-left">Product</th>
                        <th class="border p-3 text-center">Quantity</th>
                        <th class="border p-3 text-center">Total (Ksh)</th>
                        <th class="border p-3 text-center">Date</th>
                    </tr>
                </thead>
                <tbody id="salesTable">
                    <tr>
                        <td colspan="5" class="text-center p-4 text-gray-500">
                            Loading sales...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- 🔥 Top Performing Products Chart --}}
    <div class="bg-white p-8 rounded-2xl shadow-md space-y-5">
        <h2 class="text-2xl font-semibold text-gray-800 border-b pb-3">🔥 Top 5 Best-Selling Products</h2>
        <canvas id="topProductsChart" height="200"></canvas>
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
let topProductsChart = null;
const saveBtn = document.getElementById('saveBtn');
const originalBtnHTML = saveBtn.innerHTML;

// ================= Record Sale =================
document.getElementById('salesForm').addEventListener('submit', function(e) {
    e.preventDefault();

    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

    const formData = {
        _token: "{{ csrf_token() }}",
        pdt_name: document.getElementById('pdt_name').value,
        quantity: parseInt(document.getElementById('quantity').value),
        totalAmount: parseFloat(document.getElementById('totalAmount').value)
    };

    fetch("{{ route('sales.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(res => res.json())
        .then(data => {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalBtnHTML;

            if (!data.success) {
                alert('❌ ' + (data.message || 'Failed to save sale.'));
                return;
            }

            alert('✅ Sale recorded successfully!');
            updateSalesTable(data.sales);
            updateTopProductsChart(data.topProducts);
            document.getElementById('salesForm').reset();
        })
        .catch(err => {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalBtnHTML;
            console.error('Error:', err);
            alert('⚠️ Something went wrong. Check console for details.');
        });
});

// ================= Update Recent Sales Table =================
function updateSalesTable(sales) {
    const table = document.getElementById('salesTable');
    table.innerHTML = '';

    if (!sales || sales.length === 0) {
        table.innerHTML = '<tr><td colspan="5" class="text-center p-4 text-gray-500">No sales recorded yet.</td></tr>';
        return;
    }

    sales.forEach((sale, index) => {
        const productName = sale.product?.pdt_name || sale.pdt_name || 'N/A';
        const saleDate = new Date(sale.date || sale.created_at)
            .toLocaleString('en-KE', { timeZone: 'Africa/Nairobi' });
        const row = `
        <tr class="border-t hover:bg-gray-50">
            <td class="border p-3 text-center">${index + 1}</td>
            <td class="border p-3">${productName}</td>
            <td class="border p-3 text-center">${sale.quantity}</td>
            <td class="border p-3 text-center">${sale.totalAmount}</td>
            <td class="border p-3 text-center">${saleDate}</td>
        </tr>
        `;
        table.innerHTML += row;
    });
}

// ================= Update Top Products Chart =================
function updateTopProductsChart(topProducts) {
    if (!topProducts || topProducts.length === 0) return;

    const labels = topProducts.map(p => p.pdt_name || 'Unknown');
    const values = topProducts.map(p => p.total_sold || 0);

    if (topProductsChart) topProductsChart.destroy();

    const ctx = document.getElementById('topProductsChart').getContext('2d');
    topProductsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Units Sold',
                data: values,
                backgroundColor: 'rgba(255, 165, 0, 0.7)',
                borderColor: 'rgba(255, 140, 0, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Quantity Sold'
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Top 5 Products by Quantity Sold'
                },
                legend: {
                    display: false
                }
            }
        }
    });
}

// ================= Load initial sales & chart =================
document.addEventListener('DOMContentLoaded', () => {
    fetch("{{ route('sales.data') }}")
        .then(res => res.json())
        .then(data => {
            updateSalesTable(data.sales);
            updateTopProductsChart(data.topProducts);
        })
        .catch(err => console.error('Failed to load initial sales data:', err));
});
</script>
@endsection
