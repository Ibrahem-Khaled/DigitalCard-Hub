<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CurrencyService;

class CurrencyController extends Controller
{
    /**
     * Change user's selected currency
     */
    public function change(Request $request)
    {
        $request->validate([
            'currency' => 'required|string|in:USD,OMR,SAR,AED,EGP,KWD,QAR,BHD,EUR'
        ]);

        $currency = strtoupper($request->currency);
        
        // Store currency in session
        session(['currency' => $currency]);

        // Return back with success message
        return back()->with('success', 'تم تغيير العملة بنجاح');
    }
}

