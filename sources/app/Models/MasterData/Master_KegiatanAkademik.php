<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Master_KegiatanAkademik extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_kegiatan',
        'nama_kegiatan',
        'warna_background',
    ];

    public function getTable()
    {
        return config('app.module.name') . '_master_kegiatan_akademik';
    }
}
