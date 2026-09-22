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
    public function getEmployeeRequiredToNextLevel(): int
    {
        $now = $this->building->getEmployees($this->level);
        $nextLevel = $this->building->getEmployees($this->level + 1);
        return $nextLevel - $now;
    }
}
