<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class SignupUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'signup_key';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'userkey',
        'host_id',
        'm_host_id',
        'group',
        'language',
        'regd_date',
        'end_date',
        'name',
        'school',
        'city',
        'email',
        'password',
        'dealer',
        'plain_password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function host()
    {
        $standard = $this->getSelectedStandard();
        return $this->standards()->where('standard_id', $standard)->first()->host;
    }

    public function standards()
    {
        return $this->hasMany(SignupStandard::class, 'signup_key_id');
    }

    public function get_standards_by_language($language)
    {
        return $this->standards()
            ->join('standards', 'standards.id', '=', 'signup_standard.standard_id')
            ->where('standards.language', $language)
            ->select('signup_standard.*', 'standards.sequence', 'standards.language')
            ->orderBy('standards.language')
            ->orderBy('standards.sequence')
            ->get();
    }

    public function covers()
    {
        return $this->hasMany(Cover::class, 'm_host_id', 'm_host_id');
    }

    public function covers_by_standard(Standard $standard)
    {
        $signupStandard = $this->standards()->where('standard_id', $standard->id)->first();
        return $signupStandard->covers()->where([
            'group' => $standard->group,
            'language' => $standard->language,
        ])->get();
    }

    public function getSelectedStandard()
    {
        if (session('standard')) {
            return session('standard');
        } else {
            return $this->standards()->first()->standard_id;
        }
    }

    public function languages()
    {
        return $this->standards()
            ->join('standards', 'standards.id', '=', 'signup_standard.standard_id')
            ->select('standards.language')
            ->distinct()
            ->orderBy('standards.language')
            ->get();
    }
}
