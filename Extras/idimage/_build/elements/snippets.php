<?php

return [
    'idImageClose' => [
        'file' => 'idimageclose',
        'description' => 'idimage snippet to list items',
        'properties' => [
            'pid' => [
                'type' => 'numberfield',
                'value' => '',
            ],
            'min_scope' => [
                'type' => 'numberfield',
                'value' => 80,
            ],
            'max_scope' => [
                'type' => 'numberfield',
                'value' => 100,
            ],
            'limit' => [
                'type' => 'numberfield',
                'value' => 10,
            ],
        ],
    ],
];
