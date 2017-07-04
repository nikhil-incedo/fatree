<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use App\Relation;
use Faker\Generator as Faker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    private $family;
    public $relation;
    public $userDtl;
    private $marriedWomen = [];
    private $relatedUsers = [];
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
        $this->relation = new Relation;
        //$user = new User;
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
        $lvl = 3;
        $marrieds = Relation::where('related_type', 'spouse')->get();

        foreach($marrieds AS $married) {
            $marriedMales[] = $married->user_id;
            $marriedFemales[] = $married->related_id;
        }

        $singleMales = User::whereNotIn('id', $marriedMales)->where('level', $lvl)->where('gender', 'M')->get();
        $singleFemales = User::whereNotIn('id', $marriedFemales)->where('level', $lvl)->where('gender', 'F')->get();
        $gotMarried = array();

        foreach($singleMales AS $singleMale) {
            foreach($singleFemales AS $singleFemale) {
                if(!in_array($singleFemale->id, $gotMarried)) {
                    if($this->canMarry($singleMale->id, $singleFemale->id)) {
                        $this->marryThem($singleMale->id, $singleFemale->id);
                        $this->marriedWomen[] = $singleFemale->id;
                        break;
                    }
                }
            }
        }

    }

    private function canMarry($maleId, $femaleId) {
        $this->family = [];
        if($this->isMarried($femaleId)) return false;

        $maleFamily = $this->getUpFamilyIds($maleId);
        $this->family = [];
        $femaleFamily = $this->getUpFamilyIds($femaleId);
        $this->family = [];

        if(count(array_intersect($maleFamily, $femaleFamily)) > 0) {
            return false;
        }else {
            return true;
        }
    }

    private function getUpFamilyIds($id) {
        //echo 'a' . $id . 'b';
        $relations = Relation::where('user_id', $id)
            ->whereIn('related_type', ['father', 'mother'])
            ->select('related_id')
            ->get();

        foreach($relations AS $relation) {
            $parentId = $relation->related_id;
            $this->family[] = $parentId;
            $this->getUpFamilyIds($parentId);
        }
        return $this->family;
    }

    private function isMarried($id) {
        if(in_array($id, $this->marriedWomen)) return true;

        $isMarried = Relation::where('related_type', 'spouse')
            ->where(function($q) use ($id){
                $q->where('user_id', $id)
                ->orWhere('related_id', $id);
            })->count();

        if($isMarried > 0) {
            return true;
        }else {
            return false;
        }
    }

    function marryThem($maleId, $femaleId) {
        $insert = new Relation;
        $insert->user_id = $maleId;
        $insert->related_id = $femaleId;
        $insert->related_type = 'spouse';
        $insert->save();
    }

    public function makeChild(Faker $faker) {
        $lvl = 4;
        //DB::enableQueryLog();
        $marrieds = Relation::join('users', 'relations.user_id', '=', 'users.id')
                    ->where('level', $lvl-1)
                    ->where('related_type', 'spouse')
                    ->get();
        //dd($marrieds);
        foreach($marrieds AS $married) {
            $numChildren = rand(0,4);
            $users = User::where('id', $married->user_id)->first();
            $genders[0] = 'M';
            $genders[1] = 'F';

            if($numChildren > 0) {
                for($i=0;$i<$numChildren;$i++){
                    $genderNum = rand(0,1);
                    $g  = $genders[$genderNum];
                    $arr = new User;
                    $arr->first_name = ($g=='M')?$faker->firstNameMale:$faker->firstNameFemale;
                    $arr->last_name  = $users->last_name;
                    $arr->gender     = $g;
                    $arr->mobile     = $faker->unique()->regexify('[789]{1}[0-9]{9}');
                    $arr->pan_card   = $faker->regexify('[A-Z]{5}[0-9]{4}[A-Z]{1}');
                    $arr->aadhar_no  = $faker->regexify('[0-9]{12}');
                    $arr->address    = $faker->address;
                    $arr->level      = $lvl;
                    $arr->save();

                    $relArr = new Relation;
                    $relArr->user_id = $arr->id;
                    $relArr->related_id = $married->user_id;
                    $relArr->related_type = 'father';
                    $relArr->save();

                    $relArr = new Relation;
                    $relArr->user_id = $arr->id;
                    $relArr->related_id = $married->related_id;
                    $relArr->related_type = 'mother';
                    $relArr->save();
                }
            }
        }
    }

    public function searchByAadhar(Request $request) {
        if ($request->aadhar) {
            $this->userDtl = User::where('aadhar_no', $request->aadhar)->first();
            $userId = $this->userDtl->id;
            $parents = $this->getParentsId();
            //print_r($parents); die;
            if(sizeof($parents) > 0) {
                //echo $parents[0];
                foreach($parents AS $parent) {
                    $this->relatedUsers[] = $parent;
                }
             }else {
                $this->relatedUsers[] = $userId;

                $maleSpouse = Relation::where('related_id', $userId)
                    ->where('related_type', 'spouse')->first();
                if(!empty($maleSpouse)) {
                    $this->relatedUsers[] = $maleSpouse->user_id;
                }else {
                    $femaleSpouse = Relation::where('user_id', $userId)
                    ->where('related_type', 'spouse')->first();

                    if(!empty($femaleSpouse)) {
                        $this->relatedUsers[] = $femaleSpouse->related_id;
                    }
                }
            }
            $this->getChildrenId($this->relatedUsers[0]);
            sort($this->relatedUsers);
            array_unique($this->relatedUsers);
            //echo "<pre>"; print_r($this->relatedUsers); echo "</pre>";
            $userDetails = User::whereIn('id', $this->relatedUsers)->get();
            //echo '<pre>'; print_r($userDetails[1]); echo '</pre>';
            return view('family', ['userDetails' => $userDetails]);
            // foreach($userDetails AS $user) {
            //     echo $user->id . ': ' . $user->first_name . ' ' .$user->last_name;
            // }
            // die('asd');
            // echo '<pre>'; print_r($this->relatedUsers->User->first_name); die('asd');

            // if($this->userDtl->id){
            //     $res['user'] = $this->userDtl;
            //     $res['parents'] = $this->relation->where('user_id', $this->userDtl->id)->parents()->get();
            //     $res['spouse'] = $this->relation->where('user_id', $this->userDtl->id)->orWhere('related_id', $this->userDtl->id)->spouse()->get();
            //     $res['childs'] = $this->relation->where('related_id', $this->userDtl->id)->parents()->get();
            // }
        }

        die('User Not Found');
        //return view('family', ['res' => $res, 'as'=>'hello']);
    }

    private function getParentsId() {
        $res = $this->relation->select('related_id')->where('user_id', $this->userDtl->id)->parents()->get();
        if(sizeof($res) > 0) {
            foreach($res AS $value) {
                $ret[] = $value->related_id;
            }
            return $ret;
        }
        return;
    }

    private function getChildrenId($parentId) {
        $childs = $this->relation->where('related_id', $parentId)->parents()->get();
        if(sizeof($childs) > 0) {
            foreach($childs AS $child) {
                $this->relatedUsers[] = $child->user_id;
                $maleSpouse = Relation::where('related_id', $child->user_id)
                    ->where('related_type', 'spouse')->first();
                if(!empty($maleSpouse)) {
                    //echo 'Male <br />';
                    $this->relatedUsers[] = $maleSpouse->user_id;
                }else {
                    $femaleSpouse = Relation::where('user_id', $child->user_id)
                    ->where('related_type', 'spouse')->first();

                    if(!empty($femaleSpouse)) {
                        //echo 'Female <br />';
                        $this->relatedUsers[] = $femaleSpouse->related_id;
                    }
                }

                $this->getChildrenId($child->user_id);
            }

        }else {
            return;
        }
               //echo '<pre>'; print_r($this->relatedUsers); echo '</pre>';
    }

}
