<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pertandingan extends Model
{
    use HasFactory;

    protected $table = 'pertandingans';
    protected $fillable = ['nama', 'keterangan'];
    public $incrementing = false;
    protected $keyType = 'string';

    // Otomatis generate UUID
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /** 🔗 Relasi */
    public function kelompoks()
    {
        return $this->hasMany(Kelompok::class, 'pertandingan_id');
    }
}
