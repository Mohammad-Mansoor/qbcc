<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = ['name', 'location', 'type', 'is_active', 'subtype'];

    public function getSubtypeFaAttribute()
    {
        $map = [
            'carpet' => 'قالین',
            'yarn'   => 'تار',
            'dye'    => 'رنگ',
        ];
        return $map[$this->subtype] ?? $this->subtype;
    }
}

