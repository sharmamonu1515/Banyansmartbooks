<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cover extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'host_id',
        'status',
        'fld',
        'm_host_id',
        'group',
        'group_index',
        'cover_index',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function host() {
        return $this->belongsTo(Host::class);
    }

    public function topics() {
        return $this->hasMany(Topic::class);
    }

    public function image_url() {
        return $this->host->bucket . "/{$this->host->name}/CoverPg/{$this->host->id}{$this->id}.jpg";
    }

    public static function parent_cover(Cover $cover) {
        return Cover::where([
            'fld' => $cover->fld,
            'host_id' => $cover->m_host_id
        ])->first();
    }

    public function parent_host() {
        return $this->belongsTo(Host::class, 'm_host_id');
    }
}
