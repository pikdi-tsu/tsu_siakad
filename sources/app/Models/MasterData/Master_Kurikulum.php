<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Master_Kurikulum extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'siakad_master_kurikulum';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nama_kurikulum',
        'deskripsi',
        'isactive',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'isactive' => 'boolean',
    ];
}
