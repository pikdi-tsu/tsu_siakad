<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Master_JabatanFungsional extends Model
{
    use HasFactory, HasUuids;


    protected $table = 'siakad_master_jabatan_fungsional';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';


    protected $fillable = ['kode_jabatan_fungsional', 'nama_jabatan_fungsional', 'sks_maksimal'];
}
