<?php

namespace App\Http\Controllers;

use App\Models\Cover;
use App\Models\SignupStandard;
use App\Models\SignupUser;
use App\Models\Standard;
use Illuminate\Http\Request;

class StandardController extends Controller
{
    public function by_language($language)
    {
        $standards = Standard::byLanguage($language)->pluck('group');

        return response()->json([
            'success' => true,
            'standards' => $standards,
        ]);
    }

    public function choose_language(Request $request)
    {
        $languages = auth()->user()->languages();

        if ($languages->count() === 1) {
            return redirect()->route('user.choose.standard', $languages->first()->language);
        }

        return view('choose-language', compact('languages'));
    }

    public function choose_standard(Request $request, $language)
    {
        $standards = auth()->user()->get_standards_by_language($language);

        if ($standards->count() === 1) {
            return redirect()->route('home', $standards->first()->standard);
        }

        return view('choose-standards', compact('standards'));
    }
}
