<?php

namespace Tests\Feature\Admin\User;

use App\VitalGym\Entities\User;
use Illuminate\Http\Testing\File;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteUserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function an_admin_can_delete_a_user()
    {
        Storage::fake('public');
        $adminUser = factory(User::class)->states('admin')->create();
        $file = File::image('my-avatar.jpg', 160, 160);

        $user = factory(User::class)->states('admin')->create([
            'email' => 'john@example.com',
            'avatar' => "avatars/{$file->name}",
        ]);

        $response = $this->be($adminUser)->delete(route('admin.users.destroy', $user));

        $response->assertRedirect(route('admin.users.index'));
        $this->assertEquals(0, User::whereEmail('john@example.com')->count());
        Storage::disk('public')->assertMissing($user->avatar);
    }
    
    /** @test */
    function dont_delete_default_avatar()
    {
        $adminUser = factory(User::class)->states('admin')->create();

        $user = factory(User::class)->states('admin')->create([
            'email' => 'john@example.com',
            'avatar' => "avatars/default-avatar.jpg",
        ]);

        $response = $this->be($adminUser)->delete(route('admin.users.destroy', $user));

        $response->assertRedirect(route('admin.users.index'));
        $this->assertEquals(0, User::whereEmail('john@example.com')->count());
        Storage::disk('public')->assertExists('avatars/default-avatar.jpg');
    }
    
    /** @test */
    function see_error_404_when_try_to_delete_a_user_that_does_not_exists()
    {
        $adminUser = factory(User::class)->states('admin')->create();

        $response = $this->be($adminUser)->delete(route('admin.users.destroy', '999'));

        $response->assertStatus(404);
    }
}
