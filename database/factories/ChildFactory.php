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

$factory->define(App\User::class, function (Faker\Generator $faker) {
	$genders[0] = 'M';
	$genders[1]	= 'F';
	$genderNum = rand(0,1);
	$g	= $genders[$genderNum];
	
	if($g == 'M'){
		$f = $faker->firstNameMale;
	}else {
		$f = $faker->firstNameFemale;
	}
	
    return [
        'first_name' => $f,
        'gender' => $g,
        'mobile' => $faker->unique()->regexify('[789]{1}[0-9]{9}'),
        'pan_card' => $faker->regexify('[A-Z]{5}[0-9]{4}[A-Z]{1}'),
        'aadhar_no' => $faker->regexify('[0-9]{12}'),
        'address' => $faker->address
    ];
});