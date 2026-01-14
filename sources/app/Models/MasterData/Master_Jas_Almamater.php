<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Master_Jas_Almamater extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'siakad_master_jas_almamater';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_ukuran',
        'nama_ukuran',
    ];
}
