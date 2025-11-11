<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sales Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        h1, h2, h3 { text-align: center; margin-bottom: 10px; }
        h3 { text-align: left; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background: #f2f2f2; }
        .summary { margin-top: 20px; }
        .summary div { margin-bottom: 5px; }
        .card { display: inline-block; width: 30%; background: #f0f0f0; padding: 10px; margin-right: 1%; border-radius: 5px; text-align: center; }
        .card:last-child { margin-right: 0; }
        .card-title { font-weight: bold; color: #555; }
        .card-value { font-size: 16px; font-weight: bold; margin-top: 5px; }
    </style>
</head>
<body>

    <h1>Sales Report</h1>
    <h2>Generated: {{ $generatedAt }}</h2>

    <div class="summary">
        <div class="card">
            <div class="card-title">Total Sales</div>
            <div class="card-value">{{ $totalSales }}</div>
        </div>
        <div class="card">
            <div class="card-title">Total Revenue</div>
            <div class="card-value">Ksh {{ number_format($totalRevenue, 2) }}</div>
        </div>
        <div class="card">
            <div class="card-title">Products Sold</div>
            <div class="card-value">{{ $totalProducts }}</div>
        </div>
    </div>

    <h3>Detailed Sales</h3>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Amount</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sales as $sale)
            <tr>
                <td>{{ $sale->product->pdt_name ?? 'Unknown' }}</td>
                <td>{{ $sale->quantity }}</td>
                <td>Ksh {{ number_format($sale->totalAmount, 2) }}</td>
                <td>
                    @php
                        $saleDate = $sale->created_at ?? $sale->date ?? null;
                    @endphp
                    {{ $saleDate
                        ? \Carbon\Carbon::parse($saleDate)
                            ->timezone(config('app.timezone'))
                            ->format('d M Y H:i')
                        : 'N/A'
                    }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
