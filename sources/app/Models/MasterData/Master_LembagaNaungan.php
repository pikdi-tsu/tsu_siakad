<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Master_LembagaNaungan extends Model
{
    use HasFactory;

    protected $table = 'siakad_master_lembaga_naungan';

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nama_lembaga',
        'isactive',
    ];
}
