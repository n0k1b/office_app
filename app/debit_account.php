<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class debit_account extends Model
{
    //
    protected $fillable = ['user_id','debit_type_id','debited_to_id','note','amount','month','date','year'];
}
