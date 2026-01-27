<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Master_JenisPegawai extends Model
{
    use HasFactory, HasUuids;


    protected $table = 'siakad_master_jenis_pegawai';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';


    protected $fillable = ['kode_jenis_pegawai', 'nama_jenis_pegawai'];
}
