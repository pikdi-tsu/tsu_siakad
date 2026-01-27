<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Master_TingkatPendidikan extends Model
{
    use HasFactory, HasUuids;


    protected $table = 'siakad_master_tingkat_pendidikan';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';


    protected $fillable = [
        'jenjang',
        'nama_jenjang_pendidikan',
        'nama_jenjang_pendidikan_en',
        'urutan_jenjang_pendidikan',
        'perguruan_tinggi',
        'pasca_sarjana',
        'jenjang_rpl'
    ];
}
