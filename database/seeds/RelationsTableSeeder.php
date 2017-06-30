<?php

use App\Relation;
use Illuminate\Database\Seeder;

class RelationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$j = 0;
		for($i=1;$i<=10;$i++){
			$a = array(
				'user_id'=>++$j,
				'related_id'=>++$j,
			);
			
			factory(Relation::class)->create($a);
		}
    }
}
