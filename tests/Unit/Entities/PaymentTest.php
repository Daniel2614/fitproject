<?php

namespace Tests\Unit\Entities;

use Tests\TestCase;
use App\VitalGym\Entities\User;
use App\VitalGym\Entities\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function a_payment_belongs_to_a_user()
    {
        $payment = factory(Payment::class)->create();

        $this->assertInstanceOf(
            User::class, $payment->user
        );
    }
}
