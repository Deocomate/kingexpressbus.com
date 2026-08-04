<?php

namespace App\Support\Admin;

use RuntimeException;

class DeleteBlockedException extends RuntimeException
{
    public function __construct(string $message, public readonly int $bookingCount = 0)
    {
        parent::__construct($message);
    }
}
