@extends('layouts.salesanalyst')

@section('title', 'Sales Report')
@section('page-title', '📄 Sales Report')

@section('content')
<div class="bg-white p-6 rounded-lg shadow mb-8">

    <!-- Download Button -->
    <div class="flex justify-end mb-4">
        <a href="{{ route('sales.downloadReport') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">
            ⬇️ Download PDF
        </a>
    </div>

    <h2 class="text-lg font-semibold mb-4">
        Report Generated: <span id="generatedAt">{{ $generatedAt ?? now()->format('d M Y H:i') }}</span>
    </h2>

    <!-- Summary -->
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-gray-100 p-4 rounded-lg text-center">
            <h3 class="text-sm font-medium">Total Sales</h3>
            <p class="text-lg font-bold" id="totalSales">{{ $totalSales }}</p>
        </div>
        <div class="bg-gray-100 p-4 rounded-lg text-center">
            <h3 class="text-sm font-medium">Total Revenue</h3>
            <p class="text-lg font-bold" id="totalRevenue">Ksh {{ number_format($totalRevenue, 2) }}</p>
        </div>
        <div class="bg-gray-100 p-4 rounded-lg text-center">
            <h3 class="text-sm font-medium">Products Sold</h3>
            <p class="text-lg font-bold" id="totalProducts">{{ $totalProducts }}</p>
        </div>
    </div>

    <!-- Top Products Chart -->
    <div class="mb-6">
        <h3 class="text-md font-semibold mb-2">🔥 Top Performing Products</h3>
        <canvas id="topProductsChart" width="400" height="200"></canvas>
    </div>

    <!-- Detailed Sales Table -->
    <div>
        <h3 class="text-md font-semibold mb-2">🕒 Detailed Sales</h3>
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
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Initialize chart with current data
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
                backgroundColor: 'rgba(75, 192, 192, 0.7)',
                borderColor: 'rgba(75, 192, 192, 1)',
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

    // Function to refresh report data
    async function refreshReportData() {
        try {
            const res = await fetch("{{ route('sales.reports') }}");
            const html = await res.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, "text/html");

            // Update table
            const tableRows = doc.querySelectorAll('#salesTable tr');
            document.getElementById('salesTable').innerHTML = Array.from(tableRows).map(tr => tr.outerHTML).join('');

            // Update summary counts
            document.getElementById('totalSales').innerText = doc.getElementById('totalSales').innerText;
            document.getElementById('totalRevenue').innerText = doc.getElementById('totalRevenue').innerText;
            document.getElementById('totalProducts').innerText = doc.getElementById('totalProducts').innerText;
            document.getElementById('generatedAt').innerText = doc.getElementById('generatedAt').innerText;

            // Update top products chart
            const newTopProducts = @json($topProducts); // fallback
            topProductsChart.data.labels = newTopProducts.map(item => item.product?.pdt_name ?? 'Unknown');
            topProductsChart.data.datasets[0].data = newTopProducts.map(item => item.total_sold);
            topProductsChart.update();

        } catch (err) {
            console.error('Failed to refresh report:', err);
        }
    }

    // Listen to global "sale recorded" events from dashboard
    document.addEventListener('saleRecorded', refreshReportData);

</script>
@endsection
