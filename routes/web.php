<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Frontend\CoverController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PDFViewerController;
use App\Http\Controllers\StandardController;
use App\Models\SignupStandard;
use App\Models\SignupUser;
use App\Models\Standard;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Session\Middleware\AuthenticateSession;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();
Route::get('/', [HomeController::class, 'index'])->name('cover.home');
Route::get('/signup-key', [RegisterController::class, 'signup_key'])->name('signup.key');
Route::post('/signup-key', [RegisterController::class, 'signup_key_store'])->name('signup.key.store');
Route::post('send/email/otp', [RegisterController::class, 'send_otp'])->name('register.send.otp');
Route::post('resend/email/otp', [RegisterController::class, 'resend_otp'])->name('register.resend.otp');
Route::get('register/verify/email', [RegisterController::class, 'verify'])->name('register.verify.email');

Route::post('login/send/otp', [LoginController::class, 'send_otp'])->name('login.send.otp');
Route::post('login/resend/otp', [LoginController::class, 'resend_otp'])->name('login.resend.otp');
Route::get('login/verify', [LoginController::class, 'verify'])->name('login.verify.email');

Route::get('home', function () {
    if (auth()->user()) {
        return redirect()->route('user.choose.language');
    }

    return redirect('/');
});

Route::get('standards/{language}', [StandardController::class, 'by_language'])->name('standards.by.language');

Route::group(['as' => 'user.'], function () {
    Route::get('languages', [StandardController::class, 'choose_language'])->name('choose.language')->middleware(['auth', 'auth.session']);
    Route::get('standards/{language}', [StandardController::class, 'choose_standard'])->name('choose.standard')->middleware(['auth', 'auth.session']);

    Route::get('cover/{cover_code}', [CoverController::class, 'index'])->name('cover.index');
    Route::get('get_file_by_topic/{action}/{topic}', [CoverController::class, 'get_file_by_topic'])->name('cover.get.video_worksheet');
    Route::get('get_video/{video}', [CoverController::class, 'get_video'])->name('cover.get.video');

    Route::get('pdf_viewer/{id}', [PDFViewerController::class, 'index'])->name('pdf.viewer.index');
    Route::get('pdf_viewer/read/{type}/{id}', [PDFViewerController::class, 'read'])->name('pdf.viewer.read');
});

Route::get('clear-all-cache', function () {
    Artisan::call('optimize:clear');
    Artisan::call('view:cache');
});

Route::get('/home/{standard?}', [HomeController::class, 'index'])->name('home');

Route::get('fix-users', function () {
    $users = SignupUser::all();

    foreach ($users as $user) {
        $standard = Standard::where([
            'group' => $user->group,
            'language' => $user->language,
            'imageName' => $user->imageName,
        ])->first();

        SignupStandard::create([
            'signup_key_id' => !empty($user->name) ? $user->id : null,
            'userkey' => $user->userkey,
            'standard_id' => $standard->id ?? null,
            'regd_date' => $user->regd_date === '0000-00-00' ? null : $user->regd_date,
            'end_date' => $user->end_date,
        ]);
    }
});
