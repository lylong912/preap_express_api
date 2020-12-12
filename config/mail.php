<?php
return [
  'driver' => env('MAIL_DRIVER', 'smtp'),
  'host' => env('MAIL_HOST', 'mail.anyprovider.com'),
  'port' => env('MAIL_PORT', 587),
  'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'name@example.com'),
        'name' => env('MAIL_FROM_NAME', 'example.com'),
  ],
  'encryption' => env('MAIL_ENCRYPTION', 'tls'),
  'username' => env('MAIL_USERNAME', 'name@example.com'),
  'password' => env('MAIL_PASSWORD', 'supersecretpassword'),
  'sendmail' => '/usr/sbin/sendmail -bs',
  'markdown' => [
    'theme' => 'default',
    'paths' => [
      resource_path('views/vendor/mail'),
    ],
  ],
];