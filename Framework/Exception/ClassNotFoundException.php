<?php
declare(strict_types=1);

namespace Framework\Exception;

/**
 * 类不存在异常
 * @author chenlin
 * @date 2020/10/19
 */
class ClassNotFoundException extends \Exception
{
    public function __construct($message = "Class Not Found", $code = 404, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}