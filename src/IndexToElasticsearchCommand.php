<?php
/**
 * 游戏应用参数elasticSearch 批量操作索引命令
 *
 */
namespace App\Console\Commands;

use Illuminate\Console\Command;

use Config;
use Ltbl\ESSearchService\Index;

class IndexToElasticsearchCommand extends Command
{
    /**
     * The name and signature of the console command.
     * $indexType 索引配置名
     * $action 操作名 [new bulk clear]
     *
     * @var string
     */
    protected $signature = 'app:es-index {name} {action}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'elasticsearch 批量索引操作';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $arguments = $this->argument();
        $this->name = $arguments['name'];
        $this->action = $arguments['action'];
        $actions = [
            'new' => 'mapping', // 新建索引
            'clear' => 'clear', // 清除索引
            'bulk' => 'bulk', // 批量导入
        ];

        $this->getConfig();
        if (! $this->config) {
            $this->info('essearch配置文件内未发现' . $this->name);
            dd();
        }
        $action = array_get($actions, $this->action);
        if (! $action) {
            $this->info('输入操作 ' . $this->action . '未发现，只能输入 new, clear, bulk这三个操作');
            dd();
        }
        $this->getIndex();
        call_user_func([$this, $action]);
    }

    /**
     * 读取配置信息
     *
     * @return void
     */
    private function getConfig()
    {
        $this->config = Config::get('essearch.' . $this->name, []);
    }
    
    /**
     * 获得索引函数
     *
     * @return void
     */
    private function getIndex()
    {
        $this->index = new Index($this->name);
    }

    /**
     * 新建索引操作
     *
     * @return void
     */
    private function mapping()
    {
        $this->info('-------------------新建索引开始-----------------------------');
        $this->index->mapping();
        $this->info('*******************新建索引结束*****************************');
    }

    /**
     * 清除索引操作
     *
     * @return void
     */
    private function clear()
    {
        $this->info(sprintf('-------------------清除%s索引开始-------------------', $this->name));
        $this->index->clear();
        $this->info(sprintf('*******************清除%s索引结束*******************', $this->name));
    }

    /**
     * 批量索引操作
     *
     * @return void
     */
    private function bulk()
    {
        $this->info('-------------------批量索引开始-----------------------------');
        $this->chunk();
        $this->info('*******************批量索引结束*****************************');
    }

    /**
     * 切片获得数据
     *
     * @return void
     */
    private function chunk()
    {
        $model = new $this->index->config['model'];
        $limitNum = array_get($this->index->config, 'limitNum', 1000);

        $model->chunk($limitNum, function ($rows) {
            $params = ['body' => []];
            foreach ($rows as $row) {
                $idName = $this->index->indexConf['id'];
                
                $data = [
                    'index' => [
                        '_index' => $this->index->index,
                        '_type' => $this->index->type,
                    ]
                ];
                // if ($idName) {
                //     $data['index']['_id'] = $row->$idName;
                // }
                $data['index']['_id'] = sprintf('%s%s', $row->id, $row->apps_type);
                $params['body'][] = $data;
                $params['body'][] = $this->index->getItemBody($row);
            }

            $this->index->bulk($params);
        });
    }
}
