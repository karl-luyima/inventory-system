<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $report->name }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #333; }
        h1, h2 { text-align: center; color: #2c3e50; }
        .section { margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; }
        .summary { display: flex; justify-content: space-around; margin: 20px 0; }
        .box { padding: 10px 20px; border-radius: 6px; background: #f0f0f0; }
    </style>
</head>
<body>
    <h1>{{ $report->name }}</h1>
    <p><strong>Created by:</strong> {{ $report->creator_name ?? ucfirst($report->creator_type) }}</p>
    <p><strong>Date:</strong> {{ $report->created_at->format('d M Y, H:i') }}</p>

    <div class="summary">
        <div class="box"><strong>Total Users:</strong> {{ $data['total_users'] ?? 0 }}</div>
        <div class="box"><strong>Total Products:</strong> {{ $data['total_products'] ?? 0 }}</div>
        <div class="box"><strong>Active KPIs:</strong> {{ $data['active_kpis'] ?? 0 }}</div>
    </div>

    <div class="section">
        <h2>Top Products</h2>
        <table>
            <thead>
                <tr><th>Product</th><th>Sales</th></tr>
            </thead>
            <tbody>
                @forelse ($data['top_products'] ?? [] as $product)
                    <tr>
                        <td>{{ $product['pdt_name'] ?? 'N/A' }}</td>
                        <td>{{ $product['sales_sum_quantity'] ?? 0 }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2">No data available</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Low Stock Items</h2>
        <table>
            <thead>
                <tr><th>Item</th><th>Stock Level</th></tr>
            </thead>
            <tbody>
                @forelse ($data['low_stock_items'] ?? [] as $item)
                    <tr>
                        <td>{{ $item['name'] ?? 'N/A' }}</td>
                        <td>{{ $item['stock_level'] ?? 0 }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2">No low stock items found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
