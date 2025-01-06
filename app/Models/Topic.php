<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = [
        'cover_id',
        'name',
        'sequence',
    ];

    public function cover() {
        return $this->belongsTo(Cover::class);
    }

    public function videos() {
        return $this->hasMany(Video::class);
    }

    public function audios() {
        return $this->hasMany(Audio::class);
    }

    public function worksheets() {
        return $this->hasMany(Worksheet::class);
    }

    public function tests() {
        return $this->hasMany(Test::class);
    }
}
