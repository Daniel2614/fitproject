<?php

namespace Tests\Unit\Entities;

use App\VitalGym\Entities\ActivationToken;
use App\VitalGym\Entities\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_can_create_a_user_with_account_activation_token()
    {
        $data = [
            'name' =>'John',
            'last_name' => 'Dor',
            'avatar' => 'default-avatar',
            'email' =>'john@example.com',
            'phone' => '0698741256',
            'cell_phone' => '09635874125',
            'address' => 'My Address',
            'role' => 'customer',
        ];

        $user = User::createWithActivationToken($data);

        $this->assertEquals($data['name'], $user->name);
        $this->assertEquals($data['last_name'], $user->last_name);
        $this->assertEquals($data['avatar'], $user->avatar);
        $this->assertEquals($data['email'], $user->email);
        $this->assertEquals($data['phone'], $user->phone);
        $this->assertEquals($data['cell_phone'], $user->cell_phone);
        $this->assertEquals($data['address'], $user->address);
        $this->assertEquals($data['role'], $user->role);
        $this->assertEquals(1, ActivationToken::where('user_id', $user->id)->count());
    }
}
