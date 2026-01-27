<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Master_Gedung extends Model
{
    use HasFactory, HasUuids;


    protected $table = 'siakad_master_gedung';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';


    protected $fillable = ['kode_gedung', 'nama_gedung', 'lokasi_kampus', 'telepon', 'jml_lantai', 'jml_ruang'];
}
