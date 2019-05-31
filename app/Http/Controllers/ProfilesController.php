<?php

namespace App\Http\Controllers;

use App\Activity;
use App\User;
use Illuminate\Http\Request;

class ProfilesController extends Controller
{
    public function show(User $user)
    {


        $activities= $user->activity()->latest()->with('subject')->get()->groupBy(function($activity){
            return $activity->created_at->format('Y-m-d');
        });
        //return $activities;
        return view('profiles.show',[
            'profileUser'=>$user,
            'activities'=>Activity::feed($user),
        ]);
    }
}
