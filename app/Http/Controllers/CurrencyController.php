<?php

namespace App\Http\Controllers;

use App\Services\CurrencyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CurrencyController extends Controller
{
    /**
     * Allow users to manually switch the display currency.
     */
    public function switch(Request $request, CurrencyService $currencyService): RedirectResponse
    {
        $request->validate([
            'currency' => 'required|string|size:3',
        ]);

        $currency = strtoupper($request->input('currency'));
        $supported = $currencyService->getSupportedCurrencies();

        if (!array_key_exists($currency, $supported)) {
            return redirect()->back()->with('warning', 'Selected currency is not supported yet.');
        }

        Session::put('currency', $currency);
        Session::put('currency_manual', true); // Prevent auto-detect from overriding the choice

        return redirect()->back();
    }
}
