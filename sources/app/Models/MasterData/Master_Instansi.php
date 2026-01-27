<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Master_Instansi extends Model
{
    use HasFactory, HasUuids;


    protected $table = 'siakad_master_instansi';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';


    protected $fillable = ['no', 'nama_instansi', 'alamat', 'no_telepon'];
}
