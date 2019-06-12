<?php

namespace Tests\Feature\Admin\User;

use App\VitalGym\Entities\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function an_admin_can_view_the_users_list()
    {
        $this->withoutExceptionHandling();

        $adminUser = factory(User::class)->states('admin', 'active')->create();
        factory(User::class)->states('admin')->times(10)->create();
        factory(User::class)->states('customer')->times(5)->create();

        $response = $this->be($adminUser)->get(route('admin.users.index'));

        $response->assertSuccessful();
        $this->assertEquals(10, $response->data('users')->count());
        $response->assertViewIs('admin.users.index');
        $this->assertInstanceOf(LengthAwarePaginator::class, $response->data('users'));
    }
}
