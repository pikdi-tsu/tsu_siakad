<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;

class NeoFeederProdi extends Model
{
    protected $table = 'neo_feeder_prodis';
    protected $guarded = [];
    public $incrementing = false;
    protected $keyType = 'string';
}
