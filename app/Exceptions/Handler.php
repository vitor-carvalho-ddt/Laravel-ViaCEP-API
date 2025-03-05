<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\Facades\Log;

class Handler extends ExceptionHandler
{
    // ...

    public function report(Throwable $exception): void
    {
        Log::error(message: 'An unexpected error occurred', context: [
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);

        parent::report(e: $exception);
    }

    // ...
}