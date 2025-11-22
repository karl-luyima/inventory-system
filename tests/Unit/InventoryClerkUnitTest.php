<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\InventoryClerk;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Sale;
use App\Models\Kpi;

class InventoryClerkUnitTest extends TestCase
{
    use RefreshDatabase;

    protected $clerk;
    protected $inventory;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an inventory clerk
        $this->clerk = InventoryClerk::create([
            'clerk_name' => 'Test Clerk',
            'clerk_email' => 'clerk@test.com',
            'password' => bcrypt('password123'),
        ]);

        // Create an inventory
        $this->inventory = Inventory::create([
            'inventory_name' => 'Main Inventory',
            'pdtList' => json_encode([]),
        ]);

        // Create a product
        $this->product = Product::create([
            'pdt_name' => 'Test Product',
            'price' => 100,
            'stock_level' => 50,
            'inventory_id' => $this->inventory->inventory_id,
        ]);

        // Create test KPIs
        Kpi::create([
            'title' => 'Total Sales',
            'value' => '1000',
            'color' => 'blue',
        ]);
    }

    /** @test */
    public function dashboard_displays()
    {
        $response = $this->withSession(['clerk_id' => $this->clerk->id])
                         ->get('/clerk/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Test Product');
    }

    /** @test */
    public function can_search_products()
    {
        $response = $this->withSession(['clerk_id' => $this->clerk->id])
                         ->get('/clerk/search?search=Test');

        $response->assertStatus(200);
        $response->assertSee('Test Product');
    }

    /** @test */
    public function can_update_stock()
    {
        $response = $this->withSession(['clerk_id' => $this->clerk->id])
                         ->put("/clerk/update-stock/{$this->product->pdt_id}", [
                             'stock' => 75,
                         ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('products', [
            'pdt_id' => $this->product->pdt_id,
            'stock_level' => 75,
        ]);
    }

    /** @test */
    public function cannot_update_stock_with_negative_value()
    {
        $response = $this->withSession(['clerk_id' => $this->clerk->id])
                         ->put("/clerk/update-stock/{$this->product->pdt_id}", [
                             'stock' => -10,
                         ]);

        $response->assertSessionHasErrors('stock');
    }

    /** @test */
    public function metrics_page_displays()
    {
        $response = $this->withSession(['clerk_id' => $this->clerk->id])
                         ->get('/clerk/metrics');

        $response->assertStatus(200);
        $response->assertSee('Total Sales');
    }

    /** @test */
    public function create_product_form_displays()
    {
        $response = $this->withSession(['clerk_id' => $this->clerk->id])
                         ->get('/clerk/products/create');

        $response->assertStatus(200);
        $response->assertSee('Main Inventory');
    }

    /** @test */
    public function can_store_product()
    {
        $response = $this->withSession(['clerk_id' => $this->clerk->id])
                         ->post('/clerk/products/store', [
                             'pdt_name' => 'New Product',
                             'price' => 250,
                             'stock_level' => 30,
                             'inventory_id' => $this->inventory->inventory_id,
                         ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('products', [
            'pdt_name' => 'New Product',
            'price' => 250,
            'stock_level' => 30,
        ]);
    }

    /** @test */
    public function cannot_store_product_with_invalid_inventory()
    {
        $response = $this->withSession(['clerk_id' => $this->clerk->id])
                         ->post('/clerk/products/store', [
                             'pdt_name' => 'Invalid Product',
                             'price' => 100,
                             'stock_level' => 10,
                             'inventory_id' => 999,
                         ]);

        $response->assertSessionHasErrors('inventory_id');
    }

    /** @test */
    public function create_inventory_form_displays()
    {
        $response = $this->withSession(['clerk_id' => $this->clerk->id])
                         ->get('/clerk/inventory/create');

        $response->assertStatus(200);
    }

    /** @test */
    public function can_store_inventory()
    {
        $response = $this->withSession(['clerk_id' => $this->clerk->id])
                         ->post('/clerk/inventory/store', [
                             'inventory_name' => 'Secondary Inventory',
                             'pdtList' => json_encode(['product1', 'product2']),
                         ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('inventories', [
            'inventory_name' => 'Secondary Inventory',
        ]);
    }

    /** @test */
    public function report_page_displays()
    {
        $response = $this->withSession(['clerk_id' => $this->clerk->id])
                         ->get('/clerk/report');

        $response->assertStatus(200);
        $response->assertSee('Total Sales');
        $response->assertSee('Test Product');
        $response->assertSee('Main Inventory');
    }

    /** @test */
    public function can_download_report()
    {
        $response = $this->withSession(['clerk_id' => $this->clerk->id])
                         ->get('/clerk/report/download');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    /** @test */
    public function can_create_new_clerk()
    {
        $response = $this->withSession(['clerk_id' => $this->clerk->id])
                         ->post('/clerk/create', [
                             'clerk_name' => 'New Clerk',
                             'clerk_email' => 'newclerk@test.com',
                             'password' => 'password123',
                             'password_confirmation' => 'password123',
                         ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('inventory_clerks', [
            'clerk_name' => 'New Clerk',
            'clerk_email' => 'newclerk@test.com',
        ]);
    }

    /** @test */
    public function cannot_create_clerk_with_duplicate_email()
    {
        $response = $this->withSession(['clerk_id' => $this->clerk->id])
                         ->post('/clerk/create', [
                             'clerk_name' => 'Another Clerk',
                             'clerk_email' => 'clerk@test.com',
                             'password' => 'password123',
                             'password_confirmation' => 'password123',
                         ]);

        $response->assertSessionHasErrors('clerk_email');
    }
}