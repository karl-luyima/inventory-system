<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $report->name }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #333; }
        h1, h2, h3 { color: #2c3e50; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f7f7f7; }
        ul { list-style: none; padding: 0; }
        .section { margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>{{ $report->name }}</h1>
    <p><strong>Date:</strong> {{ $report->created_at->format('d M Y, H:i A') }}</p>
    <hr>

    <div class="section">
        <h3>📊 Summary</h3>
        <p><strong>Total Users:</strong> {{ $data['total_users'] }}</p>
        <p><strong>Active KPIs:</strong> {{ $data['active_kpis'] }}</p>
        <p><strong>Total Products:</strong> {{ $data['total_products'] }}</p>
    </div>

    <div class="section">
        <h3>⚠️ Low Stock Items</h3>
        <ul>
            @forelse ($data['low_stock_items'] as $item)
                <li>{{ $item['name'] }} — {{ $item['stock_level'] }} left</li>
            @empty
                <li>No low stock items.</li>
            @endforelse
        </ul>
    </div>

    <div class="section">
        <h3>🔥 Top 5 Products</h3>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity Sold</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data['top_products'] as $p)
                    <tr>
                        <td>{{ $p['pdt_name'] }}</td>
                        <td>{{ $p['sales_sum_quantity'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
