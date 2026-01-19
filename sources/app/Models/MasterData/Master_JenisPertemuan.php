<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Master_JenisPertemuan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'siakad_master_jenis_pertemuan';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_jenis',
        'nama_jenis',
        'nama_singkat',
        'is_hitung_presensi',
        'is_ujian',
        'kelompok_jenis',
    ];

    protected $casts = [
        'is_hitung_presensi' => 'boolean',
        'is_ujian' => 'boolean',
    ];
}
