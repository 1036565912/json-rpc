<?php
declare(strict_types=1);

namespace Framework\Exception;



class MethodNotFoundException extends \Exception
{
    public function __construct($message = "Method Not Found", $code = 404, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}