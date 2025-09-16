@extends('layouts.salesanalyst')

@section('title', 'Sales Analyst Dashboard')
@section('page-title', '📊 Sales Analyst Dashboard')

@section('content')
<!-- Header -->
<div class="flex justify-between items-center mb-6">
    <button id="generateReportBtn" 
        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow">
        📄 Generate Top Products Report
    </button>
    <a href="{{ route('sales.downloadReport') }}" 
       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow">
       ⬇ Download PDF Report
    </a>
</div>

<!-- Record New Sale -->
<div class="bg-white p-6 rounded-lg shadow mb-8">
    <h2 class="text-lg font-semibold mb-4">➕ Record New Sale</h2>
    <form id="saleForm" class="space-y-4">
        @csrf
        <div>
            <label for="pdt_id" class="block text-sm font-medium text-gray-700">Product</label>
            <select name="pdt_id" id="pdt_id"
                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm">
                <option value="">-- Select Product --</option>
                @foreach(\App\Models\Product::all() as $product)
                    <option value="{{ $product->pdt_id }}">{{ $product->pdt_name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
            <input type="number" name="quantity" id="quantity" min="1"
                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm">
        </div>

        <div>
            <label for="totalAmount" class="block text-sm font-medium text-gray-700">Amount</label>
            <input type="number" step="0.01" name="totalAmount" id="totalAmount" min="0"
                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm">
        </div>

        <button type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">
            💾 Save Sale
        </button>
    </form>
    <p id="saleMessage" class="text-green-600 font-medium mt-3 hidden"></p>
</div>

<!-- Top Performing Products -->
<div class="bg-white p-6 rounded-lg shadow mb-8">
    <h2 class="text-lg font-semibold mb-4">🔥 Top Performing Products</h2>
    <canvas id="topProductsChart" width="400" height="150"></canvas>
</div>

<!-- Latest Sales -->
<div class="bg-white p-6 rounded-lg shadow">
    <h2 class="text-lg font-semibold mb-4">🕒 Latest Sales</h2>
    <table class="min-w-full border border-gray-200 rounded-lg overflow-x-auto">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Product</th>
                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Quantity</th>
                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Amount</th>
                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Date</th>
            </tr>
        </thead>
        <tbody id="salesTable" class="divide-y divide-gray-200">
            @foreach ($sales as $sale)
                <tr>
                    <td class="px-4 py-2">{{ $sale->product->pdt_name ?? 'Unknown' }}</td>
                    <td class="px-4 py-2">{{ $sale->quantity }}</td>
                    <td class="px-4 py-2">Ksh {{ number_format($sale->totalAmount, 2) }}</td>
                    <td class="px-4 py-2">{{ $sale->created_at->format('d M Y H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Prepare chart with initial top products
    const topProducts = @json($topProducts);
    const chartLabels = topProducts.map(item => item.product?.pdt_name ?? 'Unknown');
    const chartData = topProducts.map(item => item.total_sold);

    const ctx = document.getElementById('topProductsChart').getContext('2d');
    const topProductsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Units Sold',
                data: chartData,
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: { display: true, text: 'Top 5 Products by Units Sold' }
            },
            scales: { y: { beginAtZero: true } }
        }
    });

    // Record Sale
    document.getElementById('saleForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);

        fetch("{{ route('sales.store') }}", {
            method: "POST",
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                const msg = document.getElementById('saleMessage');
                msg.innerText = data.message;
                msg.classList.remove('hidden');
                setTimeout(() => { msg.classList.add('hidden'); }, 3000);

                this.reset();

                // Update Latest Sales table
                const salesHTML = data.sales.map(sale => `
                    <tr>
                        <td class="px-4 py-2">${sale.product?.pdt_name ?? 'Unknown'}</td>
                        <td class="px-4 py-2">${sale.quantity}</td>
                        <td class="px-4 py-2">Ksh ${parseFloat(sale.totalAmount).toFixed(2)}</td>
                        <td class="px-4 py-2">${new Date(sale.created_at).toLocaleString()}</td>
                    </tr>`).join('');
                document.getElementById('salesTable').innerHTML = salesHTML;

                // Update Chart
                topProductsChart.data.labels = data.topProducts.map(item => item.product?.pdt_name ?? 'Unknown');
                topProductsChart.data.datasets[0].data = data.topProducts.map(item => item.total_sold);
                topProductsChart.update();
            }
        })
        .catch(err => console.error(err));
    });

    // Generate Top Products Report
    document.getElementById('generateReportBtn').addEventListener('click', function() {
        const data = Array.from(document.querySelectorAll('#topProductsChart')).map(li => ({
            product: li.innerText,
            total_sold: parseInt(li.innerText)
        }));

        fetch("{{ route('sales.generateReport') }}", {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ topProducts: data })
        })
        .then(res => res.json())
        .then(data => { if(data.success) alert(data.message); })
        .catch(err => console.error(err));
    });
</script>
@endsection
