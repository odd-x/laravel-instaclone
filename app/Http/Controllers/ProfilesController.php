<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Cache;
class ProfilesController extends Controller

{
    
    public function __construct()
    {
        $this->middleware('auth');    

    }
    public function index(User $user){
        $follows = (auth()->user()) ? auth()->user()->following->contains($user->id) : false;

        $PostsCount = Cache::remember('count.posts.'.$user->id, now()->addSeconds(30), function () use ($user) {
            return $user->posts->count();
        });

        $FollowersCount = Cache::remember('count.followers'.$user->id, now()->addSeconds(30), function () use ($user) {
            return $user->profile->followers->count();
        }); 

        $FollowingCount = Cache::remember('count.following'.$user->id, now()->addSeconds(30), function () use ($user) {
            return $user->following->count();
        });

        return view('profiles.index',compact('user', "follows","PostsCount","FollowersCount","FollowingCount"));
    }

    public function edit(User $user){
        $this->authorize('update',$user->profile);
        return view('profiles.edit',compact('user'));
    }
    
    public function update(User $user)
    {
        $data = request()->validate([
            'title'=>'required',
            'description'=>'required',
            'url'=>'url',
            'image'=>'',
        ]);
        
        if(request('image')){
            
            $imagePath = (request('image')->store('profile','public'));
            Image::make(public_path("storage/{$imagePath}"))
            ->fit(1000,1000)
            ->save();

        $imageArray = ['image'=>$imagePath];
        }

        // dd(array_merge($data,
        // ['image'=>$imagePath]
        // ));
        
        Auth()->user()->profile->update(array_merge(
            $data,
            $imageArray ?? []
        ));

        return redirect("/profile/{$user->id}");
    }
}
