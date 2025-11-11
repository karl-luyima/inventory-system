@extends('layouts.pdf')

@section('content')
<div style="font-family: sans-serif; padding: 20px;">

    <h1 style="text-align: center; font-size: 24px; margin-bottom: 20px;">
        {{ $report->name }}
    </h1>

    <p><strong>Created On:</strong> {{ $report->created_at->format('d M Y, H:i') }}</p>

    <hr style="margin: 20px 0;">

    <h2 style="font-size: 18px; margin-bottom: 10px;">Summary Metrics</h2>
    <ul style="list-style-type: none; padding: 0; line-height: 1.6;">
        <li><strong>Total Users:</strong> {{ $data['total_users'] }}</li>
        <li><strong>Total Products:</strong> {{ $data['total_products'] }}</li>
        <li><strong>Active KPIs:</strong> {{ $data['active_kpis'] }}</li>
    </ul>

    <hr style="margin: 20px 0;">

    <h2 style="font-size: 18px; margin-bottom: 10px;">Top Products</h2>
    @if (!empty($data['top_products']))
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr>
                    <th style="border: 1px solid #ddd; padding: 8px;">#</th>
                    <th style="border: 1px solid #ddd; padding: 8px;">Product Name</th>
                    <th style="border: 1px solid #ddd; padding: 8px;">Unit Price (KSh)</th>
                    <th style="border: 1px solid #ddd; padding: 8px;">Quantity Sold</th>
                    <th style="border: 1px solid #ddd; padding: 8px;">Total Revenue (KSh)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data['top_products'] as $index => $product)
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $index + 1 }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $product->name }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ number_format($product->unit_price, 2) }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $product->quantity_sold }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ number_format($product->total_ksh, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No sales data available for top products.</p>
    @endif

    <hr style="margin: 20px 0;">

    <h2 style="font-size: 18px; margin-bottom: 10px;">Low Stock Items</h2>
    @if (!empty($data['low_stock_items']))
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr>
                    <th style="border: 1px solid #ddd; padding: 8px;">#</th>
                    <th style="border: 1px solid #ddd; padding: 8px;">Product Name</th>
                    <th style="border: 1px solid #ddd; padding: 8px;">Stock Level</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data['low_stock_items'] as $index => $item)
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $index + 1 }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $item->name }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $item->stock_level }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No low stock items found.</p>
    @endif

</div>
@endsection
