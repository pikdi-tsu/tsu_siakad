<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Master_RuangKuliah extends Model
{
    use HasFactory, HasUuids; // Aktifkan Trait UUID

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_ruang',
        'nama_ruang',
        'unit',
        'lokasi',
        'kapasitas',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'kapasitas' => 'integer',
    ];

    public function getTable()
    {
        return config('app.module.name') . '_master_ruang_kuliah';
    }
}
