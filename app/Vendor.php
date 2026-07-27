<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $table = 'vendors';

    protected $fillable = [
        'name', 'product', 'pic', 'phone', 'payment_term', 'email', 'address',
    ];
}