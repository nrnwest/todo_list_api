<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * List of fields that will not be written to the session during validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register exception handlers.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Converts an exception to an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof BadRequestException) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        if ($e instanceof TaskCompletedException) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        if ($e instanceof NotFoundException) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        if ($e instanceof InternalServerErrorException) {
            Log::error('Internal Server Error', [
                'code'   => $e->getCode(),
                'url'    => $request->fullUrl(),
                'method' => $request->method(),
                'input'  => $request->all(),
                'error'  => $e->getMessage(),
                'trace'  => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if ($e instanceof ForbiddenException) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_FORBIDDEN);
        }

        if ($e instanceof UnauthorizedException) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }

        return parent::render($request, $e);
    }
}
