<?php
/**
 * Created by : PhpStorm
 * User: Sin Lee
 * Date: 2021/7/30
 * Time: 16:27
 */

use Fastswoole\core\ExceptionHandler;
use Fastswoole\model\PDOInstance;
use Swoole\Runtime;

echo "init\n";
//      设置核心目录
defined('CORE_PATH') or define('CORE_PATH', __DIR__);
//      设置应用目录
defined('APP_PATH') or define('APP_PATH', __DIR__ . '/../');
//      composer加载类
require_once(APP_PATH . 'vendor/autoload.php');
//      加载配置文件
\Helper\EnvHelper::instance()->load(APP_PATH . ".env");
//      根据配置确定开启调试模式
defined('APP_DEBUG') or define('APP_DEBUG', env('app_debug'));
//时区
ini_set('date.timezone', 'Asia/Shanghai');
//      根据配置确定开启调试模式
if (APP_DEBUG == TRUE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 'ON');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 'Off');
    ini_set('log_errors', 'On');
}
// 新建HTTPServer实例
$server = new \Swoole\Http\Server(
    env('swoole.http_address', '0.0.0.0'),
    env('swoole.http_port', '9501')
);
//设置HTTPServer运行条件
$server->set([
//  守护进程化
'daemonize' => 0,
//  配置 Task 进程的数量。
'task_worker_num' => 2,
//  启用 CPU 亲和性设置【默认值：false】
'open_cpu_affinity' => 2,
//  设置端口重用。【默认值：false】
'enable_reuse_port' => TRUE,
//  设置启动的 Worker 进程数。【默认值：CPU 核数】
'worker_num' => 1,
//  设置启动的 Reactor 线程数。【默认值：CPU 核数】
'reactor_num' => 6,
//  设置最大数据包尺寸，单位为字节。【默认值：2M 即 2 * 1024 * 1024，最小值为 64K】
'package_max_length' => 1024 * 1024 * 8,
//  数据包分发策略。【默认值：2】
'dispatch_mode' => 1,
//  丢弃已关闭链接的数据请求。【默认值：true】
'discard_timeout_request' => TRUE,
//  启用 open_tcp_nodelay。【默认值：false】,开启后 TCP 连接发送数据时会关闭 Nagle 合并算法，立即发往对端 TCP 连接。
'open_tcp_nodelay' => TRUE,
//  启用 MQTT 协议处理。【默认值：false】,启用后会解析 MQTT 包头，worker 进程 onReceive 每次会返回一个完整的 MQTT 数据包。
'open_mqtt_protocol' => TRUE,
//  开启静态文件请求处理功能，需配合 document_root 使用 默认 false
'enable_static_handler' => TRUE,
//  配置静态文件根目录，与 enable_static_handler 配合使用。
'document_root' => APP_PATH . "/public/",
]);

$server->on('request', function (\Swoole\Http\Request $request, \Swoole\Http\Response $response) use ($server) {
    $method = $request->server['request_method'];
    //处理跨域
    if ($method == "OPTIONS") {
        $response->header('Access-Control-Allow-Origin', '*');
        $response->header("Access-Control-Allow-Methods", "*");
        $response->header("Access-Control-Allow-Headers",
            "Content-Type,XFILENAME,XFILECATEGORY,XFILESIZE,X-Requested-With");
        $response->status(200);
        $response->end();
        return;
    }
    //处理图标
    if ($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {
        $response->end();
        return;
    }
    //实例化APP
    $app = new \Fastswoole\core\App($request, $response, $server);
    echo "request:" . date("Y-m-d H:i:s") . "\n";
    //APP启动
    $app->run();
});

//处理异步任务的结果(此回调函数在worker进程中执行)
$server->on('Finish', function ($server, $task_id, $data) {
    echo $data . " finish\n";
});


//处理异步任务(此回调函数在task进程中执行)
$server->on('Task', function ($server, $task_id, $worker_id, $data) {
    var_dump($data);
});

//启动Worker进程
$server->on('workerStart', function ($servers, $id) {
//  一键协程化,请求间的协程
    Runtime::enableCoroutine(SWOOLE_HOOK_ALL);
//  PDO连接池
    if (!empty(env('database.use')) && !empty(env('database.pool'))) {
        $pdoConfig = new \Swoole\Database\PDOConfig();
        $pdoConfig->withHost(env('database.host'))->withPort(env('database.port'))
                  ->withDbname(env('database.dbname'))
                  ->withCharset(env('database.charset'))
                  ->withUsername(env('database.username'))
                  ->withPassword(env('database.password'));
        $pdoPool = new \Swoole\Database\PDOPool($pdoConfig, env('database.pool'));
        //注入控制反转容器
        \Fastswoole\core\Di::instance()->set(\Swoole\Database\PDOPool::class, $pdoPool);
    }
//  redis连接池
    if (!empty(env('redis.use')) && !empty(env('redis.pool'))) {
        $redisConfig = new \Swoole\Database\RedisConfig();
        $redisConfig->withHost(env('redis.host'))
                    ->withPort(env('redis.port'))
                    ->withAuth(env('redis.auth'))
                    ->withDbIndex(env('redis.index'))
                    ->withTimeout(env('redis.timeout'));
        $redisPool = new \Swoole\Database\RedisPool($redisConfig, env('database.pool'));
        //注入控制反转容器
        \Fastswoole\core\Di::instance()->set(\Swoole\Database\RedisPool::class, $redisPool);
    }
});

//启动HTTPServer服务
echo "start\n";
$server->start();
