@extends('layouts.admin')

@section('title', 'Sales Dashboard')
@section('page-title', '💰 Top Performing Products')

@section('content')
<div class="max-w-5xl mx-auto space-y-8">

    {{-- Top Products Chart --}}
    <div class="bg-white p-6 rounded-xl shadow-md">
    
        <canvas id="topProductsChart" class="w-full h-80"></canvas>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let chart;

    function fetchTopProducts() {
        fetch("{{ route('admin.sales.data') }}")
            .then(res => res.json())
            .then(data => {
                const labels = data.map(item => item.pdt_name);
                const values = data.map(item => item.total_qty);

                if (chart) {
                    chart.destroy(); // refresh chart for real-time
                }

                const ctx = document.getElementById('topProductsChart').getContext('2d');
                chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Quantity Sold',
                            data: values,
                            backgroundColor: [
                                '#3b82f6',
                                '#10b981',
                                '#f59e0b',
                                '#ef4444',
                                '#8b5cf6'
                            ],
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: ctx => `${ctx.formattedValue} units`
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 }
                            }
                        }
                    }
                });
            })
            .catch(err => console.error("Error:", err));
    }

    fetchTopProducts();
    setInterval(fetchTopProducts, 10000); // refresh every 10s
</script>
@endsection
