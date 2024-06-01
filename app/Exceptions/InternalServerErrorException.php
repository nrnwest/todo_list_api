<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class InternalServerErrorException extends Exception
{
    protected $message;
    protected $code;

    public function __construct(string $message = "Internal Server Error")
    {
        $this->message = $message;
        $this->code    = Response::HTTP_INTERNAL_SERVER_ERROR;

        parent::__construct($this->message, $this->code);
    }
}
