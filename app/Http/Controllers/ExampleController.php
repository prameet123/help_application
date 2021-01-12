<?php

namespace App\Http\Controllers;

use App\Models\User;

class ExampleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function category_list()
    {
        $user = User::where(['status'=>'active','email'=>'someone@gmail.com'])->get();
        if ($user->count()==0) {
            $data = ['name' => 'Abigail', 'state' => 'CA'];
        } else {
            $data = $user->toArray();
        }
        return response()->json($data);
    }

    //
}
