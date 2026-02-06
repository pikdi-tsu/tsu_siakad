<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Master_KalenderAkademik extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_kegiatan',
        'id_periode',
        'tgl_mulai',
        'tgl_selesai',
        'keterangan',
        'is_libur_nasional',
        'is_libur_akademik',
    ];

    protected $casts = [
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date',
        'is_libur_nasional' => 'boolean',
        'is_libur_akademik' => 'boolean',
    ];

    public function getTable()
    {
        return config('app.module.name') . '_kalender_akademik';
    }

    // Relasi ke Master Kegiatan
    public function kegiatan()
    {
        return $this->belongsTo(Master_KegiatanAkademik::class, 'id_kegiatan');
    }

    // Accessor: Format Tanggal Cantik (e.g., 17 Agu 2025)
    public function getTglMulaiFormattedAttribute()
    {
        return Carbon::parse($this->tgl_mulai)->translatedFormat('d M Y');
    }

    public function getTglSelesaiFormattedAttribute()
    {
        return Carbon::parse($this->tgl_selesai)->translatedFormat('d M Y');
    }
}
