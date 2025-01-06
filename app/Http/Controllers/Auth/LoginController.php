<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SignupUser;
use App\Services\OTPService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller implements HasMiddleware
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    protected $type = 'login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('guest')->except('logout');
        // $this->middleware('auth')->only('logout');
    }

    public static function middleware()
    {
        return [
            (new Middleware('guest'))->except('logout'),
            (new Middleware('auth'))->only('logout'),
        ];
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => ['required', 'string', 'email', 'exists:signup_key'],
        ]);
    }

    public function send_otp(Request $request)
    {
        $data = $request->all();

        $this->validator($data)->validate();

        $otpService = new OTPService($this->type, $data);
        $otp = $otpService->generateOTP();

        session(['login_user' => [
            'email' => $data['email'],
            'otp' => $otp,
        ]]);

        return redirect()->route('login.verify.email');
    }

    public function resend_otp()
    {
        $user = (array) session('login_user');

        if (! $user) {
            return redirect()->route('login')->with('error', 'Please enter email address to login.');
        }

        $otpService = new OTPService($this->type, $user);
        $otp = $otpService->generateOTP();

        session(['login_user' => [
            'email' => $user['email'],
            'otp' => $otp,
        ]]);

        return redirect()->route('login.verify.email');
    }

    public function verify()
    {
        $user = session('login_user');

        if (! $user) {
            return redirect()->route('login')->with('error', 'Please enter email address to login.');
        }

        $type = 'login';

        return view('auth.otp-verify', compact('type'));
    }

    public function login(Request $request)
    {
        $data = session('login_user');

        if (! $data) {
            return redirect()->route('login')->with('error', 'Please enter email address to login.');
        }

        // validate user email
        $email = $data['email'];

        $user = SignupUser::where('email', $email)->first();
        if (! $user) {
            return redirect()->route('login')->with('error', 'No user found with given email address.');
        }

        // validate request
        $request->validate([
            'otp' => ['required', 'min:6', 'max:6'],
        ]);

        $otpService = new OTPService($this->type, $user->toArray());
        $verified = $otpService->verifyOTP($request->otp);

        if (! $verified) {
            return redirect()->back()->with('error', 'Invalid OTP.');
        }

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (
            method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)
        ) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $user->update([
            'password' => bcrypt('123')
        ]);

        Auth::logoutOtherDevices(123);

        Auth::login($user);

        if ($request->hasSession()) {
            $request->session()->put('auth.password_confirmed_at', time());
        }

        if (auth()->user()) {
            return $this->sendLoginResponse($request);
        } else {
            // If the login attempt was unsuccessful we will increment the number of attempts
            // to login and redirect the user back to the login form. Of course, when this
            // user surpasses their maximum number of attempts they will get locked out.
            $this->incrementLoginAttempts($request);
        }

        return $this->sendFailedLoginResponse($request);
    }
}
