<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataMahasiswa extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'siakad_data_mahasiswas';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'nim',
        'id_prodi',
        'id_jenjang',
        'id_waktu_kuliah',
        'angkatan',
        'status_akademik', // AKTIF, CUTI, etc
        'jalur_masuk',

        // Data Pribadi
        'nik_ktp',
        'nisn',
        'tempat_lahir',
        'tgl_lahir',
        'jenis_kelamin', // L/P
        'agama',
        'no_hp',
        'email_pribadi',

        // Wilayah
        'id_provinsi',
        'id_kabupaten',
        'alamat_lengkap',
        'kodepos',

        // Ortu
        'nama_ayah',
        'nama_ibu',
        'no_hp_ortu',
    ];

    protected $casts = [
        'tgl_lahir' => 'date',
    ];

    // Ke User (Induk)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
