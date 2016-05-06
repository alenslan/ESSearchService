
# **ESSearchService 文档**

## **安装**
## **安装ElasticSearch**
   **ubuntu版本** 下载官方网站deb包
   
    dkpg -i xxxx.deb

## **插件**

ElasticSearch IK 安装
#### **1.下载** 

    https://github.com/medcl/elasticsearch-analysis-ik
    
选择版本下载
#### **2.本地文件解压**
    
    unzip 
    cd elasticsearch-xx
    mvn package
    
#### **3.安装**
ps坑：jdk需要1.7版本以上
    
    CONFIG_HOME:/etc/elasticsearch/  （源码的config）
    ES_HOME:/usr/share/elasticsearch

复制 ik 自己的 config 到 CONFIG_HOME
复制 mvn后的 /target/releases/elasticsearch-analysis-ik-1.9.1.zip 到ES_HOME/plugins/ik
解压elasticsearch-analysis-ik-1.9.1.zip

restart elasticsearch

#### **4.热更新**
plugins/ik/config/ik/IKAnalyzer.cfg.xml
目前该插件支持热更新 IK 分词，通过上文在 IK 配置文件中提到的如下配置

    <!--用户可以在这里配置远程扩展字典 -->
    <entry key="remote_ext_dict">location</entry>
    <!--用户可以在这里配置远程扩展停止词字典-->
    <entry key="remote_ext_stopwords">location</entry>
其中 location 是指一个 url，比如 http://yoursite.com/getCustomDict，该请求只需满足以下两点即可完成分词热更新。

## **Laravel安装**
环境要求

    "require": {
        "php": ">=5.5.0",
        "laravel/framework": "5.1.*",
        "elasticsearch/elasticsearch": "~2.0",
    },

添加到composer.json

    "require": {
        "Ltbl/ESSearchService": "~1.0"
    },

私有包安装

    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/alenslan/ESSearchService.git"
        }
    ]
    
然后 composer update

## **使用文档**

#### **配置**
添加config/app.php 文件里的

    'providers' => [
        Ltbl\ESSearchService\SearchServiceProvider::class,
    ]
    
    php artisan vendor:publish

或者复制配置文件
 
    cp vendor/Ltbl/ESSearchService/src/config/essearch.php config/


#### **配置文件详解**

config/essearch.php

    'hosts' => [
        '10.0.2.15:9200'
    ],
    
多个地址和端口的集合

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
    
test : 配置名

model : 配置使用model名

limitNum : 配置批量导入一次的条数

index : 索引字段配置

mapping : 生成新索引的配置

## **功能模块**

    use Ltbl\ESSearchService\Analyze;
    use Ltbl\ESSearchService\Search;
    use Ltbl\ESSearchService\Index;

#### **模块类**
**1 Analyze**
使用 

    (new Analyze())->ikAnalyze("中华人民共和国");

说明

    /**
     * 分词器调用
     *
     * @param string $text 输入文本 
     * @param string $analyzer 选择分析器 
     * @return array
     */
    public function ikAnalyze($text, $analyzer = 'ik_smart')
    
**2 Search**    

使用 

    (new Search())->search('全民');
    
说明

    /**
     * 搜索搜索调用
     *
     * @param string $word 输入查询词
     * @return array
     */
    public function search($word)
    
**3 Index**

使用

    $index = new Index('test'); // 引入配置
    
    $index->mapping(); // 新建索引
    
    $index->clear(); // 清除索引
    
    $index->bulk($params); // 批量导入索引
    
    $index->created($item); // 新增单条索引
    
    $index->updated($item); // 更新单条索引
    
    $index->deleted($item); // 删除单条索引
说明

    /**
     * @param string $indexName 索引配置
     * @return void
     */
    public function __construct($indexName = "")
    
    /**
     * 添加单条索引
     *
     * @param object $item 输入对象
     * @return void
     */
    public function created($item)
    
    /**
     * 更新单条索引
     *
     * @param object $item 输入对象
     * @return void
     */
    public function updated($item)
    
    /**
     * 删除单条索引
     *
     * @param object $item 输入对象
     * @return void
     */
    public function deleted($item)
    
    /**
     * 建立索引
     *
     * @return void
     */
    public function mapping()
    
    /**
     * 清空索引
     *
     * @return void
     */
    public function clear()
    
    /**
     * 批量导入数据
     * 
     * @param array $params 输入列表
     * @return void
     */
    public function bulk($params)

    
    
## **命令行操作索引**
 复制文件：
 
    cp vendor/Ltbl/ESSearchService/src/IndexToElasticsearchCommand.php app/Console/Commands/
    
 然后在app/Console/Kernel.php 添加以下代码：
 
    protected $commands = [
        \App\Console\Commands\IndexToElasticsearchCommand::class,
    ];
    
运行命令如下：
    
    php artisan app:es-index test [new|clear|bulk]

test是配置名
new   是新建索引[需要索引不存在]
clear 是清除索引
bulk  是批量导入索引数据



