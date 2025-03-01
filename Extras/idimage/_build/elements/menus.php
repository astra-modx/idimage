<?php

return [
    'idimage' => [
        'description' => 'idimage_menu_desc',
        'action' => 'home',
        //'icon' => '<i class="icon icon-large icon-modx"></i>',
    ],

    'idimage_system_settings' => [
        'description' => 'idimage_system_settings_desc',
        'parent' => 'idimage',
        'menuindex' => 2,
        'namespace' => 'core',
        'permissions' => 'settings',
        'action' => 'system/settings',
        'params' => '&ns=idimage',
    ],
];
