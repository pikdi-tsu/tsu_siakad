<?php

namespace App\Models\MasterData;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Master_SlotWaktu extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'siakad_master_slot_waktu';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'waktu',
    ];

    /**
     * Accessor untuk format jam cantik (07:30) tanpa detik
     */
    public function getWaktuFormattedAttribute()
    {
        return Carbon::parse($this->waktu)->format('H:i');
    }
}
