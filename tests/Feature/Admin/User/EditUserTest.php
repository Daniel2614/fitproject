<?php

namespace Tests\Feature\Admin\User;

use App\VitalGym\Entities\ActivationToken;
use App\VitalGym\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;


class EditUserTest extends TestCase
{
    use RefreshDatabase;

    private $file;
    private $user;

    public function setUp()
    {
        parent::setUp();

        Storage::fake('public');
        $this->file = File::image('john.jpg', 160, 160);
        $this->user = factory(User::class)->states('admin')->create([
            'email' => 'john@example.com',
            'password' => bcrypt('laravel'),
            'avatar' => 'avatars/my-avatar.jpg',
        ]);
    }

    private function validParams($overrides = [])
    {
        return array_merge([
            'name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'avatar' => $this->file,
            'phone' => '022691666',
            'cell_phone' => '0968679725',
            'address' => 'Fake address',
            'password' => 'laravel',
            'password_confirmation' => 'laravel',
            'active' => false,
        ], $overrides);
    }

    /** @test */
    function an_admin_can_view_the_page_to_edit_a_user()
    {
        $this->withoutExceptionHandling();
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->get(route('admin.users.edit', $this->user));

        $response->assertSuccessFul();
        $response->assertViewIs('admin.users.edit');
        $this->assertTrue($response->data('user')->is($this->user));
    }

    /** @test */
    function an_admin_can_edit_a_user()
    {
        $this->withoutExceptionHandling();
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->patch(route('admin.users.update', $this->user), $this->validParams());

        $response->assertRedirect(route('admin.users.index'));
        tap(User::whereEmail('john@example.com')->first(), function ( $user ) {
            $this->assertEquals('John', $user->name);
            $this->assertEquals('Doe', $user->last_name);
            $this->assertNotNull($user->avatar);
            Storage::disk('public')->assertExists($user->avatar);
            $this->assertFileEquals($this->file->getPathname(), Storage::disk('public')->path($user->avatar));
            $this->assertEquals('john@example.com', $user->email);
            $this->assertEquals('022691666', $user->phone);
            $this->assertEquals('0968679725', $user->cell_phone);
            $this->assertEquals('Fake address', $user->address);
            $this->assertTrue(Hash::check('laravel', $user->password));
            $this->assertEquals('admin', $user->role);
            $this->assertFalse((boolean) $user->active);
            $this->assertNull(ActivationToken::first());
        });
        $response->assertSessionHas('message');
        $response->assertSessionHas('alert-type', 'success');
    }

    /** @test */
    function name_is_required_to_edit_a_user()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.users.edit', $this->user))->patch(route('admin.users.update', $this->user), $this->validParams([
            'name' => '',
        ]));

        $response->assertRedirect(route('admin.users.edit', $this->user));
        $response->assertSessionHasErrors('name');
    }

    /** @test */
    function name_must_have_maximum_of_80_characters_to_edit_user()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.users.edit', $this->user))->patch(route('admin.users.update', $this->user), $this->validParams([
            'name' => str_random(81),
        ]));

        $response->assertRedirect(route('admin.users.edit', $this->user));
        $response->assertSessionHasErrors('name');
    }

    /** @test */
    function last_name_is_required_to_edit_user()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.users.edit', $this->user))->patch(route('admin.users.update', $this->user), $this->validParams([
            'last_name' => '',
        ]));

        $response->assertRedirect(route('admin.users.edit', $this->user));
        $response->assertSessionHasErrors('last_name');
    }

    /** @test */
    function last_name_must_have_maximum_of_100_characters_to_edit_a_user()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.users.edit', $this->user))->patch(route('admin.users.update', $this->user), $this->validParams([
            'last_name' => str_random(101),
        ]));

        $response->assertRedirect(route('admin.users.edit', $this->user));
        $response->assertSessionHasErrors('last_name');
    }

    /** @test */
    function email_is_required_to_edit_a_user()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.users.edit', $this->user))->patch(route('admin.users.update', $this->user), $this->validParams([
            'email' => '',
        ]));

        $response->assertRedirect(route('admin.users.edit', $this->user));
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    function email_must_be_a_valid_email_to_edit_a_user()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.users.edit', $this->user))->patch(route('admin.users.update', $this->user), $this->validParams([
            'email' => 'invalid-email',
        ]));

        $response->assertRedirect(route('admin.users.edit', $this->user));
        $response->assertSessionHasErrors('email');
    }


    /** @test */
    function email_must_be_unique_to_edit_a_user()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        factory(User::class)->states('admin')->create(['email' => 'jane@example.com']);

        $response = $this->be($adminUser)->from(route('admin.users.edit', $this->user))->patch(route('admin.users.update', $this->user), $this->validParams([
            'email' => 'jane@example.com',
        ]));

        $response->assertRedirect(route('admin.users.edit', $this->user));
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    function ignore_current_user_id_from_unique_validation_rule()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.users.edit', $this->user))->patch(route('admin.users.update', $this->user), $this->validParams([
            'email' => 'john@example.com',
        ]));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHasNoErrors('email');
    }

    /** @test */
    function password_is_optional_to_edit_a_user()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.users.edit', $this->user))->patch(route('admin.users.update', $this->user), $this->validParams([
            'password' => '',
        ]));

        $response->assertRedirect(route('admin.users.index'));
        $this->assertTrue(Hash::check('laravel', $this->user->password));
        $response->assertSessionHasNoErrors('password');
    }

    /** @test */
    function password_must_have_a_minimum_of_6_characters_to_edit_a_user()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.users.edit', $this->user))->patch(route('admin.users.update', $this->user), $this->validParams([
            'password' => '123',
        ]));

        $response->assertRedirect(route('admin.users.edit', $this->user));
        $response->assertSessionHasErrors('password');
    }

    /** @test */
    function password_must_have_a_maximum_of_64_characters_to_edit_a_user()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.users.edit', $this->user))->patch(route('admin.users.update', $this->user), $this->validParams([
            'password' => str_random(65),
        ]));

        $response->assertRedirect(route('admin.users.edit', $this->user));
        $response->assertSessionHasErrors('password');
    }

    /** @test */
    function password_must_be_equals_to_password_confirmation_field_to_edit_a_user()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.users.edit', $this->user))->patch(route('admin.users.update', $this->user), $this->validParams([
            'password' => 'secret',
            'password_confirmation' => 'laravel',
        ]));

        $response->assertRedirect(route('admin.users.edit', $this->user));
        $response->assertSessionHasErrors('password');
    }

    /** @test */
    function avatar_is_optional_to_edit_a_user()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create(['avatar' => 'avatars/my-avatar.jpg']);

        $response = $this->be($adminUser)->from(route('admin.users.edit', $this->user))->patch(route('admin.users.update', $this->user), $this->validParams([
            'avatar' => '',
        ]));

        $response->assertRedirect(route('admin.users.index'));
        $user = User::whereEmail('john@example.com')->first();
        $this->assertEquals('avatars/my-avatar.jpg', $user->avatar);
        $response->assertSessionHas('alert-type', 'success');
        $response->assertSessionHas('message');
    }

    /** @test */
    function avatar_must_be_an_image_to_edit_a_user()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.users.edit', $this->user))->patch(route('admin.users.update', $this->user), $this->validParams([
            'avatar' => File::create('no-image.pdf'),
        ]));

        $response->assertRedirect(route('admin.users.edit', $this->user));
        $response->assertSessionHasErrors('avatar');
    }

    /** @test */
    function avatar_must_have_a_maximum_of_1024_kilobytes_to_edit_a_user()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.users.edit', $this->user))->patch(route('admin.users.update', $this->user), $this->validParams([
            'avatar' => File::create('my-avatar.png')->size(1025),
        ]));

        $response->assertRedirect(route('admin.users.edit', $this->user));
        $response->assertSessionHasErrors('avatar');
    }

    /** @test */
    function phone_is_required_to_edit_a_user()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.users.edit', $this->user))->patch(route('admin.users.update', $this->user), $this->validParams([
            'phone' => '',
        ]));

        $response->assertRedirect(route('admin.users.edit', $this->user));
        $response->assertSessionHasErrors('phone');
    }

    /** @test */
    function phone_must_have_a_maximum_of_10_characters_to_edit_a_user()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.users.edit', $this->user))->patch(route('admin.users.update', $this->user), $this->validParams([
            'phone' => str_random(11),
        ]));

        $response->assertRedirect(route('admin.users.edit', $this->user));
        $response->assertSessionHasErrors('phone');
    }

    /** @test */
    function cell_phone_is_required_to_edit_a_user()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.users.edit', $this->user))->patch(route('admin.users.update', $this->user), $this->validParams([
            'cell_phone' => '',
        ]));

        $response->assertRedirect(route('admin.users.edit', $this->user));
        $response->assertSessionHasErrors('cell_phone');
    }

    /** @test */
    function cell_phone_must_have_a_maximum_of_10_characters_to_edit_a_user()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.users.edit', $this->user))->patch(route('admin.users.update', $this->user), $this->validParams([
            'cell_phone' => str_random(11),
        ]));

        $response->assertRedirect(route('admin.users.edit', $this->user));
        $response->assertSessionHasErrors('cell_phone');
    }

    /** @test */
    function address_is_required_to_edit_a_user()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.users.edit', $this->user))->patch(route('admin.users.update', $this->user), $this->validParams([
            'address' => '',
        ]));

        $response->assertRedirect(route('admin.users.edit', $this->user));
        $response->assertSessionHasErrors('address');
    }

    /** @test */
    function address_must_have_a_maximum_of_255_characters_to_edit_a_user()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.users.edit', $this->user))->patch(route('admin.users.update', $this->user), $this->validParams([
            'address' => str_random(256),
        ]));

        $response->assertRedirect(route('admin.users.edit', $this->user));
        $response->assertSessionHasErrors('address');
    }

    /** @test */
    function active_is_required_to_edit_a_user()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.users.edit', $this->user))->patch(route('admin.users.update', $this->user), $this->validParams([
            'active' => '',
        ]));

        $response->assertRedirect(route('admin.users.edit', $this->user));
        $response->assertSessionHasErrors('active');
    }

    /** @test */
    function active_must_be_boolean_to_edit_a_user()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.users.edit', $this->user))->patch(route('admin.users.update', $this->user), $this->validParams([
            'active' => 'no-boolean',
        ]));

        $response->assertRedirect(route('admin.users.edit', $this->user));
        $response->assertSessionHasErrors('active');
    }
}
