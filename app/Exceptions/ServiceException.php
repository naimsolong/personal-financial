<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class ServiceException extends Exception
{
    public function __construct($message = '', $code = 0, ?Throwable $previous = null)
    {
        $message = '['.pathinfo($this->getFile())['basename']."] $message";

        parent::__construct($message, $code, $previous);
    }
}
