<?php

namespace App\Http\Controllers;

use App\Models\Standard;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

class HomeController extends Controller implements HasMiddleware
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    public static function middleware(): array
    {
        return [
            'auth',
            'auth.session',
        ];
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index($standard = null)
    {
        // check if valid standard if passed
        if (auth()->user() && $standard) {
            $standard = Standard::find($standard);
            if (! $standard) {
                $standard = null;
            }
        }

        // if standard is still null then check if user has 2 standard to redirect to standard page
        if (auth()->user() && is_null($standard)) {
            $standards = auth()->user()->standards();

            if ($standards->count() > 1) {
                return redirect()->route('user.choose.language');
            } else {
                return redirect()->route('home', $standards->first()->standard);
            }
        }

        if ($standard) {
            $covers = auth()->user()->covers_by_standard($standard);
        } else {
            $covers = auth()->user()->host->covers;
        }

        session(['standard' => $standard->id]);

        return view('home', compact('standard', 'covers'));
    }
}
