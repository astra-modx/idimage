<?php

return [
    'idImageSimilar' => [
        'file' => 'idimagesimilar',
        'description' => 'snippet return most similar items',
        'properties' => [
            'pid' => [
                'type' => 'numberfield',
                'value' => '',
            ],
            'min_scope' => [
                'type' => 'numberfield',
                'value' => 70,
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
