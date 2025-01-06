<?php

namespace App\Services;

use App\Mail\SendOtpEmail;
use App\Models\Otp;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OTPService
{

    public function __construct(public $type, public $data = [])
    {

    }

    public function generateOTP()
    {
        // delete all older otp generated against this email address
        Otp::where('email', $this->data['email'])->delete();

        $otp = mt_rand(100000, 999999);

        Otp::create([
            'email' => $this->data['email'],
            'otp' => $otp,
            'is_valid' => true,
        ]);

        $this->sendOTP($this->data['email'], $otp);

        return $otp;
    }

    private function sendOTP($email, $otp)
    {
        Mail::to($email)->send(new SendOtpEmail($this->type, $otp, $this->data));
    }

    public function verifyOTP($otp)
    {
        $otpRecord = Otp::where('email', $this->data['email'])
            ->where('otp', $otp)
            ->where('is_valid', true)
            ->where('created_at', '>', now()->subMinutes(10))
            ->first();

        if ($otpRecord) {
            $otpRecord->update(['is_valid' => false]);
            return true;
        }

        return false;
    }
}
