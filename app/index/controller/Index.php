<?php
/**
 * Created by : PhpStorm
 * User: Sin Lee
 * Date: 2021/7/30
 * Time: 16:27
 */

namespace App\index\controller;

use App\exception\SystemException;
use App\index\model\Admin;
use App\index\model\BigData;
use Fastswoole\core\Controller;

class Index extends Controller
{
    public function index()
    {

        $this->app->html('<h1>Hello Swoole. #' . rand(1000, 9999) . '</h1>');
    }

    public function systemError()
    {
        SystemException::throwException(SystemException::SYSTEM_ERROR);
    }

    public function coroutine()
    {
        sleep(1);
        $this->app->html("hello coroutine");
    }

    public function hello()
    {
        $this->app->json(['hello' => "fastswoole"]);
    }
}
