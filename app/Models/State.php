<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $table = 'states';
    protected $primaryKey = 'state_id';
    public $timestamps = false;

    public function lgas()
    {
        return $this->hasMany(Lga::class, 'state_id', 'state_id');
    }
}
