<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use Carbon\Carbon;

class ForecastController extends Controller
{
    public function viewChart($pdtId)
    {
        $product = Product::find($pdtId);
        if (!$product) {
            return redirect()->back()->with('error', 'Product not found.');
        }

        $historicalSales = DB::table('sales')
            ->where('pdt_id', $pdtId)
            ->orderBy('date')
            ->get();

        $forecastData = DB::table('product_forecasts')
            ->where('pdt_id', $pdtId)
            ->orderBy('forecast_date')
            ->get();

        // Generate explanations per forecast row based on most recent historical sale
        $lastHistorical = $historicalSales->last()?->quantity ?? 0;
        foreach ($forecastData as $forecast) {
            $forecast->explanation_text = $this->generateRowExplanation($lastHistorical, $forecast);
            $lastHistorical = $forecast->predicted_sales; // update for next comparison
        }

        // Generate overall chart explanation dynamically
        $chartExplanation = $this->generateChartExplanation($historicalSales, $forecastData);

        return view('sales.forecast-chart', [
            'product' => $product,
            'historicalSales' => $historicalSales,
            'forecastData' => $forecastData,
            'chartExplanation' => $chartExplanation,
        ]);
    }

    private function generateRowExplanation($referenceValue, $forecast)
    {
        $forecastValue = $forecast->predicted_sales;

        if ($forecastValue > $referenceValue) {
            $trend = "Expected to rise above recent sales.";
        } elseif ($forecastValue < $referenceValue) {
            $trend = "Expected to fall below recent sales.";
        } else {
            $trend = "Expected to remain near recent sales.";
        }

        return $trend . " Based on past sales trends and seasonal effects.";
    }

    private function generateChartExplanation($historicalSales, $forecastData)
    {
        if ($historicalSales->isEmpty() && $forecastData->isEmpty()) {
            return "No sales or forecast data available.";
        }

        $avgHistorical = $historicalSales->avg('quantity');

        // Split forecast into halves for trend detection
        $half = (int) ceil($forecastData->count() / 2);
        $firstHalfAvg = $forecastData->take($half)->avg('predicted_sales');
        $secondHalfAvg = $forecastData->slice($half)->avg('predicted_sales');

        $trendParts = [];

        if ($firstHalfAvg > $avgHistorical) {
            $trendParts[] = "Sales are expected to rise initially.";
        } elseif ($firstHalfAvg < $avgHistorical) {
            $trendParts[] = "Sales may start below historical averages.";
        } else {
            $trendParts[] = "Sales start near historical averages.";
        }

        if ($secondHalfAvg > $firstHalfAvg) {
            $trendParts[] = "Trend shows an increase in later periods.";
        } elseif ($secondHalfAvg < $firstHalfAvg) {
            $trendParts[] = "Trend indicates a decrease in later periods.";
        } else {
            $trendParts[] = "Sales remain stable in later periods.";
        }

        $reason = "Forecasts consider historical trends and seasonal/promotional effects.";

        return implode(" ", $trendParts) . " " . $reason;
    }
}
