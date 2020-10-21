<?php

//rpc server 入口文件
use Swoole\Runtime;
use Framework\Server;
//开启一键协程化
Runtime::enableCoroutine($flags = SWOOLE_HOOK_ALL);

//定义入口常量 以及  注册自动加载
//定义入口目录常量
define('ROOT_PATH', __DIR__);

//注册自动加载函数
include './Autoload/Autoload.php';
use Autoload\Autoload;
Autoload::register();


//注册rpc server 这里不采用注册发布

new Server('127.0.0.1',8080);