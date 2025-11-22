<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Administrator;
use App\Models\InventoryClerk;
use App\Models\SalesAnalyst;
use App\Models\Product;
use App\Models\Report;
use App\Models\Kpi;
use App\Models\Inventory;

class AdminControllerUnitTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // -------------------------------
        // Create an admin manually
        // -------------------------------
        $this->admin = Administrator::create([
            'admin_name' => 'Test Admin',
            'admin_email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Other role users
        InventoryClerk::create([
            'clerk_name' => 'Test Clerk',
            'clerk_email' => 'clerk@example.com',
            'password' => bcrypt('password123'),
        ]);

        SalesAnalyst::create([
            'analyst_name' => 'Test Analyst',
            'analyst_email' => 'analyst@example.com',
            'password' => bcrypt('password123'),
        ]);

        // -------------------------------
        // Create test inventory first
        // -------------------------------
        $inventory = Inventory::create([
            'inventory_name' => 'Test Inventory',
        ]);

        // -------------------------------
        // Create test products
        // -------------------------------
        Product::create([
            'pdt_name' => 'Test Product',
            'price' => 100,
            'stock_level' => 10,
            'inventory_id' => $inventory->inventory_id,
        ]);

        // -------------------------------
        // Create test reports
        // -------------------------------
        Report::create([
            'name' => 'Dummy Report',
            'data' => json_encode(['sample' => 'data']),
        ]);

        // -------------------------------
        // Create test KPIs
        // -------------------------------
        Kpi::create([
            'title' => 'Sales Growth',
            'value' => '500',
            'color' => 'blue',
        ]);

        // Simulate admin session
        session(['admin_id' => $this->admin->admin_id]);
    }

    /** @test */
    public function dashboard_displays()
    {
        $response = $this->withSession(['admin_id' => $this->admin->admin_id])
                         ->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
    }

    /** @test */
    public function inventory_displays()
    {
        $response = $this->withSession(['admin_id' => $this->admin->admin_id])
                         ->get('/admin/inventory');

        $response->assertStatus(200);
        $response->assertSee('Inventory Dashboard');
    }

    /** @test */
    public function users_displays()
    {
        $response = $this->withSession(['admin_id' => $this->admin->admin_id])
                         ->get('/admin/users');

        $response->assertStatus(200);
        $response->assertSee('Users Management');
        $response->assertSee('Test Clerk');
        $response->assertSee('Test Analyst');
    }

    /** @test */
    public function can_delete_inventory_user()
    {
        $clerk = InventoryClerk::first();
        
        $response = $this->withSession(['admin_id' => $this->admin->admin_id])
                         ->delete("/admin/users/inventory-clerk/{$clerk->id}");

        $response->assertStatus(302);
        $this->assertDatabaseMissing('inventory_clerks', ['id' => $clerk->id]);
    }

    /** @test */
    public function reports_displays()
    {
        $response = $this->withSession(['admin_id' => $this->admin->admin_id])
                         ->get('/admin/reports');

        $response->assertStatus(200);
        $response->assertSee('Dummy Report');
    }

    /** @test */
    public function can_generate_summary_report()
    {
        $response = $this->withSession(['admin_id' => $this->admin->admin_id])
                         ->get('/admin/reports/generate');

        $response->assertStatus(302);
    }

    /** @test */
    public function can_view_report()
    {
        $report = Report::first();
        
        $response = $this->withSession(['admin_id' => $this->admin->admin_id])
                         ->get("/admin/reports/{$report->id}");

        $response->assertStatus(200);
        $response->assertSee('Dummy Report');
    }

    /** @test */
    public function can_download_summary_report()
    {
        $report = Report::first();
        
        $response = $this->withSession(['admin_id' => $this->admin->admin_id])
                         ->get("/admin/reports/download/{$report->id}");

        $response->assertStatus(200);
    }

    /** @test */
    public function can_delete_report()
    {
        $report = Report::first();
        
        $response = $this->withSession(['admin_id' => $this->admin->admin_id])
                         ->delete("/admin/reports/{$report->id}");

        $response->assertStatus(302);
        $this->assertDatabaseMissing('reports', ['id' => $report->id]);
    }

    /** @test */
    public function kpis_displays()
    {
        $response = $this->withSession(['admin_id' => $this->admin->admin_id])
                         ->get('/admin/kpis');

        $response->assertStatus(200);
        $response->assertSee('Sales Growth');
    }

    /** @test */
    public function can_add_kpi()
    {
        $response = $this->withSession(['admin_id' => $this->admin->admin_id])
                         ->post('/admin/kpis/add', [
                             'title' => 'New KPI',
                             'value' => '50',
                             'color' => 'green',
                         ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('kpis', ['title' => 'New KPI']);
    }

    /** @test */
    public function can_edit_and_update_kpi()
    {
        $kpi = Kpi::first();
        
        $response = $this->withSession(['admin_id' => $this->admin->admin_id])
                         ->put("/admin/kpis/update/{$kpi->id}", [
                             'title' => 'Updated KPI',
                             'value' => '700',
                             'color' => 'red',
                         ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('kpis', ['title' => 'Updated KPI']);
    }

    /** @test */
    public function can_delete_kpi()
    {
        $kpi = Kpi::first();
        
        $response = $this->withSession(['admin_id' => $this->admin->admin_id])
                         ->delete("/admin/kpis/delete/{$kpi->id}");

        $response->assertStatus(302);
        $this->assertDatabaseMissing('kpis', ['id' => $kpi->id]);
    }
}