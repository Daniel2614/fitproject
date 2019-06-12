<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Admin User
        factory(\App\VitalGym\Entities\User::class)->states('admin', 'active')->create([
            'name' => 'John',
            'last_name' => 'Doe',
            'email' => 'admin@example.com',
            'password' => bcrypt('secret'),
        ]);

        //Admin Users
        factory(\App\VitalGym\Entities\User::class)->states('admin', 'active')->times(20)->create();

        //Levels
        $levels = collect([
            factory(\App\VitalGym\Entities\Level::class)->create(['name' => 'General']),
            factory(\App\VitalGym\Entities\Level::class)->create(['name' => 'Principiante']),
            factory(\App\VitalGym\Entities\Level::class)->create(['name' => 'Medio']),
            factory(\App\VitalGym\Entities\Level::class)->create(['name' => 'Avanzado']),
            factory(\App\VitalGym\Entities\Level::class)->create(['name' => 'Experto']),
        ]);

        //Routines
        $routines = collect([
            factory(\App\VitalGym\Entities\Routine::class)->create(['level_id' => $levels->random()->id]),
            factory(\App\VitalGym\Entities\Routine::class)->create(['level_id' => $levels->random()->id]),
            factory(\App\VitalGym\Entities\Routine::class)->create(['level_id' => $levels->random()->id]),
            factory(\App\VitalGym\Entities\Routine::class)->create(['level_id' => $levels->random()->id]),
        ]);

        //Customers
        factory(\App\VitalGym\Entities\Customer::class, 10)->create(['level_id' => $levels->random()->id, 'routine_id' => $routines->random()->id]);

        //Membership types
        factory(\App\VitalGym\Entities\Plan::class)->create(['name' => 'diario', 'price' => 200]);
        factory(\App\VitalGym\Entities\Plan::class)->create(['name' => 'mensual', 'price' => 3000]);
        factory(\App\VitalGym\Entities\Plan::class)->create(['name' => 'trimestral', 'price' => 8000]);
        factory(\App\VitalGym\Entities\Plan::class)->create(['name' => 'Semestral', 'price' => 16000]);
        factory(\App\VitalGym\Entities\Plan::class)->create(['name' => 'mensual', 'price' => 32000]);
    }
}
