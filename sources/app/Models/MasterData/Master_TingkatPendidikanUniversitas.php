<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Master_TingkatPendidikanUniversitas extends Model
{
    use HasFactory, HasUuids;


    protected $table = 'siakad_master_tingkat_pendidikan_universitas';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';


    protected $fillable = ['jenjang', 'masa_studi', 'max_cuti', 'max_studi'];
}
