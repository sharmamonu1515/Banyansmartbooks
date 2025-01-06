<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Standard extends Model
{
    use HasFactory;

    protected $fillable = [
        'group',
        'language',
        'imageName',
        'dealer',
        'sequence'
    ];

    public static function languages() {
        return self::groupBy('language')->pluck('language');
    }

    public static function byLanguage($language) {
        return self::where('language', $language)->get();
    }

    public function signup_standards() {
        return $this->hasMany(SignupStandard::class, 'standard_id');
    }

}
