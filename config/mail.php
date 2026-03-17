<?php

return [
    'default'  => env('MAIL_MAILER', 'log'),
    'mailers'  => [
        'log'      => ['transport' => 'log', 'channel' => env('MAIL_LOG_CHANNEL')],
        'array'    => ['transport' => 'array'],
        'smtp'     => ['transport' => 'smtp', 'scheme' => env('MAIL_SCHEME'), 'url' => env('MAIL_URL'), 'host' => env('MAIL_HOST', '127.0.0.1'), 'port' => env('MAIL_PORT', 2525), 'username' => env('MAIL_USERNAME'), 'password' => env('MAIL_PASSWORD'), 'timeout' => null, 'local_domain' => env('MAIL_EHLO_DOMAIN', parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST))],
        'sendmail' => ['transport' => 'sendmail', 'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i')],
        'mailgun'  => ['transport' => 'mailgun', 'client' => ['timeout' => 5]],
        'ses'      => ['transport' => 'ses'],
        'postmark' => ['transport' => 'postmark'],
        'resend'   => ['transport' => 'resend'],
    ],
    'from'     => ['address' => env('MAIL_FROM_ADDRESS', 'noreply@nabadiganta.com'), 'name' => env('MAIL_FROM_NAME', 'Novodigonto Somity')],
];
