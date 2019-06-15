<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = ['name'];
    protected $hidden = ['created_at','update_at'];
}
