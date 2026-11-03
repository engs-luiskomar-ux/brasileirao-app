<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Representa uma regra de negócio violada, com mensagem e status HTTP.
 */
class RegraNegocioException extends RuntimeException
{
    public function __construct(string $message, public readonly int $status = 422)
    {
        parent::__construct($message);
    }
}
