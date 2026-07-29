<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $counter=10;
        
        for($i=0;$i<=$counter;$i++)
        {
            User::factory()->create([
                'email' => $i.fake()->unique()->safeEmail()
            ]);
        }
    }
}
