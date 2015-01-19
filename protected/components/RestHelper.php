<?php
/**
 * RestHelper
 *
 * File: RestHelper.php
 * Date: 14-3-3
 * Copyright: 2014 Mofang.com
 */
class RestHelper
{
    const SUCCESS_CODE = 0; // 成功
    const ERROR_CODE = 999; // 失败（未定义原因）
    const UNAUTHORIZED_CODE = 1; // 未授权
    const EXISTS_CODE = 2; // 已存在

    /**
     * 返回一个错误
     *
     * @param $msg
     * @param int $code 非1000的Code为错误Code
     * @param array $data
     */
    public static function error($msg, $code = self::ERROR_CODE, $data = array())
    {
        self::output($msg, $code, $data);
    }

    /**
     * 返回成功数据
     *
     * @param array $data
     * @param string $msg
     * @param int $code
     */
    public static function success(array $data = array(), $msg = 'Succeed!', $code = self::SUCCESS_CODE)
    {
        self::output($msg, $code, $data);
    }

    /**
     * 最终输出
     *
     * @param $msg
     * @param int $code
     * @param array $data
     */
    private static function output($msg, $code = self::SUCCESS_CODE, $data = array())
    {
        $resData = array(
            'code'    => $code,
            'message'    => $msg,
            'data'    => $data,
        );

        $result = json_encode($resData);

        $callback = isset($_GET['callback']) ? $_GET['callback'] : '';
        if ($callback != '') {
            $result = htmlspecialchars($callback)."({$result})";
        } else {
            header('Content-type: application/json');
        }

        echo $result;
        Yii::app()->end();
    }
}
