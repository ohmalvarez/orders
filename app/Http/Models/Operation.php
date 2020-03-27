<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Operation extends Model
{
    //
    protected $table = "operation";
    public $timestamps = false;

    public function scopeBytype($query,$type){
        return $query->where('type',$type);
    }

    public function  scopeByorderid($query,$id){
        return $query->where('id_order',$id);
    }
}
