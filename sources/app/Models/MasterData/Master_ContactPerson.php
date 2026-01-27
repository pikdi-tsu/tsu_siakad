<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Master_ContactPerson extends Model
{
    use HasFactory, HasUuids;


    protected $table = 'siakad_master_contact_person';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';


    protected $fillable = ['nama', 'jenis_kelamin', 'no_telepon', 'alamat_email'];
}
