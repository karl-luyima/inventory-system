<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sales Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1, h2 { text-align: center; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background: #f2f2f2; }
        .summary { margin-top: 20px; }
        .summary div { margin-bottom: 5px; }
    </style>
</head>
<body>
    <h1>Sales Report</h1>
    <h2>Generated: {{ $generatedAt }}</h2>

    <div class="summary">
        <div><strong>Total Sales:</strong> {{ $totalSales }}</div>
        <div><strong>Total Revenue:</strong> Ksh {{ number_format($totalRevenue, 2) }}</div>
        <div><strong>Products Sold:</strong> {{ $totalProducts }}</div>
    </div>

    <h3>Top Products</h3>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Units Sold</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($topProducts as $item)
            <tr>
                <td>{{ $item->product->pdt_name ?? 'Unknown' }}</td>
                <td>{{ $item->total_sold }}</td>
                <td>Ksh {{ number_format($item->total_amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

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
                <td>{{ $sale->created_at->format('d M Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
