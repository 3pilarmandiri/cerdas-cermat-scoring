<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Kelompok extends Model
{
    use HasFactory;

    protected $table = 'kelompoks';
    protected $fillable = ['pertandingan_id', 'kode', 'nama_peserta', 'total_skor'];
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /** 🔗 Relasi */
    public function pertandingan()
    {
        return $this->belongsTo(Pertandingan::class, 'pertandingan_id');
    }

    public function skorHistories()
    {
        return $this->hasMany(SkorHistory::class, 'kelompok_id');
    }
}
