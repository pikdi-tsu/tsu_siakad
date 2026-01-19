<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Master_UnsurNilai extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'siakad_master_unsur_nilai';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_unsur',
        'nama_unsur',
        'nama_singkat',
        'kelompok_unsur',
        'metode_evaluasi',
    ];
}
