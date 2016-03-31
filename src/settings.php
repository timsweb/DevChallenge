<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'layout' => [
            'header' => 'layout/header.phtml',
            'footer' => 'layout/footer.phtml',
        ],
        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../logs/app.log',
        ],
    ],
];
