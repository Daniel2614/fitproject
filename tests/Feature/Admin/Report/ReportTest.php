<?php

namespace Tests\Feature\Admin\Report;

use App\Exports\CustomersExcelExport;
use App\Exports\MembershipsExcelExport;
use App\VitalGym\Entities\Customer;
use App\VitalGym\Entities\Membership;
use App\VitalGym\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function an_admin_can_view_the_page_of_reports()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->get(route('admin.reports.index'));

        $response->assertSuccessful();
        $response->assertViewIs('admin.reports.index');
    }

    /**
     * @test
     */
    public function user_can_download_memberships_report()
    {
        Excel::fake();
        $this->withoutExceptionHandling();

        $adminUser = factory(User::class)->states('admin', 'active')->create();
        factory(Membership::class)->times(5)->create();
        $memberships = Membership::with('customer', 'plan', 'payment')->orderByDesc('created_at')->get();


        $this->be($adminUser)->get(route('admin.memberships-export.excel'));

        Excel::assertDownloaded('memberships.xlsx', function(MembershipsExcelExport $export)  use ( $memberships ){
            return $export->query()->get() == $memberships;
         });
    }

    /**
     * @test
     */
    public function user_can_download_customer_report()
    {
        Excel::fake();
        $this->withoutExceptionHandling();

        $adminUser = factory(User::class)->states('admin', 'active')->create();
        factory(Customer::class)->times(5)->create();
        $customers = Customer::with('level', 'routine')->orderByDesc('created_at')->get();

        $this->be($adminUser)->get(route('admin.customers-export.excel'));

        Excel::assertDownloaded('customers.xlsx', function(CustomersExcelExport $export)  use ( $customers ){
            return $export->query()->get() == $customers;
        });
    }
}
