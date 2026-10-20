<?php

namespace App\Http\Middleware;

use App\Services\CurrencyService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Stevebauman\Location\Facades\Location;
use Symfony\Component\HttpFoundation\Response;

class DetectCurrency
{
    protected $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Honor a user-selected currency and skip auto-detection
        if (Session::get('currency_manual')) {
            return $next($request);
        }

        // Always try to detect currency based on current IP
        try {
            $ip = $request->ip();
            // For local development, use a default
            if ($ip === '127.0.0.1' || $ip === '::1') {
                $countryCode = 'IN'; // Default to India for dev
            } else {
                $location = Location::get($ip);
                $countryCode = $location ? $location->countryCode : 'IN';
            }

            $currency = $this->currencyService->getCurrencyForCountry($countryCode);
            Session::put('currency', $currency);
            Session::put('country', $countryCode);
        } catch (\Exception $e) {
            // Default to INR if detection fails
            Session::put('currency', 'INR');
            Session::put('country', 'IN');
        }

        return $next($request);
    }
}
