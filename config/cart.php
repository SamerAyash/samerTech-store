<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cart Tax Rate
    |--------------------------------------------------------------------------
    |
    | The tax rate to apply to cart subtotals.
    | This is a decimal value (e.g., 0.10 for 10%).
    |
    */
    'tax_rate' => env('CART_TAX_RATE', 0.00),

    /*
    |--------------------------------------------------------------------------
    | Cart Discount
    |--------------------------------------------------------------------------
    |
    | Default discount amount (can be overridden per cart).
    | This is a decimal value.
    |
    */
    'default_discount' => env('CART_DEFAULT_DISCOUNT', 0),
];

