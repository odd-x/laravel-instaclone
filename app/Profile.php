<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $guarded = [];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function profileImage(){
        $imagePath = ($this->image) ?  $this->image : '/profile/TVINquNOa6nBQILuegZFVRec2mX03CIdMxmueCpN.png';
        return '/storage/' . $imagePath;
    }

    public function followers()
    {
        return $this->belongsToMany(User::class);
    }

}
