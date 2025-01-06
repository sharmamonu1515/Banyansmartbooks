<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'menu_name',
        'file_name',
        'ref_id',
        'sequence',
    ];

    public function topic() {
        return $this->belongsTo(Topic::class);
    }
}
