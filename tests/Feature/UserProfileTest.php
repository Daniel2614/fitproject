<?php

namespace Tests\Feature;

use App\VitalGym\Entities\User;
use Tests\TestCase;
use Storage;
use Hash;
use Illuminate\Http\Testing\File;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    private $file;

    public function setUp()
    {
        parent::setUp();

        Storage::fake('public');
        $this->file = File::image('john.jpg', 160, 160);
    }

    private function validParams($overrides = [])
    {
        return array_merge([
            'name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'avatar' => $this->file,
            'phone' => '2695755',
            'cell_phone' => '0123456789',
            'address' => 'Fake address',
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ], $overrides);
    }

    /** @test */
    function an_admin_can_view_the_page_to_edit_his_profile()
    {
        $this->withoutExceptionHandling();
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->get(route('admin.profile.edit'));

        $response->assertSuccessFul();
        $this->assertTrue($response->data('user')->is($adminUser));
    }

   /** @test */
   function an_admin_can_edit_his_profile()
   {
       $this->withoutExceptionHandling();

       $adminUser = factory(User::class)->states('customer', 'active')->create([
           'name' => 'John',
           'last_name' => 'Doe',
           'email' => 'john@example.com',
           'avatar' => $this->file,
           'phone' => '2695755',
           'cell_phone' => '0123456789',
           'address' => 'Fake address',
           'password' => 'secret',
       ]);

       $newImage = File::image('new-avatar.jpg', 160, 160);

       $response = $this->be($adminUser)->from(route('admin.profile.edit'))->patch(route('admin.profile.update'), [
           'name' => 'Jane',
           'last_name' => 'Eyre',
           'email' => 'jane@example.com',
           'avatar' => $newImage,
           'phone' => '02261666',
           'cell_phone' => '0968679735',
           'address' => 'New address',
           'password' => 'laravel',
           'password_confirmation' => 'laravel',
       ]);

       $response->assertRedirect(route('admin.profile.edit'));
       tap(User::first(), function ( $user ) use ( $newImage ){
           $this->assertEquals('Jane', $user->name);
           $this->assertEquals('Eyre', $user->last_name);
           $this->assertEquals('jane@example.com', $user->email);
           $this->assertNotNull($user->avatar);
           Storage::disk('public')->assertExists($user->avatar);
           $this->assertFileEquals($newImage->getPathname(), Storage::disk('public')->path($user->avatar));
           $this->assertEquals('02261666', $user->phone);
           $this->assertEquals('0968679735', $user->cell_phone);
           $this->assertEquals('New address', $user->address);
           $this->assertTrue(Hash::check('laravel', $user->password));
       });
       $response->assertSessionHas('alert-type', 'success');
       $response->assertSessionHas('message');
   }

    /** @test */
    function name_is_required()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.profile.edit'))->patch(route('admin.profile.update'), $this->validParams([
            'name' => '',
        ]));

        $response->assertRedirect(route('admin.profile.edit'));
        $response->assertSessionHasErrors('name');
    }

    /** @test */
    function name_must_have_maximum_of_80_characters()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.profile.edit'))->patch(route('admin.profile.update'), $this->validParams([
            'name' => str_random(81),
        ]));

        $response->assertRedirect(route('admin.profile.edit'));
        $response->assertSessionHasErrors('name');
    }


    /** @test */
    function last_name_is_required()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.profile.edit'))->patch(route('admin.profile.update'), $this->validParams([
            'last_name' => '',
        ]));

        $response->assertRedirect(route('admin.profile.edit'));
        $response->assertSessionHasErrors('last_name');
    }

    /** @test */
    function last_name_must_have_maximum_of_100_characters()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.profile.edit'))->patch(route('admin.profile.update'), $this->validParams([
            'last_name' => str_random(101),
        ]));

        $response->assertRedirect(route('admin.profile.edit'));
        $response->assertSessionHasErrors('last_name');
    }

    /** @test */
    function email_is_required()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.profile.edit'))->patch(route('admin.profile.update'), $this->validParams([
            'email' => '',
        ]));

        $response->assertRedirect(route('admin.profile.edit'));
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    function email_must_be_a_valid_email()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.profile.edit'))->patch(route('admin.profile.update'), $this->validParams([
            'email' => 'invalid-email',
        ]));

        $response->assertRedirect(route('admin.profile.edit'));
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    function email_must_be_unique()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $otherUser = factory(User::class)->states('customer')->create(['email' => 'john@example.com']);

        $response = $this->be($adminUser)->from(route('admin.profile.edit'))->patch(route('admin.profile.update'), $this->validParams([
            'email' => $otherUser->email,
        ]));

        $response->assertRedirect(route('admin.profile.edit'));
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    function ignore_a_given_user_ID_during_the_unique_email_check()
    {
        $this->withoutExceptionHandling();

        $adminUser = factory(User::class)->states('admin', 'active')->create(['email' =>'john@example.com',]);

        $response = $this->be($adminUser)->from(route('admin.profile.edit'))->patch(route('admin.profile.update'), $this->validParams([
            'email' => 'john@example.com',
        ]));

        $response->assertRedirect(route('admin.profile.edit'));
        $this->assertEquals('john@example.com', $adminUser->email);
        $response->assertSessionHas('alert-type', 'success');
        $response->assertSessionHas('message');
    }

    /** @test */
    function password_is_optional()
    {
        $this->withoutExceptionHandling();
        $adminUser = factory(User::class)->states('admin', 'active')->create(['password' => bcrypt('secret')]);

        $response = $this->be($adminUser)->from(route('admin.profile.edit'))->patch(route('admin.profile.update'), $this->validParams([
            'password' => '',
        ]));

        $response->assertRedirect(route('admin.profile.edit'));
        $adminUser = User::first();
        $this->assertTrue(Hash::check('secret', $adminUser->password));
        $response->assertSessionHas('alert-type', 'success');
        $response->assertSessionHas('message');
    }

    /** @test */
    function password_must_have_a_minimum_of_6_characters()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.profile.edit'))->patch(route('admin.profile.update'), $this->validParams([
            'password' => '12345',
        ]));

        $response->assertRedirect(route('admin.profile.edit'));
        $response->assertSessionHasErrors('password');
    }

    /** @test */
    function user_password_must_have_a_maximum_of_64_characters()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.profile.edit'))->patch(route('admin.profile.update'), $this->validParams([
            'password' => str_random(65),
        ]));

        $response->assertRedirect(route('admin.profile.edit'));
        $response->assertSessionHasErrors('password');
    }

    /** @test */
    function user_password_must_be_equals_to_password_confirmation_field()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.profile.edit'))->patch(route('admin.profile.update'), $this->validParams([
            'password' => 'laravel',
            'password_confirmation' => 'other-password',
        ]));

        $response->assertRedirect(route('admin.profile.edit'));
        $response->assertSessionHasErrors('password');
    }

    /** @test */
    function user_avatar_is_optional()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create(['avatar' => 'avatars/my-avatar.jpg']);

        $response = $this->be($adminUser)->from(route('admin.profile.edit'))->patch(route('admin.profile.update'), $this->validParams([
            'avatar' => null,
        ]));

        $response->assertRedirect(route('admin.profile.edit'));
        $user = User::first();
        $this->assertEquals('avatars/my-avatar.jpg', $user->avatar);
        $response->assertSessionHas('alert-type', 'success');
        $response->assertSessionHas('message');
    }

    /** @test */
    function user_avatar_must_be_an_image()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.profile.edit'))->patch(route('admin.profile.update'), $this->validParams([
            'avatar' => File::create('no-image.pdf'),
        ]));

        $response->assertRedirect(route('admin.profile.edit'));
        $response->assertSessionHasErrors('avatar');
    }

    /** @test */
    function user_avatar_must_have_a_maximum_of_1024_kilobytes()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.profile.edit'))->patch(route('admin.profile.update'), $this->validParams([
            'avatar' => File::image('avatar.jpg')->size(1025),
        ]));

        $response->assertRedirect(route('admin.profile.edit'));
        $response->assertSessionHasErrors('avatar');
    }

    /** @test */
    function user_phone_is_required()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.profile.edit'))->patch(route('admin.profile.update'), $this->validParams([
            'phone' => '',
        ]));

        $response->assertRedirect(route('admin.profile.edit'));
        $response->assertSessionHasErrors('phone');
    }

    /** @test */
    function user_phone_must_have_a_maximum_of_10_characters()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.profile.edit'))->patch(route('admin.profile.update'), $this->validParams([
            'phone' => str_random(11),
        ]));

        $response->assertRedirect(route('admin.profile.edit'));
        $response->assertSessionHasErrors('phone');
    }

    /** @test */
    function user_cell_phone_is_required()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.profile.edit'))->patch(route('admin.profile.update'), $this->validParams([
            'cell_phone' => '',
        ]));

        $response->assertRedirect(route('admin.profile.edit'));
        $response->assertSessionHasErrors('cell_phone');
    }

    /** @test */
    function user_cell_phone_must_have_a_maximum_of_10_characters()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.profile.edit'))->patch(route('admin.profile.update'), $this->validParams([
            'cell_phone' => str_random(11),
        ]));

        $response->assertRedirect(route('admin.profile.edit'));
        $response->assertSessionHasErrors('cell_phone');
    }

    /** @test */
    function user_address_is_required()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.profile.edit'))->patch(route('admin.profile.update'), $this->validParams([
            'address' => '',
        ]));

        $response->assertRedirect(route('admin.profile.edit'));
        $response->assertSessionHasErrors('address');
    }

    /** @test */
    function user_address_must_have_a_maximum_of_255_characters()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.profile.edit'))->patch(route('admin.profile.update'), $this->validParams([
            'address' => str_random(256),
        ]));

        $response->assertRedirect(route('admin.profile.edit'));
        $response->assertSessionHasErrors('address');
    }
}
