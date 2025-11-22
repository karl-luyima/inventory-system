<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\InventoryClerk;
use App\Models\SalesAnalyst;
use App\Models\Kpi;
use App\Models\Product;
use App\Models\Report;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Mockery;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(); // Skip auth
    }

    protected function tearDown(): void
    {
        Mockery::close(); // clean up mocks
        parent::tearDown();
    }

    // ---------------- Helper Methods ----------------

    private function mockCount($modelClass, int $count)
    {
        $spy = Mockery::spy($modelClass);
        $spy->shouldReceive('count')->andReturn($count);
        return $spy;
    }

    private function mockAll($modelClass, $collection)
    {
        $spy = Mockery::spy($modelClass);
        $spy->shouldReceive('all')->andReturn(collect($collection));
        return $spy;
    }

    private function mockFindOrFail($modelClass, $id, $mockObject)
    {
        $spy = Mockery::spy($modelClass);
        $spy->shouldReceive('findOrFail')->with($id)->andReturn($mockObject);
        return $spy;
    }

    private function mockProductWhereGet($products)
    {
        $spy = Mockery::spy(Product::class);
        $query = Mockery::spy();
        $query->shouldReceive('get')->andReturn(collect($products));
        $spy->shouldReceive('where')->andReturn($query);
        $spy->shouldReceive('count')->andReturn(count($products) + 50);
        return $spy;
    }

    private function mockDBChain($data = [])
    {
        $dbMock = Mockery::mock('alias:' . DB::class);
        $dbMock->shouldReceive('table')->andReturnSelf()
            ->shouldReceive('leftJoin')->andReturnSelf()
            ->shouldReceive('select')->andReturnSelf()
            ->shouldReceive('groupBy')->andReturnSelf()
            ->shouldReceive('orderByDesc')->andReturnSelf()
            ->shouldReceive('limit')->andReturnSelf()
            ->shouldReceive('get')->andReturn(collect($data));
        return $dbMock;
    }

    private function mockReportCreate()
    {
        $spy = Mockery::mock(Report::class);
        $spy->shouldReceive('create')->andReturn(Mockery::mock(Report::class));
        return $spy;
    }

    private function mockPdfDownload()
    {
        $pdfMock = Mockery::mock(Pdf::class);
        $pdfMock->shouldReceive('loadView')->andReturnSelf();
        $pdfMock->shouldReceive('download')->andReturn('pdf_content');
        return $pdfMock;
    }

    // ---------------- Tests ----------------

    public function test_dashboard(): void
    {
        $this->mockCount(InventoryClerk::class, 5);
        $this->mockCount(SalesAnalyst::class, 10);
        $this->mockCount(Kpi::class, 8);

        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.home');
    }

    public function test_inventory(): void
    {
        $spy = Mockery::spy(Product::class);
        $spy->shouldReceive('paginate')->with(10)->andReturn(collect());

        $response = $this->get(route('admin.inventory'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.inventory');
    }

    public function test_users(): void
    {
        $this->mockAll(InventoryClerk::class, [
            (object)['clerk_id'=>1,'clerk_name'=>'Inv A','clerk_email'=>'a@inv.com']
        ]);
        $this->mockAll(SalesAnalyst::class, [
            (object)['analyst_id'=>2,'analyst_name'=>'Sales B','analyst_email'=>'b@sales.com']
        ]);

        $response = $this->get(route('admin.users'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.users');
    }

    public function test_generateSummaryReport(): void
    {
        $this->mockCount(InventoryClerk::class, 5);
        $this->mockCount(SalesAnalyst::class, 10);
        $this->mockCount(Kpi::class, 8);
        $this->mockProductWhereGet([
            (object)['pdt_name'=>'Product A','stock_level'=>3]
        ]);
        $this->mockDBChain([
            (object)['pdt_name'=>'Top X','price'=>200,'quantity_sold'=>10,'total_ksh'=>2000]
        ]);
        $this->mockReportCreate();

        $response = $this->get(route('generateSummaryReport'));
        $response->assertRedirect(route('admin.reports'));
    }

    public function test_viewReport(): void
    {
        $report = Mockery::mock();
        $report->data = json_encode(['dummy'=>'data']);
        $this->mockFindOrFail(Report::class, 1, $report);

        $response = $this->get(route('admin.reports.view', 1));
        $response->assertViewIs('admin.view-report');
    }

    public function test_downloadSummaryReport(): void
    {
        $report = Mockery::mock();
        $report->id = 1;
        $report->data = json_encode(['dummy'=>'data']);
        $this->mockFindOrFail(Report::class, 1, $report);
        $this->mockPdfDownload();

        $response = $this->get(route('admin.reports.download', 1));
        $this->assertEquals('pdf_content', $response);
    }

    public function test_addKpi(): void
    {
        $request = Request::create(route('admin.kpis.add'), 'POST', [
            'title'=>'Test KPI','value'=>'100','color'=>'blue'
        ]);

        $spy = Mockery::mock(Kpi::class);
        $spy->shouldReceive('create')->once();

        $response = $this->post(route('admin.kpis.add'), $request->all());
        $response->assertRedirect(route('admin.kpis'));
    }

    public function test_topProducts(): void
    {
        $this->mockDBChain();

        $response = $this->get(route('admin.top-products'));
        $response->assertViewIs('admin.top-products');
    }

    public function test_settings(): void
    {
        $response = $this->get(route('admin.settings'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.settings');
    }

    public function test_inventoryData(): void
    {
        $this->mockProductWhereGet([
            (object)['name'=>'Product A','stock'=>3]
        ]);

        $response = $this->get(route('admin.inventory.data'));
        $response->assertJsonStructure(['totalProducts','lowStockItems']);
    }
}
