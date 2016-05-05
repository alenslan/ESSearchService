<?php
/**
 * eleasticsearch 配置文件
 *
 */
return [
    // eleasticsearch hosts list 必填
    'hosts' => [
        '10.0.2.15:9200'
    ],
    // 索引配置文档1
    
    // 测试索引配置2
    'test' => [
        // model读取
        'model' => '\App\Games',
        // 一次取的数量
        'limitNum' => 10000,
        // 索引名称和类型
        'index' => [
            'indices' => 'test', // 索引库
            'type' => 'test', // 索引类型
            'id' => 'id', // ID 来源
            'feilds' => [ // 索引字段
                'title'
            ]
        ],
        // 基础配置
        'mappings' => [
            'test' => [
                '_all' => [
                    'analyzer' => 'ik_smart'
                ],
                'properties' => [
                    'title' => [
                        'type' => 'string',
                        'boost' => 10,
                        'term_vector' => 'with_positions_offsets',
                        'analyzer' => 'ik_smart',
                        'include_in_all' => true
                    ]
                ]
            ]
        ],
    ],
];
