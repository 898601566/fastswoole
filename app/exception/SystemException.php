<?php
/**
 * Created by : PhpStorm
 * User: Sin Lee
 * Date: 2021/7/30
 * Time: 16:27
 */

namespace App\exception;

use Fastswoole\core\BaseException;

/**
 * 100-999
 * Class SystemException
 * @package app\exception
 */
class SystemException extends BaseException
{
    const SYSTEM_ERROR = ['msg' => '系统错误', 'code' => 10000,];
    const MODULE_DOES_NOT_EXIST = ['msg' => '模块不存在', 'code' => 10001,];
    const CONTROLLER_DOES_NOT_EXIST = ['msg' => '控制器不存在', 'code' => 10002,];
    const METHOD_DOES_NOT_EXIST = ['msg' => '方法不存在', 'code' => 10003,];
    const TIMESTAMP_CANNOT_BE_EMPTY = ['msg' => '时间戳不能为空', 'code' => 10004,];
    const TIMESTAMP_OUT = ['msg' => '时间戳超时', 'code' => 10005,];
    const SIGNATURE_ERROR = ['msg' => '签名错误', 'code' => 10006,];
    const SERVER_TIMEOUT = ['msg' => '服务器超时,请稍后再试', 'code' => 10007,];
}
