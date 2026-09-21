<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameBuilding extends Model
{
    protected $guarded = [];
    public function building()
    {
        return $this->belongsTo(Building::class);
    }
}
