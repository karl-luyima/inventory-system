<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Report Summary</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        h2 { text-align: center; margin-bottom: 20px; }
        ul, ol { margin-left: 20px; }
    </style>
</head>
<body>
    <h2>Admin Report Summary</h2>

    <h4>System Overview</h4>
    <ul>
        <li><strong>Inventory items:</strong> {{ $summaryData['inventory'] }}</li>
        <li><strong>Registered users:</strong> {{ $summaryData['users'] }}</li>
        <li><strong>Total KPIs:</strong> {{ count($summaryData['kpis']) }}</li>
    </ul>

    <h4>Top Products</h4>
    <ol>
        @foreach($summaryData['topProducts'] as $product)
            <li>{{ $product->name }} — {{ $product->sales }} sales</li>
        @endforeach
    </ol>
</body>
</html>
