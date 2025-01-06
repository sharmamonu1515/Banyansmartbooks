<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'menu_name',
        'file_name',
        'sequence',
    ];

    public function topic() {
        return $this->belongsTo(Topic::class, 'topic_id');
    }

    public function url() {
        return $this->topic->cover->host->bucket . "/{$this->topic->cover->host->name}/{$this->topic->cover->fld}/{$this->file_name}";
    }
}
