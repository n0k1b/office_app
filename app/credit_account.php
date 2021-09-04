<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class credit_account extends Model
{
    //
    
    protected $fillable = ['user_id','credit_type_id','note','amount','month','date','year'];
}
