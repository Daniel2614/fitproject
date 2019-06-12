<?php

namespace Tests\Unit\Entities;

use App\VitalGym\Entities\Level;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class LevelTest extends TestCase
{
    /** @test */
    function a_level_has_many_customers()
    {
        $level = factory(Level::class)->make();

        $this->assertInstanceOf(Collection::class, $level->customers);
    }
}
