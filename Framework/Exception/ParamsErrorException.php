<?php
declare(strict_types=1);

namespace Framework\Exception;


class ParamsErrorException extends \Exception
{
    public function __construct($message = "Params Not Match", $code = 404, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}