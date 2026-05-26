<?php

return [

  /*
  |--------------------------------------------------------------------------
  | Razorpay API credentials
  |--------------------------------------------------------------------------
  |
  | Use TEST keys from Razorpay Dashboard → Settings → API Keys (Test Mode).
  | For production, switch to Live Mode keys and set RAZORPAY_MODE=live.
  |
  */

    'key' => env('RAZORPAY_KEY'),

    'secret' => env('RAZORPAY_SECRET'),

    'webhook_secret' => env('RAZORPAY_WEBHOOK_SECRET'),

    'currency' => env('RAZORPAY_CURRENCY', 'INR'),

    'mode' => env('RAZORPAY_MODE', 'test'),

    /*
    |--------------------------------------------------------------------------
    | Cash on delivery (optional)
    |--------------------------------------------------------------------------
    */

    'cod_enabled' => (bool) env('CHECKOUT_COD_ENABLED', false),

];
