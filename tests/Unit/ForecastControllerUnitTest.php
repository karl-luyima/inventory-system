<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ForecastControllerUnitTest extends TestCase
{
    use RefreshDatabase;

    protected $product;
    protected $inventory;

    protected function setUp(): void
    {
        parent::setUp();

        // Set a consistent date for testing
        Carbon::setTestNow('2024-01-15');

        // Create an inventory
        $this->inventory = DB::table('inventories')->insertGetId([
            'inventory_name' => 'Main Inventory',
            'pdtList' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create a product
        $this->product = Product::create([
            'pdt_name' => 'Test Product',
            'price' => 100,
            'stock_level' => 50,
            'inventory_id' => $this->inventory,
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /** @test */
    public function forecast_chart_displays()
    {
        DB::table('sales')->insert([
            ['pdt_id' => $this->product->pdt_id, 'date' => '2024-01-01', 'quantity' => 100, 'totalAmount' => 1000],
            ['pdt_id' => $this->product->pdt_id, 'date' => '2024-01-05', 'quantity' => 150, 'totalAmount' => 1500],
        ]);

        DB::table('product_forecasts')->insert([
            ['pdt_id' => $this->product->pdt_id, 'forecast_date' => '2024-01-16', 'predicted_sales' => 130],
            ['pdt_id' => $this->product->pdt_id, 'forecast_date' => '2024-01-20', 'predicted_sales' => 140],
        ]);

        $response = $this->get("/sales/forecast/chart/{$this->product->pdt_id}");

        $response->assertStatus(200);
        $response->assertSee('Forecast Chart');
        $response->assertSee('Visualizing future predicted sales');
    }

    /** @test */
    public function redirects_when_product_not_found()
    {
        $response = $this->get('/sales/forecast/chart/999');

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'Product not found.');
    }

    /** @test */
    public function historical_sales_are_ordered_by_date()
    {
        DB::table('sales')->insert([
            ['pdt_id' => $this->product->pdt_id, 'date' => '2024-01-10', 'quantity' => 120, 'totalAmount' => 1200],
            ['pdt_id' => $this->product->pdt_id, 'date' => '2024-01-01', 'quantity' => 100, 'totalAmount' => 1000],
            ['pdt_id' => $this->product->pdt_id, 'date' => '2024-01-05', 'quantity' => 150, 'totalAmount' => 1500],
        ]);

        $response = $this->get("/sales/forecast/chart/{$this->product->pdt_id}");

        $response->assertStatus(200);
        $historicalSales = $response->viewData('historicalSales');

        $this->assertEquals(100, $historicalSales[0]->quantity);
        $this->assertEquals(150, $historicalSales[1]->quantity);
        $this->assertEquals(120, $historicalSales[2]->quantity);
    }

    /** @test */
    public function forecast_data_is_ordered_by_forecast_date()
    {
        DB::table('product_forecasts')->insert([
            ['pdt_id' => $this->product->pdt_id, 'forecast_date' => '2024-01-25', 'predicted_sales' => 140],
            ['pdt_id' => $this->product->pdt_id, 'forecast_date' => '2024-01-16', 'predicted_sales' => 130],
            ['pdt_id' => $this->product->pdt_id, 'forecast_date' => '2024-01-20', 'predicted_sales' => 135],
        ]);

        $response = $this->get("/sales/forecast/chart/{$this->product->pdt_id}");

        $response->assertStatus(200);
        $forecastData = $response->viewData('forecastData');

        $this->assertEquals(130, $forecastData[0]->predicted_sales);
        $this->assertEquals(135, $forecastData[1]->predicted_sales);
        $this->assertEquals(140, $forecastData[2]->predicted_sales);
    }

    /** @test */
    public function generates_rising_forecast_explanation()
    {
        DB::table('sales')->insert([
            ['pdt_id' => $this->product->pdt_id, 'date' => '2024-01-10', 'quantity' => 100, 'totalAmount' => 1000],
        ]);

        DB::table('product_forecasts')->insert([
            ['pdt_id' => $this->product->pdt_id, 'forecast_date' => '2024-01-16', 'predicted_sales' => 150],
        ]);

        $response = $this->get("/sales/forecast/chart/{$this->product->pdt_id}");

        $response->assertStatus(200);
        $forecastData = $response->viewData('forecastData');
        
        $this->assertStringContainsString('Expected to rise above recent sales', $forecastData[0]->explanation_text);
    }

    /** @test */
    public function generates_falling_forecast_explanation()
    {
        DB::table('sales')->insert([
            ['pdt_id' => $this->product->pdt_id, 'date' => '2024-01-10', 'quantity' => 200, 'totalAmount' => 2000],
        ]);

        DB::table('product_forecasts')->insert([
            ['pdt_id' => $this->product->pdt_id, 'forecast_date' => '2024-01-16', 'predicted_sales' => 100],
        ]);

        $response = $this->get("/sales/forecast/chart/{$this->product->pdt_id}");

        $response->assertStatus(200);
        $forecastData = $response->viewData('forecastData');
        
        $this->assertStringContainsString('Expected to fall below recent sales', $forecastData[0]->explanation_text);
    }

    /** @test */
    public function generates_stable_forecast_explanation()
    {
        DB::table('sales')->insert([
            ['pdt_id' => $this->product->pdt_id, 'date' => '2024-01-10', 'quantity' => 150, 'totalAmount' => 1500],
        ]);

        DB::table('product_forecasts')->insert([
            ['pdt_id' => $this->product->pdt_id, 'forecast_date' => '2024-01-16', 'predicted_sales' => 150],
        ]);

        $response = $this->get("/sales/forecast/chart/{$this->product->pdt_id}");

        $response->assertStatus(200);
        $forecastData = $response->viewData('forecastData');
        
        $this->assertStringContainsString('Expected to remain near recent sales', $forecastData[0]->explanation_text);
    }

    /** @test */
    public function chart_explanation_when_no_data_exists()
    {
        $response = $this->get("/sales/forecast/chart/{$this->product->pdt_id}");

        $response->assertStatus(200);
        $chartExplanation = $response->viewData('chartExplanation');
        
        $this->assertEquals('No sales or forecast data available.', $chartExplanation);
    }

    /** @test */
    public function chart_explanation_shows_rising_trend()
    {
        DB::table('sales')->insert([
            ['pdt_id' => $this->product->pdt_id, 'date' => '2024-01-01', 'quantity' => 100, 'totalAmount' => 1000],
            ['pdt_id' => $this->product->pdt_id, 'date' => '2024-01-05', 'quantity' => 110, 'totalAmount' => 1100],
        ]);

        DB::table('product_forecasts')->insert([
            ['pdt_id' => $this->product->pdt_id, 'forecast_date' => '2024-01-16', 'predicted_sales' => 120],
            ['pdt_id' => $this->product->pdt_id, 'forecast_date' => '2024-01-20', 'predicted_sales' => 140],
        ]);

        $response = $this->get("/sales/forecast/chart/{$this->product->pdt_id}");

        $response->assertStatus(200);
        $chartExplanation = $response->viewData('chartExplanation');
        
        $this->assertStringContainsString('expected to rise', $chartExplanation);
        $this->assertStringContainsString('increase in later periods', $chartExplanation);
    }

    /** @test */
    public function chart_explanation_shows_declining_trend()
    {
        DB::table('sales')->insert([
            ['pdt_id' => $this->product->pdt_id, 'date' => '2024-01-01', 'quantity' => 200, 'totalAmount' => 2000],
        ]);

        DB::table('product_forecasts')->insert([
            ['pdt_id' => $this->product->pdt_id, 'forecast_date' => '2024-01-16', 'predicted_sales' => 150],
            ['pdt_id' => $this->product->pdt_id, 'forecast_date' => '2024-01-20', 'predicted_sales' => 100],
        ]);

        $response = $this->get("/sales/forecast/chart/{$this->product->pdt_id}");

        $response->assertStatus(200);
        $chartExplanation = $response->viewData('chartExplanation');
        
        $this->assertStringContainsString('decrease in later periods', $chartExplanation);
    }

    /** @test */
    public function only_retrieves_sales_for_specific_product()
    {
        $product2 = Product::create([
            'pdt_name' => 'Another Product',
            'price' => 150,
            'stock_level' => 30,
            'inventory_id' => $this->inventory,
        ]);

        DB::table('sales')->insert([
            ['pdt_id' => $this->product->pdt_id, 'date' => '2024-01-01', 'quantity' => 100, 'totalAmount' => 1000],
            ['pdt_id' => $product2->pdt_id, 'date' => '2024-01-01', 'quantity' => 200, 'totalAmount' => 2000],
        ]);

        $response = $this->get("/sales/forecast/chart/{$this->product->pdt_id}");

        $response->assertStatus(200);
        $historicalSales = $response->viewData('historicalSales');
        
        $this->assertCount(1, $historicalSales);
        $this->assertEquals(100, $historicalSales[0]->quantity);
    }

    /** @test */
    public function only_retrieves_forecasts_for_specific_product()
    {
        $product2 = Product::create([
            'pdt_name' => 'Another Product',
            'price' => 150,
            'stock_level' => 30,
            'inventory_id' => $this->inventory,
        ]);

        DB::table('product_forecasts')->insert([
            ['pdt_id' => $this->product->pdt_id, 'forecast_date' => '2024-01-16', 'predicted_sales' => 130],
            ['pdt_id' => $product2->pdt_id, 'forecast_date' => '2024-01-16', 'predicted_sales' => 230],
        ]);

        $response = $this->get("/sales/forecast/chart/{$this->product->pdt_id}");

        $response->assertStatus(200);
        $forecastData = $response->viewData('forecastData');
        
        $this->assertCount(1, $forecastData);
        $this->assertEquals(130, $forecastData[0]->predicted_sales);
    }

    /** @test */
    public function handles_empty_historical_sales()
    {
        DB::table('product_forecasts')->insert([
            ['pdt_id' => $this->product->pdt_id, 'forecast_date' => '2024-01-16', 'predicted_sales' => 130],
        ]);

        $response = $this->get("/sales/forecast/chart/{$this->product->pdt_id}");

        $response->assertStatus(200);
        $forecastData = $response->viewData('forecastData');
        
        $this->assertNotNull($forecastData[0]->explanation_text);
    }

    /** @test */
    public function row_explanations_chain_correctly()
    {
        DB::table('sales')->insert([
            ['pdt_id' => $this->product->pdt_id, 'date' => '2024-01-10', 'quantity' => 100, 'totalAmount' => 1000],
        ]);

        DB::table('product_forecasts')->insert([
            ['pdt_id' => $this->product->pdt_id, 'forecast_date' => '2024-01-16', 'predicted_sales' => 120],
            ['pdt_id' => $this->product->pdt_id, 'forecast_date' => '2024-01-20', 'predicted_sales' => 130],
            ['pdt_id' => $this->product->pdt_id, 'forecast_date' => '2024-01-25', 'predicted_sales' => 110],
        ]);

        $response = $this->get("/sales/forecast/chart/{$this->product->pdt_id}");

        $response->assertStatus(200);
        $forecastData = $response->viewData('forecastData');

        // First compares to historical (100), should rise
        $this->assertStringContainsString('rise', $forecastData[0]->explanation_text);

        // Second compares to first forecast (120), should rise
        $this->assertStringContainsString('rise', $forecastData[1]->explanation_text);

        // Third compares to second forecast (130), should fall
        $this->assertStringContainsString('fall', $forecastData[2]->explanation_text);
    }

    /** @test */
    public function chart_explanation_includes_reasoning()
    {
        DB::table('sales')->insert([
            ['pdt_id' => $this->product->pdt_id, 'date' => '2024-01-01', 'quantity' => 100, 'totalAmount' => 1000],
        ]);

        DB::table('product_forecasts')->insert([
            ['pdt_id' => $this->product->pdt_id, 'forecast_date' => '2024-01-16', 'predicted_sales' => 120],
        ]);

        $response = $this->get("/sales/forecast/chart/{$this->product->pdt_id}");

        $response->assertStatus(200);
        $chartExplanation = $response->viewData('chartExplanation');
        
        $this->assertStringContainsString('Forecasts consider historical trends and seasonal/promotional effects', $chartExplanation);
    }

    /** @test */
    public function row_explanation_includes_reasoning()
    {
        DB::table('sales')->insert([
            ['pdt_id' => $this->product->pdt_id, 'date' => '2024-01-10', 'quantity' => 100, 'totalAmount' => 1000],
        ]);

        DB::table('product_forecasts')->insert([
            ['pdt_id' => $this->product->pdt_id, 'forecast_date' => '2024-01-16', 'predicted_sales' => 150],
        ]);

        $response = $this->get("/sales/forecast/chart/{$this->product->pdt_id}");

        $response->assertStatus(200);
        $forecastData = $response->viewData('forecastData');
        
        $this->assertStringContainsString('Based on past sales trends and seasonal effects', $forecastData[0]->explanation_text);
    }
}