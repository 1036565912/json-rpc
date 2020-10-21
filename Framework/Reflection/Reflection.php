<?php
declare(strict_types=1);
namespace Framework\Reflection;

use Framework\Exception\ParamsErrorException;
use Framework\Exception\MethodNotFoundException;
class Reflection
{
    /**
     * 反射执行类实例方法
     * @param string $class
     * @param string $method
     * @param array $params
     * @param object $obj class的实例对象
     * @author chenlin
     * @date 2020/10/19
     */
    public function deal(string $class, string $method, array $params, $obj)
    {
        //反射方法需要的参数
        //检测该类是否有此方法
        if (!$this->checkMethodExist($class, $method)) {
            throw new MethodNotFoundException();
        }
        $method_reflection = new \ReflectionMethod($class, $method);
        $param_arr = $this->getParams($method_reflection, $params);
        $param_arr = $param_arr ?: [];
        return $method_reflection->invokeArgs($obj, $param_arr);
    }


    public function getClass(string $class)
    {
        $reflection = new \ReflectionClass($class);
        $constructor = $reflection->getConstructor();
        if (is_null($constructor)) {
            //没有构造函数 则不能在获取实例对象的时候传递参数
            return $reflection->newInstance();
        }
        $param_arr = $this->getParams($constructor);
        if (is_null($param_arr)) {
            $instance = $reflection->newInstance($param_arr);
        } else {
            $instance = $reflection->newInstance(...$param_arr);
        }
        return $instance;
    }

    /**
     * 检测方法是否存在
     * @param string $class
     * @param string $method
     * @return bool
     * @author chenlin
     * @date 2020/10/19
     */
    public function checkMethodExist(string $class, string $method): bool
    {
        $reflection = new \ReflectionClass($class);
        return $reflection->hasMethod($method);
    }

    /**
     * 反射出方法需要的参数列表
     * @param array|null $params
     * @return array
     * @author chenlin
     * @date 2020/10/19
     */
    public function getParams(?\ReflectionMethod $method, ?array $params = null): ?array
    {

        if (is_null($method) || $method->getNumberOfParameters() === 0) {
            return null;
        }

        $param_arr = [];
        foreach ($method->getParameters() as $param) {
            $class = $param->getClass();
            if ($class) {
                //不为null  则代表是类参数
                array_push($param_arr, $this->getClass($class->getName()));
            } else {
                //为null  则代表是其他参数 需要从参数中进行获取
                if (!$params) {
                    throw new ParamsErrorException();
                }

                //判断对应的key是否存在
                if (!isset($params[$param->getName()])) {
                    throw new ParamsErrorException();
                }
                array_push($param_arr, $params[$param->getName()]);
            }
        }

        return $param_arr;
    }
}