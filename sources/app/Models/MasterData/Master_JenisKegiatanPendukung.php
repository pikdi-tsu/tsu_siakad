<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Master_JenisKegiatanPendukung extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'siakad_master_jenis_kegiatan_pendukung';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_kegiatan',
        'nama_kegiatan',
        'is_system',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];
}
