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
     * @param string $indexName 索引配置名
     */
    public function __construct($indexName = "")
    {
        parent::__construct();
        $this->getConfig($indexName);
    }

    /**
     * 搜索搜索调用
     *
     * @param string $word 输入查询词
     * @param int $pn 页数
     * @param int $size 每页条数
     * @return array
     */
    public function search($word, $pn=0, $size=10)
    {
        if (! $word) {
            return [];
        }

        $from = $pn * $size;
        $items = $this->client->search([
            'index' => $this->index,
            'type' => $this->type,
            'body' => [
                'query' => [
                    'match' => [
                        '_all' => $word
                    ]
                ]
            ],
            'from' => $from,
            'size' => $size,
        ]);

        return $items['hits']['hits'];
    }

    /**
     * 多字段搜索搜索调用
     *
     * @param string $word 输入查询词
     * @param array $fields 输入查询字段
     * @param int $pn 页数
     * @param int $size 每页条数
     * @return array
     */
    public function multiSearch($word, $fields, $pn=0, $size=10)
    {
        if (! $word) {
            return [];
        }

        $from = $pn * $size;
        $items = $this->client->search([
            'index' => $this->index,
            'type' => $this->type,
            'body' => [
                'query' => [
                    'multi_match' => [
                        'query' => $word,
                        'type' => 'best_fields', 
                        'fields' => $fields,
                    ]
                ]
            ],
            'from' => $from,
            'size' => $size,
        ]);

        return $items['hits']['hits'];
    }

    /**
     * 结果输出整理
     * 
     * @param array $items 输入列表
     * @return array
     */
    public function searchOutputFomat($items)
    {
        $res = [];
        foreach ($items as $item) {
            $data = $item['_source'];
            $data['_id'] = $item['_id'];
            $res[] = $data;
        }

        return $res;
    }
}
