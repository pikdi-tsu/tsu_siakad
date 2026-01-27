<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Master_StatusKeaktifan extends Model
{
    use HasFactory, HasUuids;


    protected $table = 'siakad_master_status_keaktifan';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';


    protected $fillable = ['nama_status_keaktifan', 'status_keluar'];
}
