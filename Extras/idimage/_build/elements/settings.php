<?php

return [
    'cloud' => [
        'xtype' => 'combo-boolean',
        'value' => false,
        'area' => 'idimage_main',
    ],
    'enable' => [
        'xtype' => 'combo-boolean',
        'value' => true,
        'area' => 'idimage_main',
    ],
    'token' => [
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'idimage_main',
    ],
    'api_url' => [
        'xtype' => 'textfield',
        'value' => 'https://idimage.ru/api',
        'area' => 'idimage_main',
    ],
    'site_url' => [
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'idimage_main',
    ],
    'extract_path' => [
        'xtype' => 'textfield',
        'value' => '',
        //'value' => '{core_path}cache/idimage/indexed',
        'area' => 'idimage_main',
    ],
];
