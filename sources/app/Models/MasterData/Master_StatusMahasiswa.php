<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Master_StatusMahasiswa extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'siakad_master_status_mahasiswa';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_status',
        'nama_status',
        'is_pengajuan_mhs',
        'is_aktif',
        'is_sks',
        'is_system',
    ];

    protected $casts = [
        'is_pengajuan_mhs' => 'boolean',
        'is_aktif'         => 'boolean',
        'is_sks'           => 'boolean',
        'is_system'        => 'boolean',
    ];
}
