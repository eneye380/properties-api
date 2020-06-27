<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    //
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description',
    ];

    public function properties()
    {
        return $this->belongsToMany('App\Property');
    }
}
