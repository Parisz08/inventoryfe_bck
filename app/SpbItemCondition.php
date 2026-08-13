<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SpbItemCondition extends Model
{
    protected $table = 'spb_item_conditions';

    protected $fillable = [
        'spb_item_id', 'vendor_id', 'round', 'supplier', 'price', 'condition_note', 'selected', 'created_by',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function item()
    {
        return $this->belongsTo(SpbItem::class, 'spb_item_id');
    }
}