<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $report->name }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        h1, h2, h3 { color: #2c3e50; margin-bottom: 5px; }
        hr { border: none; border-top: 1px solid #ccc; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; font-size: 12px; }
        th { background: #f7f7f7; }
        ul { list-style: none; padding: 0; }
        li { margin-bottom: 4px; }
        .section { margin-bottom: 15px; }
        .text-center { text-align: center; }
        .low-stock { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h1>{{ $report->name }}</h1>
    <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($report->created_at)->format('d M Y, H:i A') }}</p>
    <hr>

    <div class="section">
        <h3>Summary</h3>
        <p><strong>Total Users:</strong> {{ $data['total_users'] ?? 0 }}</p>
        <p><strong>Active KPIs:</strong> {{ $data['active_kpis'] ?? 0 }}</p>
        <p><strong>Total Products:</strong> {{ $data['total_products'] ?? 0 }}</p>
    </div>

    <div class="section">
        <h3>Low Stock Items</h3>
        <ul>
            @forelse ($data['low_stock_items'] ?? [] as $item)
                <li class="low-stock">{{ $item['name'] ?? 'Unknown' }} — {{ $item['stock_level'] ?? 0 }} left</li>
            @empty
                <li>No low stock items.</li>
            @endforelse
        </ul>
    </div>

    <div class="section">
        <h3>Top 5 Products</h3>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity Sold</th>
                    <th>Unit Price (Ksh)</th>
                    <th>Total Sales (Ksh)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data['top_products'] ?? [] as $p)
                    <tr>
                        <td>{{ $p['name'] ?? 'N/A' }}</td>
                        <td>{{ $p['quantity_sold'] ?? 0 }}</td>
                        <td>{{ number_format($p['unit_price'] ?? 0, 2) }}</td>
                        <td>{{ number_format($p['total_ksh'] ?? 0, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No top products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
