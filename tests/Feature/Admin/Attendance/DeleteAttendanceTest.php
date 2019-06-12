<?php

namespace Tests\Feature\Admin\Attendance;

use App\VitalGym\Entities\Attendance;
use App\VitalGym\Entities\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteAttendanceTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    function an_admin_can_delete_an_attendance()
    {
        $this->withoutExceptionHandling();
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $attendance = factory(Attendance::class)->create();

        $response = $this->be($adminUser)->delete(route('admin.attendances.destroy', $attendance));

        $response->assertRedirect(route('admin.attendances.index'));
        $this->assertEquals(0, Attendance::count());
        $response->assertSessionHas('alert-type', 'success');
        $response->assertSessionHas('message');
    }

    /** @test */
    function see_error_404_when_attempt_to_delete_an_attendance_does_not_exist()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->delete(route('admin.attendances.destroy', '999'));

        $response->assertStatus(404);
    }
}
