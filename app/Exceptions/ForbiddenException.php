<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class ForbiddenException extends Exception
{
    protected $message;
    protected $code;

    public function __construct(string $message = "Access denied")
    {
        $this->message = $message;
        $this->code    = Response::HTTP_FORBIDDEN;

        parent::__construct($this->message, $this->code);
    }
}
