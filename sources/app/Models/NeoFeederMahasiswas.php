<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class NeoFeederMahasiswas extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = ['id'];

    // Auto UUID saat create
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function getTable()
    {
        return 'neo_feeder_mahasiswas';
    }

    /**
     * Parsing Tanggal Lahir (Karena JSON Feeder formatnya dd-mm-yyyy)
     */
    public function setTanggalLahirAttribute($value)
    {
        // Jika format input "16-11-1998", ubah jadi "1998-11-16" untuk MySQL
        try {
            $this->attributes['tanggal_lahir'] = Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d');
        } catch (\Exception $e) {
            // Fallback kalau format sudah Y-m-d
            $this->attributes['tanggal_lahir'] = $value;
        }
    }

    public function getTanggalLahirAttribute($value)
    {
        return Carbon::parse($value);
    }

    /**
     * 🔥 STATUS SYNC CHECKER 🔥
     * Dianggap Sync kalau punya ID Feeder DAN ID Registrasi
     */
    public function getIsSyncedAttribute()
    {
        return !is_null($this->id_registrasi_mahasiswa_feeder);
    }

    // Helper Gender
    public function getNamaKelaminAttribute()
    {
        return $this->jenis_kelamin === 'L' ? 'Laki - Laki' : 'Perempuan';
    }
}
