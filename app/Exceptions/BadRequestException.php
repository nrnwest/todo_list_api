<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class BadRequestException extends Exception
{
    protected $message;
    protected $code;

    public function __construct(string $message = "Bad Request")
    {
        $this->message = $message;
        $this->code    = Response::HTTP_BAD_REQUEST;

        parent::__construct($this->message, $this->code);
    }
}
