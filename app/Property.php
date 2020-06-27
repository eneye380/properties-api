<?php

namespace App;

use App\User;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{

    protected $fillable = [
        'name','type','homes',
        'units','spaces','description',
        'state','lga','town','address',
        'latitude','longitude'
    ];

    public function users()
    {
        return $this->belongsToMany('App\User')->withPivot('abode')->withTimestamps();
    }

    public function assets()
    {
        return $this->belongsToMany('App\Asset');
    }
}
