<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Host extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'm_host_id',
        'bucket',
        'access_key_id',
        'secret_access_key',
        'website',
        'status',
        'pgfootertxt',
        'pgfooterurl'
    ];

    public function covers() {
        return $this->hasMany(Cover::class);
    }

    public function promo_image_url() {
        return $this->bucket . '/WebImgs/' . $this->name . '_Promo.jpg';
    }

    public function logo_image_url() {
        return $this->bucket . '/WebImgs/' . $this->name . '.png';
    }

    public function parent_host() {
        return $this->belongsTo(self::class, 'm_host_id');
    }
}
