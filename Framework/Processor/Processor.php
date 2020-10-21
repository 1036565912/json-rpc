<?php
declare(strict_types=1);
namespace Framework\Processor;

use Framework\Protocol\JsonProtocol;
use Framework\Exception\ClassNotFoundException;
use Framework\Reflection\Reflection;
/** 远程调用处理器 */
class Processor
{
    protected $rpc_root = ROOT_PATH.'/Service/';
    protected $rpc_namespace = '\\Service\\';
    public function deal(string $data)
    {
        //解析数据
        $parse = new JsonProtocol();
        $data = json_decode($parse->decode($data), true);

        //执行返回结果
        try {
            //判断调用的类是否存在
            $file = $this->rpc_root.ucfirst($data['class']).'.php';
            if (!file_exists($file)) {
                throw new ClassNotFoundException();
            }

            $reflection = new Reflection();
            //调用该类  然后执行方法返回数据
            $namespace_class = $this->rpc_namespace.ucfirst($data['class']);
            $classInstance = $reflection->getClass($namespace_class);

            //则调用返回数据
            $res = $reflection->deal($namespace_class, $data['method'], $data['params'], $classInstance);
            return $parse->encode(json_encode([
                'result' => $res,
                'error'  => '',
                'class'  => $data['class'],
                'method' => $data['method'],
                'params' => $data['params']
            ]));
        } catch (\Exception $e) {
            $result = [
                'result' => '',
                'error'  => $e->getMessage(),
                'class'  => $data['class'],
                'method' => $data['method'],
                'params' => $data['params']
            ];

            return $parse->encode(json_encode($result));
        }

    }
}