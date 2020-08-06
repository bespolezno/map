<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    public $timestamps = false;
    protected $fillable = ['from_place_id', 'to_place_id', 'hash'];
}
