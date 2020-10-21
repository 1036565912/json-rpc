<?php
declare(strict_types=1);


use Swoole\Client;

define('ROOT_PATH', __DIR__);

require_once './Autoload/Autoload.php';
use Autoload\Autoload;
Autoload::register();

$client = new Client(SWOOLE_SOCK_TCP);

//指定包头包体
$client->set([
    'open_length_check' => 1,
    'package_max_length' => 2 * 1024 * 1024,
    'package_length_type' => 'N',
    'package_length_offset' => 0,
    'package_body_offset' => 4
]);
if (!$client->connect('127.0.0.1', 8080)) {
    exit('connect server fail');
}

$parse = new \Framework\Protocol\JsonProtocol();

//组装数据
$data = [
    'class' => 'User',
    'method' => 'getInfo',
    'params' => [
        'username' => 'cl',
        //'age' => 24
    ]
];

$data = json_encode($data);


//封包发送
$client->send($parse->encode($data));

//接受数据
$res =  $client->recv();

//解析包
var_dump(json_decode($parse->decode($res), true));

$client->close();