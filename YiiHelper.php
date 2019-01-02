<?php
/**
 * Created by PhpStorm.
 * User: lyl
 * Date: 2019/01/02
 * Time: 13:52
 */

/**
 * 获取配置项
 * @param null $key
 * @param null $defaultValue
 * @return null
 */
function config($key = null, $defaultValue = null)
{
    $params = app()->params;

    return $params[$key] ?? $defaultValue;
}

/**
 * app
 * @return \yii\console\Application|\yii\web\Application
 */
function app()
{
    return Yii::$app;
}

/**
 * user
 * @return mixed|\yii\web\User
 */
function user()
{
    return app()->user;
}

/**
 * userInfo
 * @param null $attribute
 * @return null|\yii\web\IdentityInterface
 */
function userInfo($attribute = null)
{
    $userInfo = user()->identity;
    return $attribute?$userInfo->$attribute:$userInfo;
}

/**
 * request
 * @return \yii\console\Request|\yii\web\Request
 */
function request()
{
    return app()->request;
}

/**
 * get
 * @param null $name
 * @param null $defaultValue
 * @return array|mixed
 */
function get($name = null, $defaultValue = null)
{
    return request()->get($name, $defaultValue);
}

/**
 * post
 * @param null $name
 * @param null $defaultValue
 * @return array|mixed
 */
function post($name = null, $defaultValue = null)
{
    return request()->post($name, $defaultValue);
}

/**
 * response
 * @return \yii\console\Response|\yii\web\Response
 */
function response()
{
    return app()->response;
}

/**
 * view
 * @return \yii\base\View|\yii\web\View
 */
function view()
{
    return app()->view;
}

/**
 * db
 * @return \yii\db\Connection
 */
function db()
{
    return app()->db;
}

/**
 * cache
 * @return \yii\caching\CacheInterface
 */
function cache()
{
    return app()->cache;
}

/**
 * @return mixed|\yii\web\Session
 */
function session()
{
    return app()->session;
}

function cookies()
{
    return response()->cookies;
}

/**
 * 字符串 加密
 * @param string $string
 * @return string
 */
function encryptString(string $string)
{
    return base64_encode(app()->getSecurity()->encryptByPassword($string, config('cryptSecretKey')));
}

/**
 * 字符串 解密
 * @param string $string
 * @return string
 */
function decryptString(string $string)
{
    return app()->getSecurity()->encryptByPassword(base64_decode($string), config('cryptSecretKey'));
}

/**
 * exceptionFormat
 * @param \Exception $exception
 * @return array
 */
function exceptionFormat($exception)
{
    return [
        'code'        => $exception->getCode(),
        'file'        => $exception->getFile(),
        'line'        => $exception->getLine(),
        'message'     => $exception->getMessage(),
        'traceString' => $exception->getTraceAsString(),
        // 'trace'       => $exception->getTrace(),
    ];
}