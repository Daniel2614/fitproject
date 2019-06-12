<?php

namespace Tests\Feature\Admin\Attendance;

use App\VitalGym\Entities\Attendance;
use App\VitalGym\Entities\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewAttendanceListTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    function an_admin_can_view_attendances_list()
    {
        $this->withoutExceptionHandling();
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        factory(Attendance::class)->times(5)->create();

        $response = $this->be($adminUser)->get(route('admin.attendances.index'));

        $response->assertSuccessFul();
        $response->assertViewIs('admin.attendances.index');
        $expectedAttendances = Attendance::with('customer')->orderByDesc('id')->paginate();
        $response->assertViewHas('attendances', $expectedAttendances);
        $this->assertEquals(5, $response->data('attendances')->count());
    }
}
