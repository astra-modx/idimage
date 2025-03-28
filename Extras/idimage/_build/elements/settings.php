<?php

return [
    'enable' => [
        'xtype' => 'combo-boolean',
        'value' => true,
        'area' => 'idimage_main',
    ],
    'indexed_type' => [
        'xtype' => 'textfield',
        'value' => 'index_all',
        'area' => 'idimage_main',
    ],
    'indexed_service' => [
        'xtype' => 'combo-boolean',
        'value' => false,
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
    'root_parent' => [
        'xtype' => 'numberfield',
        'value' => 0,
        'area' => 'idimage_main',
    ],
    'send_file' => [
        'xtype' => 'combo-boolean',
        'value' => false,
        'area' => 'idimage_main',
    ],

    'site_url' => [
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'idimage_main',
    ],

    'limit_upload' => [
        'xtype' => 'numberfield',
        'value' => 20,
        'area' => 'idimage_limit',
    ],

    'limit_creation' => [
        'xtype' => 'numberfield',
        'value' => 500,
        'area' => 'idimage_limit',
    ],

    'limit_indexed' => [
        'xtype' => 'numberfield',
        'value' => 100,
        'area' => 'idimage_limit',
    ],
    'limit_show_similar_products' => [
        'xtype' => 'numberfield',
        'value' => 5,
        'area' => 'idimage_limit',
    ],
    'limit_attempt' => [
        'xtype' => 'numberfield',
        'value' => 20,
        'area' => 'idimage_limit',
    ],
    'limit_task' => [
        'xtype' => 'numberfield',
        'value' => 1000,
        'area' => 'idimage_limit',
    ],
];
