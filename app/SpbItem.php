<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SpbItem extends Model
{
    protected $table = 'spb_items';

    protected $fillable = [
        'spb_id', 'material_code', 'kategori', 'material_name', 'merek', 'specification', 'qty', 'unit', 'note',
    ];

    public function spb()
    {
        return $this->belongsTo(Spb::class, 'spb_id');
    }
}