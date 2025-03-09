<?php

return [
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
    'minimum_probability_score' => [
        'xtype' => 'numberfield',
        'value' => 70,
        'area' => 'idimage_main',
    ],
    'maximum_products_found' => [
        'xtype' => 'numberfield',
        'value' => 50,
        'area' => 'idimage_main',
    ],
];
