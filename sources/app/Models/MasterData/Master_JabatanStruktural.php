<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Master_JabatanStruktural extends Model
{
    use HasFactory, HasUuids;


    protected $table = 'siakad_master_jabatan_struktural';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';


    protected $fillable = ['nama_jabatan_struktural', 'parent_id'];
}
