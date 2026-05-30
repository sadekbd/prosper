<?php

return [
    'name'        => env('APP_NAME', 'Prosper Media'),
    'slogan'      => 'Be Optimistic',
    'tagline'     => 'Engineering Digital Success with Technical Precision.',
    'email'       => env('PROSPER_EMAIL', 'hello@prospermedia.com'),
    'max_writers' => 10,

    // Cache durations (in seconds)
    'cache' => [
        'settings'  => 3600,   // 1 hour
        'services'  => 1800,   // 30 min
        'portfolio' => 1800,
    ],
];