<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class TaskCompletedException extends Exception
{
    protected $message;
    protected $code;

    public function __construct(string $message = "The task has already been completed")
    {
        $this->message = $message;
        $this->code    = Response::HTTP_BAD_REQUEST;

        parent::__construct($this->message, $this->code);
    }
}
