<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\Property::class, function (Faker\Generator $faker) {
    $edu = array('10th','12th','BA','BCom','BSC','BTech','MA','MCom','MSC','MTech');
	return [
        'estimate' => $faker->regexify('[0-9]{4,9}'),
        'bank' => rand(1, 4),
        'education' => $edu[rand(0, 9)],
    ];

});

