<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SkorHistory extends Model
{
    use HasFactory;

    protected $table = 'skor_histories';
    protected $fillable = ['kelompok_id', 'nilai'];
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
    public function kelompok()
    {
        return $this->belongsTo(Kelompok::class, 'kelompok_id');
    }
}
