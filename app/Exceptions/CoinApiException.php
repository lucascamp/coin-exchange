<?php

namespace App\Exceptions;

use Exception;

class CoinApiException extends Exception
{
    protected $status;
    protected $code;
    protected $message;

    public function __construct(int $status, string $code, string $message)
    {
        parent::__construct($message);
        $this->status = $status;
        $this->code = $code;
    }

    public function render()
    {
        return response()->json([
            'status' => $this->status,
            'code' => $this->code,
            'message' => $this->getMessage(),
        ], $this->status);
    }
}
