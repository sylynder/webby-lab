<?php

if ( ! function_exists('currency')) 
{
    function currency($price, $decimal_places = 2)
    {
        return currency_symbol(config_item('currency')) . '' . number_format($price, $decimal_places);
    }

    function currency_word($price = '', $symbolInWords = '') 
    {
		return number_format($price) .' '.  $symbolInWords ;
	}

}

if ( ! function_exists('currency_symbol')) 
{
    function currency_symbol($currency)
    {
        if ( ! $currency) {
            return false;
        }

        $currencies = [

            'GHS' => '₵', // Ghana Cedis
            'USD' => '$', // US Dollar
            'EUR' => '€', // Euro
            'CRC' => '₡', // Costa Rican Colón
            'GBP' => '£', // British Pound Sterling
            'ILS' => '₪', // Israeli New Sheqel
            'INR' => '₹', // Indian Rupee
            'JPY' => '¥', // Japanese Yen
            'KRW' => '₩', // South Korean Won
            'NGN' => '₦', // Nigerian Naira
            'PHP' => '₱', // Philippine Peso
            'PLN' => 'zł', // Polish Zloty
            'PYG' => '₲', // Paraguayan Guarani
            'THB' => '฿', // Thai Baht
            'UAH' => '₴', // Ukrainian Hryvnia
            'VND' => '₫', // Vietnamese Dong)
        ];

        if (array_key_exists($currency, $currencies)) {
            return $currencies[$currency];
        } else {
            return $currency;
        }
    }
}

if ( ! function_exists('cents_to_dollars')) 
{
    function cents_to_dollars($amount)
    {
        return (1 / 100) * $amount;
    }
}

if ( ! function_exists('dollars_to_cents')) 
{
    function dollars_to_cents($amount)
    {
        return ($amount * 100);
    }
}
