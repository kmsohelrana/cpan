<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application, which will be used when the
    | framework needs to place the application's name in a notification or
    | other UI elements where an application name needs to be displayed.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),
    'check_incoming_ip' => env('CHECK_INCOMING_IP', false),
    'ocr_manual_entry_allowed' => env('OCR_MANUAL_ENTRY_ALLOWED', 'ON'),
    'location_fetch_domain' => env('LOCATION_FETCH_DOMAIN', 'http://crm-data-test.sslwireless.com'),
    'merchant_min_age' => env('MERCHANT_MIN_AGE', 18),
    'self_signup_allowed' => env('SELF_SIGNUP_ALLOWED', false),
    'otp_auto_fill_enabled' => env('OTP_AUTO_FILL_ENABLED', true),
    'is_nominee_step_show' => env('IS_NOMINEE_STEP_SHOW', true),
    'max_leads_per_owner_mobile' => env('LEAD_MAX_PER_OWNER_MOBILE', 5),

    'merchant_app_link' => env('MERCHANT_APP_LINK', ''),
    'crm_app_link' => env('CRM_APP_LINK', ''),

    // crm app versions
    'crm_android_app_version' => env('CRM_ANDROID_APP_VERSION', '1.0.0'),
    'crm_ios_app_version' => env('CRM_IOS_APP_VERSION', '1.0.0'),
    'crm_app_otp_enable' => env('CRM_APP_OTP_ENABLE', false),

    // merchant app versions
    'merchant_android_app_version' => env('MERCHANT_ANDROID_APP_VERSION', '1.0.0'),
    'merchant_ios_app_version' => env('MERCHANT_IOS_APP_VERSION', '1.0.0'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | the application so that it's available within Artisan commands.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. The timezone
    | is set to "UTC" by default as it is suitable for most use cases.
    |
    */

    'timezone' => 'Asia/Dhaka',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by Laravel's translation / localization methods. This option can be
    | set to any locale for which you plan to have translation strings.
    |
    */

    'locale' => env('APP_LOCALE', 'en'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is utilized by Laravel's encryption services and should be set
    | to a random, 32 character string to ensure that all encrypted values
    | are secure. You should do this prior to deploying the application.
    |
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'hmac_salt1' => 'kP9!mX2$vL7&zQ4b',
    'hmac_salt2' => 'fJ5#tN8*wY1@gX3p',
    'pin_salt' => env('APP_PIN_SALT', '1mpwMTPL4Day0IUuBULcg2iCXv9Sknt7'), // must be exactly 32 characters for AES-256-CBC

    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

];
