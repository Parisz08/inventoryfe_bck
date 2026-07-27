<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SpbCondition extends Model
{
    protected $table = 'spb_conditions';

    protected $fillable = [
        'spb_id', 'vendor_id', 'round', 'supplier', 'price', 'condition_note', 'selected', 'created_by',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function spb()
    {
        return $this->belongsTo(Spb::class, 'spb_id');
    }
}
