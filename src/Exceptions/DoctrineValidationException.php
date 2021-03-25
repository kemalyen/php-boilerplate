<?php
declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;
use Throwable;

class DoctrineValidationException extends RuntimeException implements ExceptionInterface
{
    public function __construct(
        string $message = 'Authorization token not found!',
        $code = 422,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
