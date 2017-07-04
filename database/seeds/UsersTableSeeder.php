<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		for($i=1;$i<=20;$i++){
			$a = ['gender'=>'F'];
			if($i%2 == 1) {
				$a = ['gender' => 'M'];
			}
            $a['level'] = 1;
			factory(User::class)->create($a);
		}
		//factory(Account::class)->create($account);
        //$this->call(UsersTableSeeder::class);
    }
}
