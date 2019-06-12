<?php

namespace  Tests;

use App\VitalGym\Entities\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        Hash::driver('bcrypt')->setRounds(4);

        return $app;
    }

    public function createNewUser($attributes = [])
    {
        $userData = [
            'name' => 'John',
            'last_name' => 'Doe',
            'email'      => 'tavo198718@gmail.com',
            'password'   => bcrypt('secret'),
            'active'     => true,
            'role'       => 'admin',
        ];

        if ($attributes) {
            return factory(User::class)->create($attributes);
        }

        return factory(User::class)->create($userData);
    }
}
