<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audio extends Model
{
    use HasFactory;

    public $table = 'audios';

    protected $fillable = [
        'topic_id',
        'menu_name',
        'file_name',
        'sequence',
    ];

    public function topic() {
        return $this->belongsTo(Topic::class);
    }
}
