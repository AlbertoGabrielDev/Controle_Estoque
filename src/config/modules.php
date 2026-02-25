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
        'requests' => 'Http/Requests',
        'controllers' => 'Http/Controllers',
        'models' => 'Models',
        'services' => 'Services',
        'contracts' => 'Contracts',
        'repositories' => 'Repositories',
        'repositories_contracts' => 'Repositories/Contracts',
        'database' => 'Database',
        'migrations' => 'Database/Migrations',
        'seeders' => 'Database/Seeders',
        'factories' => 'Database/Factories',
        'jobs' => 'Jobs',
        'commands' => 'Console/Commands',
        'events' => 'Events',
        'listeners' => 'Listeners',
        'observers' => 'Observers',
        'policies' => 'Policies',
        'notifications' => 'Notifications',
        'rules' => 'Rules',
        'dto' => 'DTOs',
        'actions' => 'Actions',
        'gateways' => 'Gateways',
        'broadcast' => 'Broadcast',
        'routes' => 'Routes',
        'views' => 'Resources/views',
        'resources_js' => 'Resources/js',
        'tests' => 'Tests',
        'tests_feature' => 'Tests/Feature',
        'tests_unit' => 'Tests/Unit',
    ],
];
