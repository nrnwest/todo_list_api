<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class UnauthorizedException extends Exception
{
    protected $message;
    protected $code;

    public function __construct(string $message = "Unauthorized")
    {
        $this->message = $message;
        $this->code    = Response::HTTP_UNAUTHORIZED;

        parent::__construct($this->message, $this->code);
    }
}
