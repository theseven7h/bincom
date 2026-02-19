<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PollingUnit extends Model
{
    protected $table = 'polling_unit';
    protected $primaryKey = 'uniqueid';
    public $timestamps = false;

    public function lga()
    {
        return $this->belongsTo(Lga::class, 'lga_id', 'lga_id');
    }

    public function ward()
    {
        return $this->belongsTo(Ward::class, 'ward_id', 'ward_id');
    }

    public function results()
    {
        return $this->hasMany(AnnouncedPuResult::class, 'polling_unit_uniqueid', 'uniqueid');
    }
}
