<?php

use App\User;
use App\Relation;
use Illuminate\Database\Seeder;

class ChildTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$relations = Relation::where('related_type', 'spouse')->get();
				
		foreach($relations AS $rel) {
			$numChildren = rand(0,4);
			
			$users = User::where('id', $rel->user_id)->first();
			//dd($users);
			
			$arr['last_name'] = $users->last_name;
			//dd($arr);
			if($numChildren > 0) {
				factory(User::class, $numChildren)->create($arr)->each(function($u) use($rel) {
					//dd($rel);
					$relArr['user_id'] = $u->id;
					$relArr['related_id'] = $rel->user_id;
					$relArr['related_type'] = 'father';
					factory(Relation::class)->create($relArr);
					
					$relArr['related_id'] = $rel->related_id;
					$relArr['related_type'] = 'mother';
					factory(Relation::class)->create($relArr);
				});
				
			}
		}
		//factory(Account::class)->create($account);
        //$this->call(UsersTableSeeder::class);
    }
}