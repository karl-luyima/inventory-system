<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Process\Process;

class SalesAnalystController extends Controller
{
    protected $pythonExecutable = 'C:\Users\Luyima Karl\AppData\Local\Programs\Python\Python313\python.exe';
    protected $scriptPath = 'C:\Users\Luyima Karl\Desktop\inventory-system\resources\views\sales\forecast-product-demand.py';

    // ================= Dashboard =================
    public function dashboard()
    {
        $sales = Sale::with('product')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $topProducts = Product::withSum('sales', 'quantity')
            ->withSum('sales', 'totalAmount')
            ->orderByDesc('sales_sum_quantity')
            ->take(5)
            ->get()
            ->map(fn($product) => (object)[
                'pdt_name' => $product->pdt_name,
                'total_sold' => $product->sales_sum_quantity ?? 0,
                'total_amount' => $product->{"sales_sum_total_amount"} ?? 0
            ]);

        return view('sales.dashboard', compact('sales', 'topProducts'));
    }

    // ================= Record a Sale =================
    public function store(Request $request)
    {
        $request->validate([
            'pdt_name'      => 'required|string|max:255',
            'quantity'      => 'required|integer|min:1',
            'totalAmount'   => 'required|numeric|min:0',
        ]);

        try {
            $product = Product::firstOrCreate(
                ['pdt_name' => $request->pdt_name],
                [
                    'price'       => $request->price ?? 0,
                    'stock_level' => $request->stock_level ?? 0,
                    'inventory_id' => $request->inventory_id ?? 1,
                ]
            );

            if ($product->stock_level < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough stock available for this sale.'
                ], 400);
            }

            $product->stock_level -= $request->quantity;
            $product->save();

            Sale::create([
                'pdt_id'      => $product->pdt_id,
                'quantity'    => $request->quantity,
                'totalAmount' => $request->totalAmount,
                'date'        => now(),
            ]);

            $sales = Sale::with('product')->orderBy('created_at', 'desc')->take(10)->get();

            $topProducts = Product::withSum('sales', 'quantity')
                ->withSum('sales', 'totalAmount')
                ->orderByDesc('sales_sum_quantity')
                ->take(5)
                ->get()
                ->map(fn($product) => (object)[
                    'pdt_name'      => $product->pdt_name,
                    'total_sold'    => $product->sales_sum_quantity ?? 0,
                    'total_amount'  => $product->{"sales_sum_total_amount"} ?? 0
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Sale recorded successfully!',
                'sales' => $sales,
                'topProducts' => $topProducts,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving sale: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ================= Reports =================
    public function reports()
    {
        $sales = Sale::with('product')->latest()->paginate(50); // Pagination for view

        $totalSales = Sale::count();
        $totalRevenue = Sale::sum('totalAmount');
        $totalProducts = Sale::sum('quantity');

        $topProducts = Product::withSum('sales', 'quantity')
            ->orderByDesc('sales_sum_quantity')
            ->take(5)
            ->get()
            ->map(fn($product) => (object)[
                'pdt_name' => $product->pdt_name,
                'total_sold' => $product->sales_sum_quantity ?? 0
            ]);

        return view(
            'sales.reports',
            compact('sales', 'totalSales', 'totalRevenue', 'totalProducts', 'topProducts')
        );
    }

    public function downloadReport()
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300); // 5 minutes

        try {
            // Fetch sales and related products
            $sales = Sale::with('product')->latest()->get();

            // Top 5 products summary
            $topProducts = Product::withSum('sales', 'quantity')
                ->withSum('sales', 'totalAmount')
                ->orderByDesc('sales_sum_quantity')
                ->take(5)
                ->get()
                ->map(fn($product) => (object)[
                    'pdt_name' => $product->pdt_name ?? 'Unknown',
                    'total_sold' => $product->sales_sum_quantity ?? 0,
                    'total_amount' => $product->{"sales_sum_total_amount"} ?? 0
                ]);

            
            $totalSales = $sales->count();
            $totalRevenue = $sales->sum('totalAmount');
            $totalProducts = $sales->sum('quantity');
            $generatedAt = now()->format('d M Y H:i');

            
            $pdf = Pdf::loadView('sales.report_pdf', [
                'sales' => $sales,
                'topProducts' => $topProducts,
                'totalSales' => $totalSales,
                'totalRevenue' => $totalRevenue,
                'totalProducts' => $totalProducts,
                'generatedAt' => $generatedAt,
            ]);

            return $pdf->stream('sales_report_' . now()->format('Ymd_His') . '.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'PDF generation failed: ' . $e->getMessage());
        }
    }


    // ================= Fetch Sales Data =================
    public function fetchSalesData()
    {
        $sales = Sale::with('product')->orderBy('created_at', 'desc')->take(10)->get();

        $topProducts = Product::withSum('sales', 'quantity')
            ->withSum('sales', 'totalAmount')
            ->orderByDesc('sales_sum_quantity')
            ->take(5)
            ->get()
            ->map(fn($product) => (object)[
                'pdt_name' => $product->pdt_name,
                'total_sold' => $product->sales_sum_quantity ?? 0,
                'total_amount' => $product->{"sales_sum_total_amount"} ?? 0
            ]);

        return response()->json([
            'sales' => $sales,
            'topProducts' => $topProducts
        ]);
    }

    // ================= Forecast Form & API =================
    public function showForecastForm()
    {
        return view('sales.forecast-form');
    }

    public function forecast(Request $request)
    {
        $data = $request->only(['feature1', 'feature2', 'feature3']);

        $response = Http::post('http://127.0.0.1:8000/predict', $data);

        if ($response->failed()) {
            return redirect()->back()->with('error', 'Prediction API is unavailable.');
        }

        $prediction = $response->json()['prediction'];
        $explanation = $response->json()['explanation'];

        return view('sales.forecast-result', compact('prediction', 'explanation'));
    }

    // ================= Forecast Display =================
    public function showForecast()
    {
        $forecasts = DB::table('product_forecasts AS pf')
            ->select('pf.pdt_id', 'p.pdt_name', 'pf.forecast_date', 'pf.predicted_sales', 'pf.explanation_json')
            ->join('products AS p', 'pf.pdt_id', '=', 'p.pdt_id')
            ->orderBy('p.pdt_name')
            ->orderBy('pf.forecast_date', 'ASC')
            ->get();

        $groupedForecasts = $forecasts->groupBy('pdt_name');

        return view('sales.forecast', compact('groupedForecasts'));
    }

    // ================= Generate Forecast via Python =================
    public function generateForecast()
    {
        if (!file_exists($this->scriptPath)) {
            return redirect()->back()->with('error', 'Forecast script not found: ' . $this->scriptPath);
        }

        try {
            $process = new Process([$this->pythonExecutable, $this->scriptPath]);
            $process->setTimeout(300);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new \RuntimeException("Python script failed: " . $process->getErrorOutput());
            }

            return redirect()->route('sales.show_forecast')->with('success', 'Product Demand Forecast successfully regenerated!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Forecast generation failed: ' . $e->getMessage());
        }
    }
}
