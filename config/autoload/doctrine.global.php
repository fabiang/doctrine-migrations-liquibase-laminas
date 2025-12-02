<?php

use Doctrine\DBAL\Driver\PDO\SQLite\Driver as SQLiteDriver;

return [
    'doctrine'         => [
        'cache'         => [],
        'configuration' => [
            'orm_default' => [
                'sql_logger'      => 'DoctrineLoggerProxy',
                'metadata_cache'  => 'array',
                'query_cache'     => 'array',
                'result_cache'    => 'array',
                'hydration_cache' => 'array',
                'driver'          => 'orm_default',
                'filters'         => [],
                'proxy_dir'       => 'data/DoctrineORMModule/Proxy',
            ],
            'orm_test' => [
                'sql_logger'      => 'DoctrineLoggerProxy',
                'metadata_cache'  => 'array',
                'query_cache'     => 'array',
                'result_cache'    => 'array',
                'hydration_cache' => 'array',
                'driver'          => 'orm_test',
                'filters'         => [],
                'proxy_dir'       => 'data/DoctrineORMModule/Proxy',
            ],
        ],
        'connection'    => [
            'orm_default' => [
                'driverClass' => SQLiteDriver::class,
                'params'      => [
                ],
                'doctrine_type_mappings' => [],
            ],
            'orm_test' => [
                'driverClass' => SQLiteDriver::class,
                'params'      => [
                ],
                'doctrine_type_mappings' => [],
            ],
        ],
        'entitymanager' => [
            // configuration for the `doctrine.entitymanager.orm_default` service
            'orm_default' => [
                // connection instance to use. The retrieved service name will
                // be `doctrine.connection.$thisSetting`
                'connection'    => 'orm_default',

                // configuration instance to use. The retrieved service name will
                // be `doctrine.configuration.$thisSetting`
                'configuration' => 'orm_default',
            ],
            // configuration for the `doctrine.entitymanager.orm_default` service
            'orm_test' => [
                // connection instance to use. The retrieved service name will
                // be `doctrine.connection.$thisSetting`
                'connection'    => 'orm_test',

                // configuration instance to use. The retrieved service name will
                // be `doctrine.configuration.$thisSetting`
                'configuration' => 'orm_test',
            ],
        ],
    ],
];
