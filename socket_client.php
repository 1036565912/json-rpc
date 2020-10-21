<?php
declare(strict_types=1);


define('ROOT_PATH', __DIR__);

require_once './Autoload/Autoload.php';
use Autoload\Autoload;
Autoload::register();


$client = stream_socket_client('tcp://127.0.0.1:8080',$errno, $errstr, 30);


stream_set_timeout($client, 3);

if (!$client) {
    echo $errstr.PHP_EOL;
}

$parse = new \Framework\Protocol\JsonProtocol();

//组装数据
$data = [
    'class' => 'User',
    'method' => 'getInfo',
    'params' => [
        'username' => 'cl',
        'age' => 24
    ]
];

//封包发送
fwrite($client, $parse->encode(json_encode($data)));


$data = fread($client, 2 * 1024 * 1024);

//解包打印
var_dump(json_decode($parse->decode($data), true));

