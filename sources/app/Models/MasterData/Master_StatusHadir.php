<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Master_StatusHadir extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'siakad_master_status_hadir';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_status',
        'nama_status',
        'is_hitung_hadir',
        'is_untuk_dosen',
        'is_untuk_mahasiswa',
    ];

    protected $casts = [
        'is_hitung_hadir'    => 'boolean',
        'is_untuk_dosen'     => 'boolean',
        'is_untuk_mahasiswa' => 'boolean',
    ];
}
