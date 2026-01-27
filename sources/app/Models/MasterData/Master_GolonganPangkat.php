<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Master_GolonganPangkat extends Model
{
    use HasFactory, HasUuids;


    protected $table = 'siakad_master_golongan_pangkat';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';


    protected $fillable = ['kode_golongan_pangkat', 'nama_golongan_pangkat'];
}
