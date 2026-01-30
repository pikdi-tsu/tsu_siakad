<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Master_ProgramStudi extends Model
{
    use HasFactory, HasUuids;


    protected $table = 'siakad_master_program_studi';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['kode_prodi', 'nama_prodi', 'ketua_prodi', 'fakultas_id', 'status_prodi'];
}
