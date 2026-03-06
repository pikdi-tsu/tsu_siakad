<?php

namespace App\Models\MasterData;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Master_TahunAjaran extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'siakad_tahun_ajaran';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nama_tahun_ajaran',
        'tahun_mulai',
        'tahun_selesai', 'created_at',
        'created_by', 'updated_at',
        'updated_by'
    ];

    public function periode_akademik(){
        return $this->hasMany('App\Models\MasterData\Master_PeriodeAkademik', 'id_tahunajaran','id')->where('isactive','1');
    }
}
