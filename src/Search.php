<?php
/**
 * eleastic 搜索调用
 *
 */
namespace Ltbl\ESSearchService;

use Config;

use Ltbl\ESSearchService\ES;

class Search extends ES
{
    /**
     * @param string $indexName 索引名
     */
    public function __construct($indexName = "fulltext")
    {
        parent::__construct();
        $this->getConfig($indexName);
    }

    /**
     * 搜索搜索调用
     *
     * @param string $word 输入查询词
     * @return array
     */
    public function search($word)
    {
        if (! $word) {
            return [];
        }

        $items = $this->client->search([
            'index' => $this->index,
            'type' => $this->type,
            'body' => [
                'query' =>[
                    'match' => [
                        '_all' => $word
                    ]
                ]
            ]
        ]);

        return $items['hits']['hits'];
    }
}
