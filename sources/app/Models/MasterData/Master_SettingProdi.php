<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Master_SettingProdi extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'siakad_master_setting_prodi';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = [];

    // Casting boolean agar mudah di view
    protected $casts = [
        'is_biodata' => 'boolean',
        'is_krs' => 'boolean',
        'is_validasi_krs' => 'boolean',
        'is_cetak_krs' => 'boolean',
        'is_khs' => 'boolean',
        'is_nilai' => 'boolean',
        'is_kuesioner' => 'boolean',
        'is_generate_pertemuan' => 'boolean',
    ];
}
