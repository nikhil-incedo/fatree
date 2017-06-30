<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use App\Relation;

class SearchController extends Controller
{
    private $family;
    /*
    |--------------------------------------------------------------------------
    | Search Controller
    |--------------------------------------------------------------------------
    */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

	/**
     *
     * @return array
     */
    public function search()
    {
		return view('search', []);
    }

    /**
     *
     * @return array
     */
    public function match()
    {
        $users = Relation::where('related_type', 'father')->get();
        //echo '<pre>'; print_r($users->where('related_type', 'father')); echo '</pre>';die;
        // echo '<pre>';
        // foreach($users AS $user) {
        //     print_r($user);
        //     //print_r($user->primaryUser);
        //     die;
        // }
        // die;
        return view('match', ['users'=>$users]);
    }

    public function makeSpouse() {
        $lvl = 2;
        $marrieds = Relation::where('related_type', 'spouse')->get();

        foreach($marrieds AS $married) {
            $marriedMales[] = $married->user_id;
            $marriedFemales[] = $married->related_id;
        }

        $singleMales = User::whereNotIn('id', $marriedMales)->where('level', $lvl)->get();
        $singleFemales = User::whereNotIn('id', $marriedFemales)->where('level', $lvl)->get();
        $gotMarried = array();

        foreach($singleMales AS $singleMale) {
            foreach($singleFemale AS $singleFemale) {
                if(!in_array($singleFemale->id, $gotMarried)) {
                    if($this->canMarry($singleMale, $singleFemale)) {
                        $this->marryThem($singleMale->id, $singleFemale->id);
                        $gotMarried[] = $singleFemale->id;
                    }
                }
            }
        }

        // echo '<pre>'; print_r($singleMales);
        // die;

        // $singles = Relation:: where('related_type', '!=', 'spouse')->where('related_type', 'father')->get();
        // //echo '<pre>'; print_r($singleMales[0]);die;
        // foreach($singles AS $single) {
        //     //print_r($singleMale->primaryUser->gender);
        //     if($single->primaryUser->gender == 'M') {
        //         $singleMales[] = $single->primaryUser->id;
        //         echo $single->primaryUser->first_name . ' - ' . $single->primaryUser->gender;
        //         echo '<br />';
        //     }
        // }
    }

    private function canMarry($maleId, $femaleId) {
        $this->family = [];
        $maleFamily = $this->getUpFamilyIds($male);
    }

    private function getUpFamilyIds($id) {
        $relations = Relation::where('user_id', $id)->select('related_id')->get();
        foreach($relations AS $relation) {
            $parentId = $relation->related_id;
            $this->family[] = $parentId;
            $this->getUpFamilyIds($parentId);
        }

    }
}
