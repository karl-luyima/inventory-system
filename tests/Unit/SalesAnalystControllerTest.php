<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\SalesAnalyst;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Inventory;

class SalesAnalystControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $analyst;
    protected $inventory;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a sales analyst
        $this->analyst = SalesAnalyst::create([
            'analyst_name' => 'Test Analyst',
            'analyst_email' => 'analyst@test.com',
            'password' => bcrypt('password123'),
        ]);

        // Create an inventory
        $this->inventory = Inventory::create([
            'inventory_name' => 'Test Inventory',
            'pdtList' => json_encode([]),
        ]);

        // Create a product
        $this->product = Product::create([
            'pdt_name' => 'Test Product',
            'price' => 100,
            'stock_level' => 50,
            'inventory_id' => $this->inventory->inventory_id,
        ]);

        // Create some sales
        Sale::create([
            'pdt_id' => $this->product->pdt_id,
            'quantity' => 5,
            'totalAmount' => 500,
            'date' => now()->subDays(2),
        ]);

        Sale::create([
            'pdt_id' => $this->product->pdt_id,
            'quantity' => 3,
            'totalAmount' => 300,
            'date' => now()->subDays(1),
        ]);
    }

    /** @test */
    public function dashboard_displays()
    {
        $response = $this->withSession(['analyst_id' => $this->analyst->id])
                         ->get('/sales/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Record a New Sale');
    }

    /** @test */
    public function can_store_sale()
    {
        $response = $this->withSession(['analyst_id' => $this->analyst->id])
                         ->postJson('/sales/store', [
                             'pdt_name' => 'Test Product',
                             'quantity' => 2,
                             'totalAmount' => 200,
                             'price' => 100,
                             'stock_level' => 50,
                             'inventory_id' => $this->inventory->inventory_id,
                         ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        
        // Check sale was created
        $this->assertDatabaseHas('sales', [
            'pdt_id' => $this->product->pdt_id,
            'quantity' => 2,
            'totalAmount' => 200,
        ]);

        // Check stock was reduced
        $this->product->refresh();
        $this->assertEquals(48, $this->product->stock_level);
    }

    /** @test */
    public function cannot_store_sale_with_insufficient_stock()
    {
        $response = $this->withSession(['analyst_id' => $this->analyst->id])
                         ->postJson('/sales/store', [
                             'pdt_name' => 'Test Product',
                             'quantity' => 100,
                             'totalAmount' => 10000,
                             'price' => 100,
                             'stock_level' => 50,
                             'inventory_id' => $this->inventory->inventory_id,
                         ]);

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
        
        // Stock should remain unchanged
        $this->product->refresh();
        $this->assertEquals(50, $this->product->stock_level);
    }

    /** @test */
    public function can_store_sale_for_new_product()
    {
        $response = $this->withSession(['analyst_id' => $this->analyst->id])
                         ->postJson('/sales/store', [
                             'pdt_name' => 'New Product',
                             'quantity' => 1,
                             'totalAmount' => 150,
                             'price' => 150,
                             'stock_level' => 20,
                             'inventory_id' => $this->inventory->inventory_id,
                         ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        
        // Check product was created
        $this->assertDatabaseHas('products', [
            'pdt_name' => 'New Product',
            'price' => 150,
        ]);
    }

    /** @test */
    public function reports_page_displays()
    {
        $response = $this->withSession(['analyst_id' => $this->analyst->id])
                         ->get('/sales/reports');

        $response->assertStatus(200);
        $response->assertSee('Test Product');
    }

    /** @test */
    public function can_download_report()
    {
        $response = $this->withSession(['analyst_id' => $this->analyst->id])
                         ->get('/sales/download');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    /** @test */
    public function can_fetch_sales_data()
    {
        $response = $this->withSession(['analyst_id' => $this->analyst->id])
                         ->get('/sales/fetch-sales-data');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'sales',
            'topProducts',
        ]);
    }

    /** @test */
    public function fetch_sales_data_returns_correct_format()
    {
        $response = $this->withSession(['analyst_id' => $this->analyst->id])
                         ->getJson('/sales/fetch-sales-data');

        $response->assertStatus(200);
        
        $data = $response->json();
        
        // Check sales array exists
        $this->assertIsArray($data['sales']);
        
        // Check topProducts array exists and has correct structure
        $this->assertIsArray($data['topProducts']);
        if (count($data['topProducts']) > 0) {
            $this->assertArrayHasKey('pdt_name', $data['topProducts'][0]);
            $this->assertArrayHasKey('total_sold', $data['topProducts'][0]);
            $this->assertArrayHasKey('total_amount', $data['topProducts'][0]);
        }
    }

    /** @test */
    public function forecast_page_displays()
    {
        $response = $this->withSession(['analyst_id' => $this->analyst->id])
                         ->get('/sales/forecast');

        $response->assertStatus(200);
    }

    /** @test */
    public function sales_data_shows_recent_sales()
    {
        $response = $this->withSession(['analyst_id' => $this->analyst->id])
                         ->getJson('/sales/fetch-sales-data');

        $data = $response->json();
        
        // Should have 2 sales from setUp
        $this->assertGreaterThanOrEqual(2, count($data['sales']));
    }

    /** @test */
    public function top_products_shows_product_with_most_sales()
    {
        $response = $this->withSession(['analyst_id' => $this->analyst->id])
                         ->getJson('/sales/fetch-sales-data');

        $data = $response->json();
        
        // Check that Test Product appears in top products
        $topProductNames = array_column($data['topProducts'], 'pdt_name');
        $this->assertContains('Test Product', $topProductNames);
    }

    /** @test */
    public function store_validates_required_fields()
    {
        $response = $this->withSession(['analyst_id' => $this->analyst->id])
                         ->postJson('/sales/store', [
                             // Missing required fields
                         ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function store_validates_quantity_is_positive()
    {
        $response = $this->withSession(['analyst_id' => $this->analyst->id])
                         ->postJson('/sales/store', [
                             'pdt_name' => 'Test Product',
                             'quantity' => 0,
                             'totalAmount' => 0,
                         ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function reports_shows_total_sales_count()
    {
        $response = $this->withSession(['analyst_id' => $this->analyst->id])
                         ->get('/sales/reports');

        $response->assertStatus(200);
        
        // Should show the 2 sales we created
        $response->assertViewHas('totalSales', 2);
    }

    /** @test */
    public function reports_shows_total_revenue()
    {
        $response = $this->withSession(['analyst_id' => $this->analyst->id])
                         ->get('/sales/reports');

        $response->assertStatus(200);
        
        // Total revenue should be 500 + 300 = 800
        $response->assertViewHas('totalRevenue', 800);
    }

    /** @test */
    public function reports_shows_total_products_sold()
    {
        $response = $this->withSession(['analyst_id' => $this->analyst->id])
                         ->get('/sales/reports');

        $response->assertStatus(200);
        
        // Total products sold should be 5 + 3 = 8
        $response->assertViewHas('totalProducts', 8);
    }
}