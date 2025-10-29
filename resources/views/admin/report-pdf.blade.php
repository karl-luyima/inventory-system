<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $report->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            background: #f8f9fa;
        }
        h1, h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .header, .summary, .section {
            width: 90%;
            margin: 0 auto 20px auto;
            padding: 15px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .summary {
            display: flex;
            justify-content: space-around;
            text-align: center;
        }
        .summary .box {
            padding: 15px;
            background: #f1f5f9;
            border-radius: 8px;
            width: 30%;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px 8px;
            text-align: center;
        }
        th {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .section h2 {
            margin-bottom: 10px;
            color: #1f2937;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ $report->name }}</h1>
        <p style="text-align:center;"><strong>Date:</strong> {{ $report->created_at->format('d M Y, H:i') }}</p>
    </div>

    <div class="summary">
        <div class="box"><strong>Total Users:</strong><br>{{ $data['total_users'] ?? 0 }}</div>
        <div class="box"><strong>Total Products:</strong><br>{{ $data['total_products'] ?? 0 }}</div>
        <div class="box"><strong>Active KPIs:</strong><br>{{ $data['active_kpis'] ?? 0 }}</div>
    </div>

    <div class="section">
        <h2>Top Products</h2>
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
                @forelse ($data['top_products'] ?? [] as $product)
                    <tr>
                        <td>{{ $product['name'] ?? 'N/A' }}</td>
                        <td>{{ $product['quantity_sold'] ?? 0 }}</td>
                        <td>{{ number_format($product['unit_price'] ?? 0, 2) }}</td>
                        <td>{{ number_format($product['total_ksh'] ?? 0, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4">No top products available</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Low Stock Items</h2>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Stock Level</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data['low_stock_items'] ?? [] as $item)
                    <tr>
                        <td>{{ $item['name'] ?? 'N/A' }}</td>
                        <td>{{ $item['stock_level'] ?? 0 }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2">No low stock items found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

</body>
</html>
