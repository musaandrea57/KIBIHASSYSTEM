<?php

return [
    'default' => env('SMS_DRIVER', 'simulated'),

    'drivers' => [
        'simulated' => [
            'class' => \App\Services\Sms\Providers\SimulatedSmsProvider::class,
        ],
        // Add other providers like Twilio, Infobip here
    ],

    'sender_id' => env('SMS_SENDER_ID', 'KIBIHAS'),
    
    'rate_limit' => 50, // messages per minute per user, optional
];
