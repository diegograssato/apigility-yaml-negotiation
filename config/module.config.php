<?php

use ZF\ContentNegotiation\JsonModel;
use ZF\ContentNegotiation\YAML\View\YamlModel;

return [
    'zf-content-negotiation' => [
        'selectors' => [
            'HalJsonYAML' => [
                YamlModel::class => [
                    'text/yaml',
                    'text/x-yaml',
                    'application/yaml',
                    'application/x-yaml',
                    'text/vnd.yaml',
                    'application/vnd.yaml',
                ],
                JsonModel::class => [
                    'application/json',
                    'application/*+json',
                ],
            ],
        ],
    ],
];
