<?php

use Illuminate\Database\Seeder;
use App\Currency;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Clear existing currencies to start fresh for hardening phase
        Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Currency::truncate();
        Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        // 2. Insert Core Currencies
        Currency::create([
            'code' => 'USD',
            'name' => 'US Dollar',
            'symbol' => '$',
            'exchange_rate' => 1.00000000,
            'is_base_currency' => true,
            'is_active' => true,
            'decimal_precision' => 2,
        ]);

        Currency::create([
            'code' => 'AFN',
            'name' => 'Afghan Afghani',
            'symbol' => '؋',
            'exchange_rate' => 0.01428571, // Example: 1 AFN = 0.0142 USD (approx 70 AFN = 1 USD)
            'is_base_currency' => false,
            'is_active' => true,
            'decimal_precision' => 2,
        ]);

        Currency::create([
            'code' => 'PKR',
            'name' => 'Pakistani Rupee',
            'symbol' => '₨',
            'exchange_rate' => 0.00357143, // Example: 1 PKR = 0.0035 USD (approx 280 PKR = 1 USD)
            'is_base_currency' => false,
            'is_active' => true,
            'decimal_precision' => 2,
        ]);

        Currency::create([
            'code' => 'EUR',
            'name' => 'Euro',
            'symbol' => '€',
            'exchange_rate' => 1.08000000, // Example: 1 EUR = 1.08 USD
            'is_base_currency' => false,
            'is_active' => true,
            'decimal_precision' => 2,
        ]);
    }
}
