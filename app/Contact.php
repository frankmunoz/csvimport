<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    public $fillable = ['csv_data_id','status','name', 'birthdate', 'phone', 'address', 'credit_card', 'franchise', 'email'];
}
