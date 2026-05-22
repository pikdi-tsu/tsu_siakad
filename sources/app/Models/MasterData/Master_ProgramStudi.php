<?php

namespace App\Models\MasterData;

use App\Models\DataMahasiswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Master_ProgramStudi extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        //
    ];

    public function getTable()
    {
        return config('app.module.name') . '_master_program_studi';
    }

    public function prodiMahasiswa()
    {
        return $this->hasOne(DataMahasiswa::class, 'id_prodi');
    }
}
