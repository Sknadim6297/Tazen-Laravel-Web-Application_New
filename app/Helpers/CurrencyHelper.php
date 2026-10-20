<?php

use App\Services\CurrencyService;
use Illuminate\Support\Facades\Session;

if (!function_exists('convertCurrency')) {
    function convertCurrency($amount)
    {
        if (!is_numeric($amount) || $amount === null) {
            return 0;
        }
        $currency = Session::get('currency', 'INR');
        $service = app(CurrencyService::class);
        return $service->convert($amount, $currency);
    }
}

if (!function_exists('getCurrencySymbol')) {
    function getCurrencySymbol()
    {
        $currency = Session::get('currency', 'INR');
        $service = app(CurrencyService::class);
        $currencies = $service->getSupportedCurrencies();
        return $currencies[$currency]['symbol'] ?? '₹';
    }
}

if (!function_exists('getCurrentCurrency')) {
    function getCurrentCurrency()
    {
        return Session::get('currency', 'INR');
    }
}

if (!function_exists('formatPrice')) {
    function formatPrice($amount)
    {
        if (!is_numeric($amount) || $amount === null) {
            return 'Contact for pricing';
        }
        $converted = convertCurrency($amount);
        $symbol = getCurrencySymbol();
        return $symbol . ' ' . number_format($converted, 2);
    }
}

if (!function_exists('formatPriceRange')) {
    function formatPriceRange($min, $max = null)
    {
        $symbol = getCurrencySymbol();
        $currency = getCurrentCurrency();
        
        if ($currency === 'INR') {
            // For INR, use original values
            if ($max === null) {
                return "Under {$symbol}{$min}";
            } elseif ($max === '+') {
                return "{$symbol}{$min}+";
            } else {
                return "{$symbol}{$min} - {$symbol}{$max}";
            }
        }
        
        // Convert to current currency
        $convertedMin = convertCurrency($min);
        if ($max === null) {
            return "Under {$symbol}" . number_format($convertedMin, 2);
        } elseif ($max === '+') {
            return "{$symbol}" . number_format($convertedMin, 2) . "+";
        } else {
            $convertedMax = convertCurrency($max);
            return "{$symbol}" . number_format($convertedMin, 2) . " - {$symbol}" . number_format($convertedMax, 2);
        }
    }
}