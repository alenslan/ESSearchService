<?php
/**
 * eleastic 索引操作
 *
 */
namespace Ltbl\ESSearchService;

use Ltbl\ESSearchService\ES;

class Index extends ES
{
    public $indexConf;
    public $index;
    public $type;

    public function __construct($indexName = "fulltext")
    {
        parent::__construct();
        $this->getConfig($indexName);
    }

    /**
     * 获得输入对象的需要的内容字段
     *
     * @param object $item 输入对象
     * @return array
     */
    public function getItemBody($item)
    {
        $bodyConf = $this->indexConf['feilds'];
        $res = [];
        foreach ($bodyConf as $key => $value) {
            $res[$key] = $item->$value;
        }

        return $res;
    }

    /**
     * 添加单条索引
     *
     * @param object $item 输入对象
     * @return void
     */
    public function created($item)
    {
        $this->client->index([
            'index' => $this->index,
            'type' => $this->type,
            'id' => $item->$this->indexConf['id'],
            'body' => $this->getItemBody($item)
        ]);
    }

    /**
     * 更新单条索引
     *
     * @param object $item 输入对象
     * @return void
     */
    public function updated($item)
    {
        $this->client->index([
            'index' => $this->index,
            'type' => $this->type,
            'id' => $item->$this->indexConf['id'],
            'body' => $this->getItemBody($item)
        ]);
    }

    /**
     * 删除单条索引
     *
     * @param object $item 输入对象
     * @return void
     */
    public function deleted($item)
    {
        $this->client->delete([
            'index' => $this->index,
            'type' => $this->type,
            'id' => $item->$this->indexConf['id'],
        ]);
    }

    /**
     * 建立索引
     *
     * @return void
     */
    public function mapping()
    {
        $this->client->indices()->create([
            'index' => $this->index,
            // 'type' => $this->type,
            'body' => [
                'mappings' => $this->config['mappings']
            ]
        ]);
    }

    /**
     * 清空索引
     *
     * @return void
     */
    public function clear()
    {
        $this->client->indices()->delete([
            'index' => $this->index
        ]);
    }

    /**
     * 批量导入数据
     *
     * @param array $params 输入列表
     * @return void
     */
    public function bulk($params)
    {
        if (!empty($params['body'])) {
            $this->client->bulk($params);
        }
    }
}
