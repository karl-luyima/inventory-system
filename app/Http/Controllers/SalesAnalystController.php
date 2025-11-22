<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Process\Process; // <-- ADDED: Needed for running the Python script

class SalesAnalystController extends Controller
{
    // ================= CONFIGURATION =================
    // IMPORTANT: Keep these properties at the top of the class
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
        // ... inside store(Request $request)
        $request->validate([
            'pdt_name'      => 'required|string|max:255',
            'quantity'      => 'required|integer|min:1',
            'totalAmount'   => 'required|numeric|min:0',
            // 'inventory_id'  => 'required|exists:inventories,inventory_id', // <-- REMOVED THIS LINE
        ]);

        try {
            
            $product = Product::firstOrCreate(
                ['pdt_name' => $request->pdt_name],
                [
                    'price'         => $request->price ?? 0,
                    'stock_level'   => $request->stock_level ?? 0,
                    
                    'inventory_id'  => $request->inventory_id ?? 1, 
                ]
            );


            // Stock validation
            if ($product->stock_level < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough stock available for this sale.'
                ], 400);
            }

            // Deduct stock
            $product->stock_level -= $request->quantity;
            $product->save();

            // Save sale
            Sale::create([
                'pdt_id'        => $product->pdt_id,
                'quantity'      => $request->quantity,
                'totalAmount'   => $request->totalAmount,
                'date'          => now(),
            ]);

            // Fetch updated sales for dashboard
            $sales = Sale::with('product')
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();

            // Fetch updated top products
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

            // Return JSON that the JS expects
            return response()->json([
                'success'       => true,
                'message'       => 'Sale recorded successfully!',
                'sales'         => $sales,
                'topProducts'   => $topProducts,
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
        $sales = Sale::with('product')->latest()->get();

        $totalSales = $sales->count();
        $totalRevenue = $sales->sum('totalAmount');
        $totalProducts = $sales->sum('quantity');

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

    // ================= Download Report PDF =================
    public function downloadReport()
    {
        $sales = Sale::with('product')->latest()->get();

        $topProducts = Product::withSum('sales', 'quantity')
            ->withSum('sales', 'totalAmount')
            ->orderByDesc('sales_sum_quantity')
            ->take(5)
            ->get()
            ->filter(fn($product) => $product->sales_sum_quantity > 0)
            ->map(fn($product) => (object)[
                'pdt_name'      => $product->pdt_name,
                'total_sold'    => $product->sales_sum_quantity ?? 0,
                'total_amount'  => $product->{"sales_sum_total_amount"} ?? 0
            ]);

        $totalSales = $sales->count();
        $totalRevenue = $sales->sum('totalAmount');
        $totalProducts = $sales->sum('quantity');
        $generatedAt = now()->format('d M Y H:i');

        $pdf = Pdf::loadView('sales.report_pdf', compact(
            'sales',
            'topProducts',
            'totalSales',
            'totalRevenue',
            'totalProducts',
            'generatedAt'
        ));

        return $pdf->download('sales_report_' . now()->format('Ymd_His') . '.pdf');
    }

    // ================= Fetch Sales Data for JS (initial load) =================
    public function fetchSalesData()
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
                'pdt_name'      => $product->pdt_name,
                'total_sold'    => $product->sales_sum_quantity ?? 0,
                'total_amount'  => $product->{"sales_sum_total_amount"} ?? 0
            ]);

        return response()->json([
            'sales'         => $sales,
            'topProducts'   => $topProducts
        ]);
    }

    // ================= Manual Forecast (Legacy/API) =================
    public function showForecastForm()
    {
        return view('sales.forecast-form'); // a Blade form with input fields
    }

    // Send data to Python API and return predictions
    public function forecast(Request $request)
    {
        $data = $request->only(['feature1', 'feature2', 'feature3']); // adjust features

        // Call Python FastAPI
        $response = Http::post('http://127.0.0.1:8000/predict', $data);

        if ($response->failed()) {
            return redirect()->back()->with('error', 'Prediction API is unavailable.');
        }

        $prediction = $response->json()['prediction'];
        $explanation = $response->json()['explanation'];

        return view('sales.forecast-result', compact('prediction', 'explanation'));
    }

    // ================= SALES FORECAST: DISPLAY (Products) =================
    /**
     * Fetches and groups the product demand forecasts for display.
     */
    public function showForecast()
    {
        // 1. Fetch the latest forecasts from the database
        $forecasts = DB::table('product_forecasts AS pf')
            ->select(
                'pf.pdt_id',
                'p.pdt_name',
                'pf.forecast_date',
                'pf.predicted_sales',
                'pf.explanation_json' // <-- ADDED: Retrieve XAI explanation data
            )
            ->join('products AS p', 'pf.pdt_id', '=', 'p.pdt_id')
            ->orderBy('p.pdt_name')
            ->orderBy('pf.forecast_date', 'ASC')
            ->get();

        // 2. Group the results by product name for easy display in the view
        $groupedForecasts = $forecasts->groupBy('pdt_name');

        // 👇 CORRECTED VIEW NAME: Using 'sales.forecast' which resolves to 'resources/views/sales/forecast.blade.php'
        return view('sales.forecast', [
            'groupedForecasts' => $groupedForecasts,
        ]);
    }

    // ================= SALES FORECAST: GENERATE (Run Python) =================
    /**
     * Executes the Python script to generate new product demand forecasts.
     */
    public function generateForecast()
    {
        // Check if the file exists before running
        if (!file_exists($this->scriptPath)) {
            return redirect()->back()->with('error', 'Forecast script not found. Path: ' . $this->scriptPath);
        }

        try {
            // Use Symfony Process component to execute the script
            $process = new Process([$this->pythonExecutable, $this->scriptPath]);
            $process->setTimeout(300); // Allow up to 5 minutes
            $process->run();

            // Check if the command was successful
            if (!$process->isSuccessful()) {
                // Throw exception with detailed error from Python output
                throw new \RuntimeException("Python script failed: " . $process->getErrorOutput());
            }

            // Success: Redirect to the forecast viewing page
            return redirect()->route('sales.show_forecast')->with('success', 'Product Demand Forecast successfully regenerated!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Forecast generation failed: ' . $e->getMessage());
        }
    }
}
