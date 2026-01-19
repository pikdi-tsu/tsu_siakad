<?php

namespace App\Models\MasterData;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Master_PeriodeAkademik extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'siakad_master_periode_akademik';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_periode',
        'nama_periode',
        'tgl_awal_kuliah', 'tgl_akhir_kuliah',
        'tgl_awal_uts', 'tgl_akhir_uts',
        'tgl_awal_uas', 'tgl_akhir_uas',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tgl_awal_kuliah' => 'date', 'tgl_akhir_kuliah' => 'date',
    ];

    // Accessor Format Tanggal (8 Mar 2031)
    public function getTglAwalKuliahFormattedAttribute()
    {
        return $this->tgl_awal_kuliah ? Carbon::parse($this->tgl_awal_kuliah)->translatedFormat('d M Y') : '-';
    }

    public function getTglAkhirKuliahFormattedAttribute()
    {
        return $this->tgl_akhir_kuliah ? Carbon::parse($this->tgl_akhir_kuliah)->translatedFormat('d M Y') : '-';
    }
}
