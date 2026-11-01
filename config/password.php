<?php

return [
    'history_limit' => env('PASSWORD_HISTORY_LIMIT', 3),
    'enable_force_password_change' => env('ENABLE_FORCE_PASSWORD_CHANGE', 'on'),
    'enable_password_expiration' => env('ENABLE_PASSWORD_EXPIRATION', 'on'),
    'expires_after_days' => env('PASSWORD_EXPIRES_AFTER_DAYS', 90),
];
