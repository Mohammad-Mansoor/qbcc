<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class Currency extends Model 
{
    protected $fillable = [
        'code', 'name', 'symbol', 'exchange_rate', 
        'is_base_currency', 'is_active', 'decimal_precision'
    ];

    /**
     * Convert an amount from this currency to the base currency (USD)
     */
    public function convertToBase($amount)
    {
        return bcmul($amount, $this->exchange_rate, 4);
    }

    /**
     * Convert an amount from the base currency (USD) to this currency
     */
    public function convertFromBase($amount)
    {
        if ($this->exchange_rate == 0) return 0;
        return bcdiv($amount, $this->exchange_rate, 4);
    }

    /**
     * Get the base currency instance
     */
    public static function getBase()
    {
        return self::where('is_base_currency', true)->first() 
               ?? self::where('code', 'USD')->first();
    }

    /**
     * Get the legacy AFN rate (How many AFN = 1 USD)
     */
    public static function getLegacyAFNRate()
    {
        $afn = self::where('code', 'AFN')->first();
        return ($afn && $afn->exchange_rate > 0) ? (1 / $afn->exchange_rate) : 70;
    }
}
