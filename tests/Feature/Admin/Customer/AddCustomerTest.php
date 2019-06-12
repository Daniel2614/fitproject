<?php

namespace Tests\Feature\Admin\Customer;

use App\Mail\CustomerWelcomeEmail;
use App\VitalGym\Entities\ActivationToken;
use App\VitalGym\Entities\Customer;
use App\VitalGym\Entities\Level;
use App\VitalGym\Entities\Routine;
use App\VitalGym\Entities\User;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddCustomerTest extends TestCase
{
    use RefreshDatabase;

    private $file;
    private $routine;
    private $level;

    public function setUp()
    {
        parent::setUp();

        Mail::fake();
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
            'password' => 'secret',
            'confirmation_password' => 'secret',
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
    function an_admin_can_view_the_form_to_create_a_customer()
    {
        $this->withoutExceptionHandling();

        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $levels = factory(Level::class)->times(3)->create();
        $routines = factory(Routine::class)->times(3)->create(['level_id' => $levels->random()->id]);

        $response = $this->be($adminUser)->get(route('admin.customers.create'));

        $response->assertSuccessful();
        $response->assertViewIs('admin.customers.create');
        $levels->assertEquals($response->data('levels'));
        $routines->assertEquals($response->data('routines'));
    }

    /** @test */
    function an_admin_can_create_a_new_customer()
    {
        $this->withoutExceptionHandling();
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->post(route('admin.customers.store'), $this->validParams());

        $response->assertRedirect(route('admin.customers.index'));
        $customer = Customer::first();
        $this->assertEquals('John', $customer->name);
        $this->assertEquals('Doe', $customer->last_name);
        $this->assertNotNull($customer->avatar);
        Storage::disk('public')->assertExists($customer->avatar);
        $this->assertFileEquals($this->file->getPathname(), Storage::disk('public')->path($customer->avatar));
        $this->assertEquals('john@example.com', $customer->email);
        $this->assertTrue(Hash::check('secret', $customer->user->password));
        $this->assertEquals('0926687856', $customer->ci);
        $this->assertEquals('2695755', $customer->user->phone);
        $this->assertEquals('0123456789', $customer->user->cell_phone);
        $this->assertEquals('Fake address', $customer->user->address);
        $this->assertEquals('customer', $customer->user->role);
        $this->assertFalse((boolean) $customer->user->active);
        $this->assertEquals('1987-12-09', $customer->birthdate->toDateString());
        $this->assertEquals('masculino', $customer->gender);
        $this->assertEquals('Problemas del corazón', $customer->medical_observations);
        $this->assertEquals($this->routine->id, $customer->routine->id);
        $this->assertEquals($this->level->id, $customer->level->id);
        $this->assertTrue( (boolean) $customer->has('user') );
        $this->assertEquals(1, Customer::count());
        $response->assertSessionHas('alert-type', 'success');
        $response->assertSessionHas('message');

        $this->assertInstanceOf(ActivationToken::class,  $customer->user->token);

        Mail::assertQueued(CustomerWelcomeEmail::class, function ( $mail ) use ( $customer ) {
           return $mail->hasTo('john@example.com')
                  && $mail->customer->id = $customer->id;
        });
    }

    /** @test */
    function name_is_required()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.customers.create'))->post(route('admin.customers.store'), $this->validParams([
            'name' => ''
        ]));

        $response->assertRedirect(route('admin.customers.create'));
        $response->assertSessionHasErrors('name');
        $this->assertEquals(0, Customer::count());
        Mail::assertNotQueued(CustomerWelcomeEmail::class);
    }

    /** @test */
    function name_must_have_a_maximum_of_80_characters()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.customers.create'))->post(route('admin.customers.store'), $this->validParams([
            'name' => str_random(81)
        ]));

        $response->assertRedirect(route('admin.customers.create'));
        $response->assertSessionHasErrors('name');
        $this->assertEquals(0, Customer::count());
        Mail::assertNotQueued(CustomerWelcomeEmail::class);
    }

    /** @test */
    function last_name_is_required()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.customers.create'))->post(route('admin.customers.store'), $this->validParams([
            'last_name' => ''
        ]));

        $response->assertRedirect(route('admin.customers.create'));
        $response->assertSessionHasErrors('last_name');
        $this->assertEquals(0, Customer::count());
        Mail::assertNotQueued(CustomerWelcomeEmail::class);
    }

    /** @test */
    function last_name_must_have_a_maximum_of_100_characters()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.customers.create'))->post(route('admin.customers.store'), $this->validParams([
            'last_name' => str_random(101)
        ]));

        $response->assertRedirect(route('admin.customers.create'));
        $response->assertSessionHasErrors('last_name');
        $this->assertEquals(0, Customer::count());
        Mail::assertNotQueued(CustomerWelcomeEmail::class);
    }

    /** @test */
    function email_is_required()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.customers.create'))->post(route('admin.customers.store'), $this->validParams([
            'email' => ''
        ]));

        $response->assertRedirect(route('admin.customers.create'));
        $response->assertSessionHasErrors('email');
        $this->assertEquals(0, Customer::count());
        Mail::assertNotQueued(CustomerWelcomeEmail::class);
    }

    /** @test */
    function email_must_be_a_valid_email()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.customers.create'))->post(route('admin.customers.store'), $this->validParams([
            'email' => 'invalid-email'
        ]));

        $response->assertRedirect(route('admin.customers.create'));
        $response->assertSessionHasErrors('email');
        $this->assertEquals(0, Customer::count());
        Mail::assertNotQueued(CustomerWelcomeEmail::class);
    }

    /** @test */
    function email_must_be_unique()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $oldUser = factory(User::class)->states('customer')->create(['email' => 'john@example.com']);
        $oldCustomer = factory(Customer::class)->create(['user_id' => $oldUser->id]);

        $response = $this->be($adminUser)->from(route('admin.customers.create'))->post(route('admin.customers.store'), $this->validParams([
            'email' => $oldUser->email,
        ]));

        $response->assertRedirect(route('admin.customers.create'));
        $response->assertSessionHasErrors('email');
        $this->assertEquals(0, Customer::whereNotIn('id', [$oldCustomer->id])->count());
        Mail::assertNotQueued(CustomerWelcomeEmail::class);
    }

    /** @test */
    function password_is_required()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.customers.create'))->post(route('admin.customers.store'), $this->validParams([
            'password' => ''
        ]));

        $response->assertRedirect(route('admin.customers.create'));
        $response->assertSessionHasErrors('password');
        $this->assertEquals(0, Customer::count());
        Mail::assertNotQueued(CustomerWelcomeEmail::class);
    }

    /** @test */
    function password_must_have_a_minimum_of_6_characters()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.customers.create'))->post(route('admin.customers.store'), $this->validParams([
            'password' => '12345',
            'confirmation_password' => '12345',
        ]));

        $response->assertRedirect(route('admin.customers.create'));
        $response->assertSessionHasErrors('password');
        $this->assertEquals(0, Customer::count());
        Mail::assertNotQueued(CustomerWelcomeEmail::class);
    }

    /** @test */
    function password_must_be_equals_to_password_confirmation_field()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.customers.create'))->post(route('admin.customers.store'), $this->validParams([
            'password' => 'secret',
            'confirmation_password' => 'other-password',
        ]));

        $response->assertRedirect(route('admin.customers.create'));
        $response->assertSessionHasErrors('password');
        $this->assertEquals(0, Customer::count());
        Mail::assertNotQueued(CustomerWelcomeEmail::class);
    }

    /** @test */
    function ci_is_optional()
    {
        $this->withoutExceptionHandling();
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $response = $this->be($adminUser)->from(route('admin.customers.create'))->post(route('admin.customers.store'), $this->validParams([
            'ci' => ''
        ]));

        $response->assertRedirect(route('admin.customers.index'));

        $this->assertEquals(1, Customer::count());
        $response->assertSessionHas('alert-type', 'success');
        $response->assertSessionHas('message');
    }

    /** @test */
    function ci_must_be_a_valid_ci()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.customers.create'))->post(route('admin.customers.store'), $this->validParams([
            'ci' => '123456'
        ]));

        $response->assertRedirect(route('admin.customers.create'));
        $response->assertSessionHasErrors('ci');
        $this->assertEquals(0, Customer::count());
        Mail::assertNotQueued(CustomerWelcomeEmail::class);
    }

    /** @test */
    function ci_must_be_unique()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $otherCustomer = factory(Customer::class)->create(['ci' => '0926687856']);

        $response = $this->be($adminUser)->from(route('admin.customers.create'))->post(route('admin.customers.store'), $this->validParams([
            'ci' => '0926687856'
        ]));

        $response->assertRedirect(route('admin.customers.create'));
        $response->assertSessionHasErrors('ci');
        $this->assertEquals(0, Customer::whereNotIn('id', [$otherCustomer->id])->count());
        Mail::assertNotQueued(CustomerWelcomeEmail::class);
    }

    /** @test */
    function avatar_is_optional()
    {
        $this->withoutExceptionHandling();
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $response = $this->be($adminUser)->from(route('admin.customers.create'))->post(route('admin.customers.store'), $this->validParams([
            'avatar' => ''
        ]));

        $response->assertRedirect(route('admin.customers.index'));

        $this->assertEquals(1, Customer::count());
        $response->assertSessionHas('alert-type', 'success');
        $response->assertSessionHas('message');
    }

    /** @test */
    function avatar_must_be_an_image()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.customers.create'))->post(route('admin.customers.store'), $this->validParams([
            'avatar' => File::create('no-image.pdf'),
        ]));

        $response->assertRedirect(route('admin.customers.create'));
        $response->assertSessionHasErrors('avatar');
        $this->assertEquals(0, Customer::count());
        Mail::assertNotQueued(CustomerWelcomeEmail::class);
    }

    /** @test */
    function avatar_must_have_a_maximum_of_1024_kilobytes()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.customers.create'))->post(route('admin.customers.store'), $this->validParams([
            'avatar' => File::image('avatar.jpg')->size(1025),
        ]));

        $response->assertRedirect(route('admin.customers.create'));
        $response->assertSessionHasErrors('avatar');
        $this->assertEquals(0, Customer::count());
        Mail::assertNotQueued(CustomerWelcomeEmail::class);
    }

    /** @test */
    function phone_is_required()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.customers.create'))->post(route('admin.customers.store'), $this->validParams([
            'phone' => '',
        ]));

        $response->assertRedirect(route('admin.customers.create'));
        $response->assertSessionHasErrors('phone');
        $this->assertEquals(0, Customer::count());
        Mail::assertNotQueued(CustomerWelcomeEmail::class);
    }

    /** @test */
    function phone_must_have_a_maximum_of_10_characters()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.customers.create'))->post(route('admin.customers.store'), $this->validParams([
            'phone' => str_random(11),
        ]));

        $response->assertRedirect(route('admin.customers.create'));
        $response->assertSessionHasErrors('phone');
        $this->assertEquals(0, Customer::count());
        Mail::assertNotQueued(CustomerWelcomeEmail::class);
    }

    /** @test */
    function cell_phone_is_required()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.customers.create'))->post(route('admin.customers.store'), $this->validParams([
            'cell_phone' => '',
        ]));

        $response->assertRedirect(route('admin.customers.create'));
        $response->assertSessionHasErrors('cell_phone');
        $this->assertEquals(0, Customer::count());
        Mail::assertNotQueued(CustomerWelcomeEmail::class);
    }

    /** @test */
    function cell_phone_must_have_a_maximum_of_10_characters()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.customers.create'))->post(route('admin.customers.store'), $this->validParams([
            'cell_phone' => str_random(11),
        ]));

        $response->assertRedirect(route('admin.customers.create'));
        $response->assertSessionHasErrors('cell_phone');
        $this->assertEquals(0, Customer::count());
        Mail::assertNotQueued(CustomerWelcomeEmail::class);
    }

    /** @test */
    function address_is_required()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.customers.create'))->post(route('admin.customers.store'), $this->validParams([
            'address' => '',
        ]));

        $response->assertRedirect(route('admin.customers.create'));
        $response->assertSessionHasErrors('address');
        $this->assertEquals(0, Customer::count());
        Mail::assertNotQueued(CustomerWelcomeEmail::class);
    }

    /** @test */
    function address_must_have_a_maximum_of_255_characters()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.customers.create'))->post(route('admin.customers.store'), $this->validParams([
            'address' => str_random(256),
        ]));

        $response->assertRedirect(route('admin.customers.create'));
        $response->assertSessionHasErrors('address');
        $this->assertEquals(0, Customer::count());
        Mail::assertNotQueued(CustomerWelcomeEmail::class);
    }

    /** @test */
    function birthdate_is_required()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.customers.create'))->post(route('admin.customers.store'), $this->validParams([
            'birthdate' => '',
        ]));

        $response->assertRedirect(route('admin.customers.create'));
        $response->assertSessionHasErrors('birthdate');
        $this->assertEquals(0, Customer::count());
        Mail::assertNotQueued(CustomerWelcomeEmail::class);
    }

    /** @test */
    function birthdate_mst_be_a_valid_date()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.customers.create'))->post(route('admin.customers.store'), $this->validParams([
            'birthdate' => 'invalid-birthdate',
        ]));

        $response->assertRedirect(route('admin.customers.create'));
        $response->assertSessionHasErrors('birthdate');
        $this->assertEquals(0, Customer::count());
        Mail::assertNotQueued(CustomerWelcomeEmail::class);
    }

    /** @test */
    function gender_is_required()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.customers.create'))->post(route('admin.customers.store'), $this->validParams([
            'gender' => '',
        ]));

        $response->assertRedirect(route('admin.customers.create'));
        $response->assertSessionHasErrors('gender');
        $this->assertEquals(0, Customer::count());
        Mail::assertNotQueued(CustomerWelcomeEmail::class);
    }

    /** @test */
    function gender_must_have_a_maximum_of_60_characters()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.customers.create'))->post(route('admin.customers.store'), $this->validParams([
            'gender' => str_random(61),
        ]));

        $response->assertRedirect(route('admin.customers.create'));
        $response->assertSessionHasErrors('gender');
        $this->assertEquals(0, Customer::count());
        Mail::assertNotQueued(CustomerWelcomeEmail::class);
    }

    /** @test */
    function routine_id_must_be_exist()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.customers.create'))->post(route('admin.customers.store'), $this->validParams([
            'routine_id' => 2,
        ]));

        $response->assertRedirect(route('admin.customers.create'));
        $response->assertSessionHasErrors('routine_id');
        $this->assertEquals(0, Customer::count());
        Mail::assertNotQueued(CustomerWelcomeEmail::class);
    }

    /** @test */
    function medical_observation_is_optional()
    {
        $this->withoutExceptionHandling();
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $response = $this->be($adminUser)->from(route('admin.customers.create'))->post(route('admin.customers.store'), $this->validParams([
            'medical_observations' => null
        ]));

        $response->assertRedirect(route('admin.customers.index'));

        $this->assertEquals(1, Customer::count());
        $response->assertSessionHas('alert-type', 'success');
        $response->assertSessionHas('message');
    }

    /** @test */
    function level_id_must_be_exist()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.customers.create'))->post(route('admin.customers.store'), $this->validParams([
            'level_id' => 9999,
        ]));

        $response->assertRedirect(route('admin.customers.create'));
        $response->assertSessionHasErrors('level_id');
        $this->assertEquals(0, Customer::count());
        Mail::assertNotQueued(CustomerWelcomeEmail::class);
    }
}
