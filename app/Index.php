<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Index extends Model
{   
    protected $table = 'indexs';
    protected $fillable = ['word_id','document_id','amount'];
    protected $hidden = ['created_at','update_at'];
    public static function getAllIndexsByWordId($id){
        return self::where('word_id',$id)->get();
    }
}
