<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Master_Pekerjaan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'siakad_master_pekerjaan';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_pekerjaan',
        'nama_pekerjaan',
    ];
}
