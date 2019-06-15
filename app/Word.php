<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Word extends Model
{
    protected $fillable = ['name'];
    protected $hidden = ['created_at','update_at'];

    public static function getIdByWord($termino){
        return self::where('name',$termino)->get()->first();
    }
}
