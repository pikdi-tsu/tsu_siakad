<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Master_LokasiKampus extends Model
{
    use HasFactory, HasUuids;


    protected $table = 'siakad_master_lokasi_kampus';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';


    protected $fillable = ['kode', 'nama', 'alamat', 'telepon'];
}
