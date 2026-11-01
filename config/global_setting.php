<?php

return [

    'defaults' => [
        /*
        |--------------------------------------------------------------------------
        | General Settings
        |--------------------------------------------------------------------------
        */
        'system_sms_enabled'     => '1',
        'system_push_enabled'    => '1',
        'system_email_enabled'   => '1',

        /*
        |--------------------------------------------------------------------------
        | Notification Templates
        |--------------------------------------------------------------------------
        */
        'sms_template_body'      => 'Hello {name}, your security code is {code}.',
        'push_template_title'    => 'System Alert',
        'push_template_body'     => 'An administrative action requires your attention.',
        'email_template_subject' => 'Account Credentials and Notice',
        'email_template_body'    => 'Hello {name}, your account has been updated.',
    ],

];
