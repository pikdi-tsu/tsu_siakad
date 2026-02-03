<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;

class Master_ProgramStudi extends Model
{
    protected $guarded = ['id'];

    protected $fillable = [
        //
    ];

    public function getTable()
    {
        return config('app.module.name') . '_menu_program_studi';
    }
}
