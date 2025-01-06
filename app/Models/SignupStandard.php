<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SignupStandard extends Model
{
    use HasFactory;

    protected $table = 'signup_standard';

    protected $fillable = [
        'signup_key_id',
        'standard_id',
        'userkey',
        'regd_date',
        'end_date',
        'host_id',
        'm_host_id',
        'dealer',
    ];

    public function standard() {
        return $this->belongsTo(Standard::class);
    }

    public function host() {
        return $this->belongsTo(Host::class);
    }

    public function user() {
        return $this->belongsTo(SignupUser::class);
    }

    public static function isValidKey($key) {
        return self::whereRaw('BINARY userkey = ?', [$key])->first();
    }

    public function registered() {
        return ! is_null($this->signup_key_id);
    }

    public function image() {
        return $this->host->bucket . "/{$this->host->name}/{$this->standard->imageName}";
    }

    public function covers() {
        return $this->hasMany(Cover::class, 'm_host_id', 'm_host_id');
    }

}
