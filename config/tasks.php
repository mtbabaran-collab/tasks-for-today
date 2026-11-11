<?php

return [
    /*
    | Where the "task completed" email goes. Set TASK_NOTIFY_EMAIL in .env.
    | With MAIL_MAILER=log the email is written to storage/logs/laravel.log instead of
    | being delivered, so it can be checked without a real inbox.
    */
    'notify_email' => env('TASK_NOTIFY_EMAIL', 'tasks-admin@example.com'),
];
