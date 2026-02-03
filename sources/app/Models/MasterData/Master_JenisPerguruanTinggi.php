<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Master_JenisPerguruanTinggi extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'siakad_master_jenis_perguruan_tinggi';
    protected $primaryKey = 'id';
    // public $incrementing = false;
    // protected $keyType = 'string';

    // protected $fillable = [
    //     'kode_agama',
    //     'nama_agama',
    // ];
}
