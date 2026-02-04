<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Master_DataPerguruanTinggi extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_unit',
        'nama_unit',
        'nama_unit_en',
        'nama_singkat',
        'jenis_perguruan_tinggi',
        'lembaga_naungan',
        'unit_satuan_kerja',
        'periode_berdiri',
        'no_sk_pendirian',
        'tanggal_sk_pendirian',
        'rektor',
        'wakil_rektor1',
        'wakil_rektor2',
        'wakil_rektor3',
        'wakil_rektor4',
        'lembaga_akreditasi',
        'peringkat_akreditasi',
        'nilai_akreditasi',
        'no_sk_akreditasi',
        'tanggal_sk_akreditasi',
        'tanggal_berlaku_akreditasi',
        'tanggal_berakhir_akreditasi',
        'file_sertifikat_akreditasi',
        'visi',
        'misi',
        'alamat',
        'telepon',
        'alamat_email',
        'alamat_website',
        'fax'
    ];

    public function getTable()
    {
        return config('app.module.name') . '_master_data_perguruan_tinggi';
    }
}
