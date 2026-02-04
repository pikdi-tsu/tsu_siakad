<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Master_PeringkatAkreditasi extends Model
{
    use HasFactory;

    protected $table = 'siakad_master_peringkat_akreditasi';

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'peringkat_akreditasi',
        'isactive',
    ];
}
