<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class NotFoundException extends Exception
{
    protected $message;
    protected $code;

    public function __construct(string $message = "Resource not found")
    {
        $this->message = $message;
        $this->code    = Response::HTTP_NOT_FOUND;

        parent::__construct($this->message, $this->code);
    }
}
