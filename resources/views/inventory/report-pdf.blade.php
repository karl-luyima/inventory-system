<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Report PDF</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
        }
        h2, h3 {
            margin: 0;
            padding: 0;
        }
        h2 { font-size: 18px; margin-bottom: 10px; }
        h3 { font-size: 14px; margin-bottom: 8px; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #999;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #eee;
        }
        .summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .summary .card {
            flex: 1;
            background: #f8f8f8;
            padding: 10px;
            margin-right: 10px;
            text-align: center;
            border-radius: 4px;
        }
        .summary .card:last-child {
            margin-right: 0;
        }
        .color-box {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 2px;
        }
    </style>
</head>
<body>
    <h2>Inventory Clerk Report Summary</h2>
    <p>A consolidated overview of KPIs, total products, and inventories under your management.</p>

    <!-- Summary Cards -->
    <div class="summary">
        <div class="card">
            <strong>Total KPIs</strong><br>
            {{ $kpis->count() }}
        </div>
        <div class="card">
            <strong>Total Products</strong><br>
            {{ $products->count() }}
        </div>
        <div class="card">
            <strong>Total Inventories</strong><br>
            {{ $inventories->count() }}
        </div>
    </div>

    <!-- KPIs Table -->
    <h3>KPIs Overview</h3>
    <table>
        <thead>
            <tr>
                <th>KPI Title</th>
                <th>Current Value</th>
                <th>Color</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kpis as $kpi)
            <tr>
                <td>{{ $kpi->title }}</td>
                <td>{{ $kpi->value }}</td>
                <td>
                    <span class="color-box" style="background-color: {{ $kpi->color }}"></span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Products Table -->
    <h3>Products Overview</h3>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Inventory</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $prod)
            <tr>
                <td>{{ $prod->pdt_name }}</td>
                <td>Ksh {{ number_format($prod->price, 2) }}</td>
                <td>{{ $prod->stock_level }}</td>
                <td>{{ $prod->inventory->inventory_name ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Inventory Table -->
    <h3>Inventories Overview</h3>
    <table>
        <thead>
            <tr>
                <th>Inventory Name</th>
                <th>Products Count</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inventories as $inv)
            <tr>
                <td>{{ $inv->inventory_name }}</td>
                <td>{{ $inv->products->count() }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
