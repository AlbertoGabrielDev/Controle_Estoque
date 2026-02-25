<?php

return [
    'enabled' => (bool) env('MODULES_ENABLED', true),

    'paths' => [
        'root' => base_path('Modules'),
    ],

    'autoload' => [
        'migrations' => true,
        'routes' => true,
        'views' => true,
    ],

    'ignore' => [
        '.git',
        '.github',
        '.idea',
        '.vscode',
        '_stubs',
    ],

    'structure' => [
        'http' => 'Http',
        'models' => 'Models',
        'services' => 'Services',
        'repositories' => 'Repositories',
        'database' => 'Database',
        'migrations' => 'Database/Migrations',
        'seeders' => 'Database/Seeders',
        'factories' => 'Database/Factories',
        'jobs' => 'Jobs',
        'commands' => 'Console/Commands',
        'events' => 'Events',
        'listeners' => 'Listeners',
        'routes' => 'Routes',
        'views' => 'Resources/views',
        'resources_js' => 'Resources/js',
        'tests' => 'Tests',
    ],
];
