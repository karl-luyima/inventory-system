<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Sales Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        h1,
        h2 {
            text-align: center;
            margin-bottom: 10px;
        }

        h3 {
            text-align: left;
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            page-break-inside: auto;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }

        th {
            background: #f2f2f2;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        .summary {
            margin-top: 20px;
            overflow: hidden;
        }

        .card {
            float: left;
            width: 32%;
            background: #f0f0f0;
            padding: 10px;
            margin-right: 1%;
            border-radius: 5px;
            text-align: center;
        }

        .card:last-child {
            margin-right: 0;
        }

        .card-title {
            font-weight: bold;
            color: #555;
        }

        .card-value {
            font-size: 16px;
            font-weight: bold;
            margin-top: 5px;
        }

        .page-break {
            page-break-after: always;
        }
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
    <div style="clear: both;"></div>

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
                    {{ $sale->date ? \Carbon\Carbon::parse($sale->date)->format('d M Y H:i') : 'N/A' }}
                </td>
            </tr>

            @if($loop->iteration % 25 == 0)
        </tbody>
    </table>
    <div class="page-break"></div>
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
            @endif
            @endforeach
        </tbody>
    </table>

</body>

</html>