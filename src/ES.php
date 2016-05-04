<?php
/**
 * eleastic 应用端 请求内部请求模块
 *
 */
namespace Ltbl\ESSearchService;

use Config;

use Elasticsearch\ClientBuilder as ESClient;

class ES
{
    /**
     * elastic客户端连接
     */
    protected $client;

    /**
     * 索引库名
     */
    protected $index;

    /**
     * 索引类型
     */
    protected $type;

    public function __construct()
    {
        $hosts = Config::get('essearch.hosts', []);
        $this->client = ESClient::create()
            ->setHosts($hosts)
            ->build();
    }

    /**
     * 读取类型配置
     *
     * @param string $configName 配置名
     * @return void
     */
    protected function getConfig($configName)
    {
        $this->config = Config::get('essearch.' . $configName, []);
        $this->indexConf = $this->config['index'];
        $this->index = $this->indexConf['indices'];
        $this->type = $this->indexConf['type'];
    }
}
