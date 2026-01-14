<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Master_KelompokMataKuliah extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'siakad_master_kelompok_matakuliah';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_kelompok',
        'nama_kelompok',
    ];
}
