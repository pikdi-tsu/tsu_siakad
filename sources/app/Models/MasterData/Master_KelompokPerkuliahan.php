<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Master_KelompokPerkuliahan extends Model
{
    use HasFactory, HasUuids;


    protected $table = 'siakad_master_kelompok_perkuliahan';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';


    protected $fillable = ['nama_kelompok_perkuliahan', 'urutan'];
}
