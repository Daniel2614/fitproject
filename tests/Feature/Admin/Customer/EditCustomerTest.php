<?php

namespace Tests\Feature\Admin\Customer;

use App\VitalGym\Entities\Customer;
use App\VitalGym\Entities\Level;
use App\VitalGym\Entities\Routine;
use App\VitalGym\Entities\User;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditCustomerTest extends TestCase
{
    use RefreshDatabase;

    private $file;
    private $routine;
    private $level;

    public function setUp()
    {
        parent::setUp();

        Storage::fake('public');
        $this->file = File::image('john.jpg', 160, 160);
    }

    private function validParams($overrides = [])
    {
        $this->level = factory(Level::class)->create();
        $this->routine = factory(Routine::class)->create(['level_id' => $this->level->id]);

        return array_merge([
            'name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'ci' => '0926687856',
            'avatar' => $this->file,
            'phone' => '2695755',
            'cell_phone' => '0123456789',
            'address' => 'Fake address',
            'birthdate' => '1987-12-09',
            'gender' => 'masculino',
            'medical_observations' => 'Problemas del corazón',
            'routine_id' => $this->routine->id,
            'level_id' => $this->level->id,
        ], $overrides);
    }

    /** @test */
    function an_admin_can_view_the_form_to_edit_a_customer()
    {
        $this->withoutExceptionHandling();
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $levels = factory(Level::class)->times(3)->create();
        factory(Routine::class)->times(5)->create(['level_id' => $levels->first()->id]);
        $routine = factory(Routine::class)->create(['level_id' => $levels->first()->id]);
        $customer = factory(Customer::class)->create(['level_id' => $levels->first()->id, 'routine_id' => $routine->id]);

        $response = $this->be($adminUser)->get(route('admin.customers.edit', $customer));

        $response->assertSuccessful();
        $response->assertViewIs('admin.customers.edit');
        $this->assertTrue($response->data('customer')->is($customer));
        $this->assertTrue($response->data('routine')->is($routine));
        $this->assertTrue($response->data('level')->is($levels->first()));
        $levels->assertEquals($response->data('levels'));
        $routines = Routine::all();
        $routines->assertEquals($response->data('routines'));
    }

    /** @test */
    function an_admin_can_edit_a_customer()
    {
        $this->withoutExceptionHandling();
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $levels = factory(Level::class)->times(3)->create();
        $routine = factory(Routine::class)->create(['level_id' => $levels->first()->id]);
        $newRoutine = factory(Routine::class)->create(['level_id' => $levels->last()->id]);
        $user = factory(User::class)->states('customer', 'active')->create([
            'name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'avatar' => $this->file,
            'phone' => '2695755',
            'cell_phone' => '0123456789',
            'address' => 'Fake address',
            'password' => 'secret',
        ]);

        $customer = factory(Customer::class)->create([
            'ci' => '0926687856',
            'birthdate' => '1987-12-09',
            'gender' => 'masculino',
            'medical_observations' => 'Problemas del corazón',
            'level_id' => $levels->first()->id,
            'routine_id' => $routine->id,
            'user_id' =>$user->id
        ]);
        $newImage = File::image('new-avatar.jpg', 160, 160);

        $response = $this->be($adminUser)->patch(route('admin.customers.update', $customer), [
            'name' => 'Jane',
            'last_name' => 'Eyre',
            'email' => 'jane@example.com',
            'avatar' => $newImage,
            'phone' => '02261666',
            'cell_phone' => '0968679735',
            'address' => 'New address',
            'password' => 'laravel',
            'password_confirmation' => 'laravel',
            'ci' => '1723468565',
            'birthdate' => '1988-12-09',
            'gender' => 'femenino',
            'medical_observations' => 'Problemas respiratorios',
            'routine_id' => $newRoutine->id,
            'level_id' => $levels->last()->id,
        ]);

        $response->assertRedirect(route('admin.customers.index'));
        $customer = Customer::first();
        $this->assertEquals('Jane', $customer->user->name);
        $this->assertEquals('Eyre', $customer->user->last_name);
        $this->assertEquals('jane@example.com', $customer->user->email);
        $this->assertNotNull($customer->avatar);
        Storage::disk('public')->assertExists($customer->avatar);
        $this->assertFileEquals($newImage->getPathname(), Storage::disk('public')->path($customer->avatar));
        $this->assertEquals('02261666', $customer->user->phone);
        $this->assertEquals('0968679735', $customer->user->cell_phone);
        $this->assertEquals('New address', $customer->user->address);
        $this->assertTrue(Hash::check('laravel', $customer->user->password));
        $this->assertEquals('1723468565', $customer->ci);
        $this->assertEquals('1988-12-09', $customer->birthdate->toDateString());
        $this->assertEquals('femenino', $customer->gender);
        $this->assertEquals('Problemas respiratorios', $customer->medical_observations);
        $this->assertEquals($newRoutine->id, $customer->routine_id);
        $this->assertEquals($levels->last()->id, $customer->level_id);
        $this->assertEquals('Problemas respiratorios', $customer->medical_observations);
        $response->assertSessionHas('alert-type', 'success');
        $response->assertSessionHas('message');
    }

    /** @test */
    function name_is_required()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $customer = factory(Customer::class)->create();

        $response = $this->be($adminUser)->from(route('admin.customers.edit', $customer))->patch(route('admin.customers.update', $customer), $this->validParams([
            'name' => '',
        ]));

        $response->assertRedirect(route('admin.customers.edit', $customer));
        $response->assertSessionHasErrors('name');
    }

    /** @test */
    function name_must_have_maximum_of_80_characters()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $customer = factory(Customer::class)->create();

        $response = $this->be($adminUser)->from(route('admin.customers.edit', $customer))->patch(route('admin.customers.update', $customer), $this->validParams([
            'name' => str_random(81),
        ]));

        $response->assertRedirect(route('admin.customers.edit', $customer));
        $response->assertSessionHasErrors('name');
    }

    /** @test */
    function last_name_is_required()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $customer = factory(Customer::class)->create();

        $response = $this->be($adminUser)->from(route('admin.customers.edit', $customer))->patch(route('admin.customers.update', $customer), $this->validParams([
            'last_name' => '',
        ]));

        $response->assertRedirect(route('admin.customers.edit', $customer));
        $response->assertSessionHasErrors('last_name');
    }

    /** @test */
    function last_name_must_have_maximum_of_100_characters()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $customer = factory(Customer::class)->create();

        $response = $this->be($adminUser)->from(route('admin.customers.edit', $customer))->patch(route('admin.customers.update', $customer), $this->validParams([
            'last_name' => str_random(101),
        ]));

        $response->assertRedirect(route('admin.customers.edit', $customer));
        $response->assertSessionHasErrors('last_name');
    }

    /** @test */
    function email_is_required()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $customer = factory(Customer::class)->create();

        $response = $this->be($adminUser)->from(route('admin.customers.edit', $customer))->patch(route('admin.customers.update', $customer), $this->validParams([
            'email' => '',
        ]));

        $response->assertRedirect(route('admin.customers.edit', $customer));
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    function email_must_be_unique()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $otherUser = factory(User::class)->states('customer')->create(['email' => 'john@example.com']);
        $customer = factory(Customer::class)->create();

        $response = $this->be($adminUser)->from(route('admin.customers.edit', $customer))->patch(route('admin.customers.update', $customer), $this->validParams([
            'email' => $otherUser->email,
        ]));

        $response->assertRedirect(route('admin.customers.edit', $customer));
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    function email_must_be_a_valid_email()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $customer = factory(Customer::class)->create();

        $response = $this->be($adminUser)->from(route('admin.customers.edit', $customer))->patch(route('admin.customers.update', $customer), $this->validParams([
            'email' => 'invalid-email',
        ]));

        $response->assertRedirect(route('admin.customers.edit', $customer));
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    function ignore_a_given_user_ID_during_the_unique_email_check()
    {
        $this->withoutExceptionHandling();

        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $user = factory(User::class)->states('customer')->create(['email' => 'john@example.com']);
        $john = factory(Customer::class)->create(['user_id' => $user->id]);

        $response = $this->be($adminUser)->from(route('admin.customers.edit', $john))->patch(route('admin.customers.update', $john), $this->validParams([
            'email' =>'john@example.com',
        ]));

        $response->assertRedirect(route('admin.customers.index'));
        $customer = Customer::first();
        $this->assertEquals('john@example.com', $customer->user->email);
        $response->assertSessionHas('alert-type', 'success');
        $response->assertSessionHas('message');
    }

    /** @test */
    function password_is_optional()
    {
        $this->withoutExceptionHandling();

        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $user = factory(User::class)->states('customer')->create(['password' => bcrypt('secret')]);
        $customer = factory(Customer::class)->create(['user_id' => $user->id]);

        $response = $this->be($adminUser)->from(route('admin.customers.edit', $customer))->patch(route('admin.customers.update', $customer), $this->validParams([
            'password' => '',
        ]));

        $response->assertRedirect(route('admin.customers.index'));
        $customer = Customer::first();
        $this->assertTrue(Hash::check('secret', $customer->user->password));
        $response->assertSessionHas('alert-type', 'success');
        $response->assertSessionHas('message');
    }

    /** @test */
    function password_must_have_a_minimum_of_6_characters()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $customer = factory(Customer::class)->create();

        $response = $this->be($adminUser)->from(route('admin.customers.edit', $customer))->patch(route('admin.customers.update', $customer), $this->validParams([
            'password' => '12345',
        ]));

        $response->assertRedirect(route('admin.customers.edit', $customer));
        $response->assertSessionHasErrors('password');
    }

    /** @test */
    function password_must_have_a_maximum_of_64_characters()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $customer = factory(Customer::class)->create();

        $response = $this->be($adminUser)->from(route('admin.customers.edit', $customer))->patch(route('admin.customers.update', $customer), $this->validParams([
            'password' => str_random(65),
        ]));

        $response->assertRedirect(route('admin.customers.edit', $customer));
        $response->assertSessionHasErrors('password');
    }

    /** @test */
    function password_must_be_equals_to_password_confirmation_field()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $customer = factory(Customer::class)->create();

        $response = $this->be($adminUser)->from(route('admin.customers.edit', $customer))->patch(route('admin.customers.update', $customer), $this->validParams([
            'password' => 'secret',
            'password' => 'other-password',
        ]));

        $response->assertRedirect(route('admin.customers.edit', $customer));
        $response->assertSessionHasErrors('password');
    }


    /** @test */
    function ci_is_optional()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $customer = factory(Customer::class)->create();

        $response = $this->be($adminUser)->from(route('admin.customers.edit', $customer))->patch(route('admin.customers.update', $customer), $this->validParams([
            'ci' => '',
        ]));

        $response->assertRedirect(route('admin.customers.index'));
        $customer = Customer::first();
        $this->assertNull($customer->ci);
        $response->assertSessionHas('alert-type', 'success');
        $response->assertSessionHas('message');
    }

    /** @test */
    function ci_must_be_unique()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $otherCustomer = factory(Customer::class)->create(['ci' => '0926687856']);
        $customer = factory(Customer::class)->create(['ci' => '1723468565']);

        $response = $this->be($adminUser)->from(route('admin.customers.edit', $customer))->patch(route('admin.customers.update', $customer), $this->validParams([
            'ci' => $otherCustomer->ci,
        ]));

        $response->assertRedirect(route('admin.customers.edit', $customer));
        $response->assertSessionHasErrors('ci');
    }

    /** @test */
    function ignore_a_given_customer_ID_during_the_unique_ci_check()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $customer = factory(Customer::class)->create([ 'ci' => '1723468565']);

        $response = $this->be($adminUser)->from(route('admin.customers.edit', $customer))->patch(route('admin.customers.update', $customer), $this->validParams([
            'ci' => '1723468565',
        ]));

        $response->assertRedirect(route('admin.customers.index'));
        $customer = Customer::first();
        $this->assertEquals('1723468565',$customer->ci);
        $response->assertSessionHas('alert-type', 'success');
        $response->assertSessionHas('message');
    }

    /** @test */
    function ci_must_be_a_valid_ci()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $customer = factory(Customer::class)->create();

        $response = $this->be($adminUser)->from(route('admin.customers.edit', $customer))->patch(route('admin.customers.update', $customer), $this->validParams([
            'ci' => '123545',
        ]));

        $response->assertRedirect(route('admin.customers.edit', $customer));
        $response->assertSessionHasErrors('ci');
    }

    /** @test */
    function avatar_is_optional()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $user = factory(User::class)->states('customer')->create(['avatar' => 'avatars/my-avatar.jpg']);
        $customer = factory(Customer::class)->create(['user_id' => $user->id]);

        $response = $this->be($adminUser)->from(route('admin.customers.edit', $customer))->patch(route('admin.customers.update', $customer), $this->validParams([
            'avatar' => null,
        ]));

        $response->assertRedirect(route('admin.customers.index'));
        $customer = Customer::first();
        $this->assertEquals('avatars/my-avatar.jpg', $customer->avatar);
        $response->assertSessionHas('alert-type', 'success');
        $response->assertSessionHas('message');
    }

    /** @test */
    function avatar_must_be_an_image()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $customer = factory(Customer::class)->create();

        $response = $this->be($adminUser)->from(route('admin.customers.edit', $customer))->patch(route('admin.customers.update', $customer), $this->validParams([
            'avatar' => File::create('no-image.pdf'),
        ]));

        $response->assertRedirect(route('admin.customers.edit', $customer));
        $response->assertSessionHasErrors('avatar');
    }

    /** @test */
    function avatar_must_have_a_maximum_of_1024_kilobytes()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $customer = factory(Customer::class)->create();

        $response = $this->be($adminUser)->from(route('admin.customers.edit', $customer))->patch(route('admin.customers.update', $customer), $this->validParams([
            'avatar' => File::image('avatar.jpg')->size(1025),
        ]));

        $response->assertRedirect(route('admin.customers.edit', $customer));
        $response->assertSessionHasErrors('avatar');
    }

    /** @test */
    function phone_is_required()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $customer = factory(Customer::class)->create();

        $response = $this->be($adminUser)->from(route('admin.customers.edit', $customer))->patch(route('admin.customers.update', $customer), $this->validParams([
            'phone' => '',
        ]));

        $response->assertRedirect(route('admin.customers.edit', $customer));
        $response->assertSessionHasErrors('phone');
    }

    /** @test */
    function phone_must_have_a_maximum_of_10_characters()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $customer = factory(Customer::class)->create();

        $response = $this->be($adminUser)->from(route('admin.customers.edit', $customer))->patch(route('admin.customers.update', $customer), $this->validParams([
            'phone' => str_random(11),
        ]));

        $response->assertRedirect(route('admin.customers.edit', $customer));
        $response->assertSessionHasErrors('phone');
    }

    /** @test */
    function cell_phone_is_required()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $customer = factory(Customer::class)->create();

        $response = $this->be($adminUser)->from(route('admin.customers.edit', $customer))->patch(route('admin.customers.update', $customer), $this->validParams([
            'cell_phone' => '',
        ]));

        $response->assertRedirect(route('admin.customers.edit', $customer));
        $response->assertSessionHasErrors('cell_phone');
    }

    /** @test */
    function cell_phone_must_have_a_maximum_of_10_characters()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $customer = factory(Customer::class)->create();

        $response = $this->be($adminUser)->from(route('admin.customers.edit', $customer))->patch(route('admin.customers.update', $customer), $this->validParams([
            'cell_phone' => str_random(11),
        ]));

        $response->assertRedirect(route('admin.customers.edit', $customer));
        $response->assertSessionHasErrors('cell_phone');
    }

    /** @test */
    function address_is_required()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $customer = factory(Customer::class)->create();

        $response = $this->be($adminUser)->from(route('admin.customers.edit', $customer))->patch(route('admin.customers.update', $customer), $this->validParams([
            'address' => '',
        ]));

        $response->assertRedirect(route('admin.customers.edit', $customer));
        $response->assertSessionHasErrors('address');
    }

    /** @test */
    function address_must_have_a_maximum_of_255_characters()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $customer = factory(Customer::class)->create();

        $response = $this->be($adminUser)->from(route('admin.customers.edit', $customer))->patch(route('admin.customers.update', $customer), $this->validParams([
            'address' => str_random(256),
        ]));

        $response->assertRedirect(route('admin.customers.edit', $customer));
        $response->assertSessionHasErrors('address');
    }

    /** @test */
    function birthdate_is_required()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $customer = factory(Customer::class)->create();

        $response = $this->be($adminUser)->from(route('admin.customers.edit', $customer))->patch(route('admin.customers.update', $customer), $this->validParams([
            'birthdate' => '',
        ]));

        $response->assertRedirect(route('admin.customers.edit', $customer));
        $response->assertSessionHasErrors('birthdate');
    }

    /** @test */
    function birthdate_must_be_a_valid_date()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $customer = factory(Customer::class)->create();

        $response = $this->be($adminUser)->from(route('admin.customers.edit', $customer))->patch(route('admin.customers.update', $customer), $this->validParams([
            'birthdate' => 'invalid-date',
        ]));

        $response->assertRedirect(route('admin.customers.edit', $customer));
        $response->assertSessionHasErrors('birthdate');
    }

    /** @test */
    function gender_is_required()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $customer = factory(Customer::class)->create();

        $response = $this->be($adminUser)->from(route('admin.customers.edit', $customer))->patch(route('admin.customers.update', $customer), $this->validParams([
            'gender' => '',
        ]));

        $response->assertRedirect(route('admin.customers.edit', $customer));
        $response->assertSessionHasErrors('gender');
    }

    /** @test */
    function gender_must_have_a_maximum_of_60_characters()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $customer = factory(Customer::class)->create();

        $response = $this->be($adminUser)->from(route('admin.customers.edit', $customer))->patch(route('admin.customers.update', $customer), $this->validParams([
            'gender' => str_random(61),
        ]));

        $response->assertRedirect(route('admin.customers.edit', $customer));
        $response->assertSessionHasErrors('gender');
    }

    /** @test */
    function routine_id_is_required()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $customer = factory(Customer::class)->create();

        $response = $this->be($adminUser)->from(route('admin.customers.edit', $customer))->patch(route('admin.customers.update', $customer), $this->validParams([
            'routine_id' => '',
        ]));

        $response->assertRedirect(route('admin.customers.edit', $customer));
        $response->assertSessionHasErrors('routine_id');
    }

    /** @test */
    function routine_id_must_be_exist()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $customer = factory(Customer::class)->create();

        $response = $this->be($adminUser)->from(route('admin.customers.edit', $customer))->patch(route('admin.customers.update', $customer), $this->validParams([
            'routine_id' => '999',
        ]));

        $response->assertRedirect(route('admin.customers.edit', $customer));
        $response->assertSessionHasErrors('routine_id');
    }

    /** @test */
    function level_id_is_required()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $customer = factory(Customer::class)->create();

        $response = $this->be($adminUser)->from(route('admin.customers.edit', $customer))->patch(route('admin.customers.update', $customer), $this->validParams([
            'level_id' => '',
        ]));

        $response->assertRedirect(route('admin.customers.edit', $customer));
        $response->assertSessionHasErrors('level_id');
    }

    /** @test */
    function level_id_must_be_exist()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $customer = factory(Customer::class)->create();

        $response = $this->be($adminUser)->from(route('admin.customers.edit', $customer))->patch(route('admin.customers.update', $customer), $this->validParams([
            'level_id' => '999',
        ]));

        $response->assertRedirect(route('admin.customers.edit', $customer));
        $response->assertSessionHasErrors('level_id');
    }
}
