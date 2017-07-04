<?php

use App\User;
use App\Property;
use Illuminate\Database\Seeder;

class PropertiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$users = User::get();
        foreach ($users as $key => $user) {
            $a['user_id'] = $user->id;
            factory(Property::class)->create($a);
        }
    }
}
