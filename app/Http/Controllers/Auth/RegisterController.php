<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SignupStandard;
use App\Models\SignupUser;
use App\Models\Standard;
use App\Services\OTPService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller implements HasMiddleware
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    protected $type = 'register';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('guest');
    }

    public static function middleware() {
        return [
            'guest'
        ];
    }

    public function showRegistrationForm(Request $request)
    {
        if ( ! $request->key ) {
            return redirect()->route('signup.key');
        }

        $key = SignupStandard::isValidKey($request->key);

        if ( ! $key ) {
            return redirect()->route('signup.key')->with('error', 'Signup Key not found.');
        }

        $languages = Standard::languages();

        $signup_key = $request->key;

        return view('auth.register', compact('key', 'languages', 'signup_key'));
    }

    public function send_otp(Request $request) {
        $data = $request->all();

        $this->validator($data)->validate();

        $user = SignupUser::where('email', '=', $data['email'])->first();

        $standard = Standard::where([
            'group' => $data['standard'],
            'language' => $data['language'],
        ])->first();

        if ( $user && $user->standards()->where('standard_id', $standard->id)->first() ) { // old user make sure user has not selected the same standard
            return redirect()->back()->withErrors(['standard' => 'You already have an account with selected standard.'])->withInput();
        }

        $otpService = new OTPService($this->type, $data);
        $otp = $otpService->generateOTP();

        session(['registered_user' => [
            ...$data,
            'otp' => $otp,
        ]]);

        return redirect()->route('register.verify.email');
    }

    public function resend_otp() {
        $user = (array) session('registered_user');

        if ( ! $user ) {
            return redirect()->route('signup.key')->with('error', 'Please enter details to continue.');
        }

        $otpService = new OTPService($this->type, $user);
        $otp = $otpService->generateOTP();

        session(['registered_user' => [
            ...$user,
            'otp' => $otp,
        ]]);

        return redirect()->route('register.verify.email');
    }

    public function verify() {
        $user = session('registered_user');

        if ( ! $user ) {
            return redirect()->route('signup.key')->with('error', 'Please enter details to continue.');
        }

        $type = 'register';

        return view('auth.otp-verify', compact('type'));
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            // 'email' => ['required', 'string', 'email', 'max:255', 'unique:signup_key'],
            // 'password' => ['required', 'string', 'min:8'],
            'address' => ['required'],
            'school_name' => ['required'],
            'language' => ['required'],
            'standard' => ['required'],
        ]);
    }

    public function register(Request $request)
    {
        $user = session('registered_user');

        if ( ! $user ) {
            return redirect()->route('signup.key')->with('error', 'Please enter details to continue.');
        }

        $request->validate([
            'otp' => ['required', 'min:6', 'max:6'],
        ]);

        $otpService = new OTPService($this->type, $user);
        $verified = $otpService->verifyOTP($request->otp);

        if ( ! $verified ) {
            return redirect()->back()->with('error', 'Invalid OTP.');
        }

        event(new Registered($user = $this->create()));

        session()->forget('registered_user');

        $this->guard()->login($user);

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        return $request->wantsJson()
                    ? new JsonResponse([], 201)
                    : redirect($this->redirectPath());
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create()
    {
        $data = session('registered_user');

        $signup_standard = SignupStandard::isValidKey($data['signup_key']);

        $standard = Standard::where([
            'group' => $data['standard'],
            'language' => $data['language'],
        ])->first();

        $user = SignupUser::where('email', $data['email'])->first();

        if ( ! $user ) {
            $user = new SignupUser;
        }

        $user->name = $data['name'];
        $user->city = $data['address'];
        $user->school = $data['school_name'];
        $user->email = $data['email'];
        // $user->password = Hash::make($data['password']);
        // $user->plain_password = $data['password'];
        $user->save();

        $signup_standard->signup_key_id = $user->id;
        $signup_standard->standard_id = $standard->id;
        $signup_standard->regd_date = now()->toDateString();
        $signup_standard->save();

        return $user;
    }

    public function signup_key() {
        return view('auth.signup_key');
    }

    public function signup_key_store(Request $request) {
        $data = $request->validate([
            'signup_key' => 'required'
        ]);

        $key = SignupStandard::isValidKey($data['signup_key']);

        if ( ! $key ) {
            return redirect()->back()->with('error', 'Signup Key not found.');
        }

        if ( $key->registered() ) {
            return redirect()->back()->with('error', 'Account already exists with given signup key. Please log in to continue.');
        }

        return redirect()->route('register', ['key' => $data['signup_key']]);
    }
}
