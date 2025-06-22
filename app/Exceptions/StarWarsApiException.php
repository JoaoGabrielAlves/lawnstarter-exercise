<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class StarWarsApiException extends Exception
{
    public function __construct(
        string $message = 'Star Wars API error occurred',
        int $code = 500,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
        ], $this->getCode());
    }
}
