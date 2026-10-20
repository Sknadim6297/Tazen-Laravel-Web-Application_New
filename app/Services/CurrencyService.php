<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CurrencyService
{
    protected $baseCurrency = 'INR';
    protected $apiUrl;
    protected $cacheDuration;

    public function __construct()
    {
        $this->apiUrl = config('services.currency.api_url', 'https://api.exchangerate-api.com/v4/latest/INR');
        $this->cacheDuration = config('services.currency.cache_duration', 3600);
    }

    public function getExchangeRate($toCurrency)
    {
        if ($toCurrency === $this->baseCurrency) {
            return 1;
        }

        $cacheKey = "exchange_rate_{$toCurrency}";

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($toCurrency) {
            try {
                $response = Http::timeout(5)->get($this->apiUrl);
                if ($response->successful()) {
                    $data = $response->json();
                    $rate = $data['rates'][$toCurrency] ?? null;
                    if ($rate && $rate > 0) {
                        return $rate;
                    }
                }
            } catch (\Exception $e) {
                \Log::warning("Currency API failed: {$e->getMessage()}");
            }
            // Fallback to fixed rates if API fails
            return $this->getFallbackRate($toCurrency);
        });
    }

    protected function getFallbackRate($currency)
    {
        // Updated rates as of January 2026 (approximate)
        $rates = [
            'USD' => 0.012,   // 1 INR ≈ $0.012 (₹83 ≈ $1)
            'EUR' => 0.011,   // 1 INR ≈ €0.011 (₹90 ≈ €1)
            'GBP' => 0.0095,  // 1 INR ≈ £0.0095 (₹105 ≈ £1)
            'AUD' => 0.018,   // 1 INR ≈ A$0.018
            'CAD' => 0.016,   // 1 INR ≈ C$0.016
            'SGD' => 0.016,   // 1 INR ≈ S$0.016
        ];
        return $rates[$currency] ?? 1;
    }

    public function convert($amount, $toCurrency)
    {
        $rate = $this->getExchangeRate($toCurrency);
        return round($amount * $rate, 2);
    }

    public function getSupportedCurrencies()
    {
        return [
            'INR' => ['symbol' => '₹', 'name' => 'Indian Rupee'],
            'USD' => ['symbol' => '$', 'name' => 'US Dollar'],
            'EUR' => ['symbol' => '€', 'name' => 'Euro'],
            'GBP' => ['symbol' => '£', 'name' => 'British Pound'],
            'AUD' => ['symbol' => 'A$', 'name' => 'Australian Dollar'],
            'CAD' => ['symbol' => 'C$', 'name' => 'Canadian Dollar'],
            'SGD' => ['symbol' => 'S$', 'name' => 'Singapore Dollar'],
        ];
    }

    public function getCurrencyForCountry($countryCode)
    {
        $countryCurrencies = [
            'IN' => 'INR',
            'US' => 'USD',
            'GB' => 'GBP',
            'DE' => 'EUR',
            'FR' => 'EUR',
            'IT' => 'EUR',
            'ES' => 'EUR',
            'AU' => 'AUD',
            'CA' => 'CAD',
            'SG' => 'SGD',
            'MY' => 'INR', // Malaysia - many Indians
            'AE' => 'USD', // UAE
            'SA' => 'USD', // Saudi Arabia
        ];
        return $countryCurrencies[$countryCode] ?? 'USD'; // Default to USD
    }
}