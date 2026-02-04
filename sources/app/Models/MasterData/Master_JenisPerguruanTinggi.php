<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Master_JenisPerguruanTinggi extends Model
{
    use HasFactory;

    protected $table = 'siakad_master_jenis_perguruan_tinggi';

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'jenis_pt',
        'isactive',
    ];
}
