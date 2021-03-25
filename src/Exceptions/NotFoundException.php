<?php
declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;
use Throwable;

class NotFoundException extends RuntimeException implements ExceptionInterface
{
    public function __construct(
        string $message = 'Page not found',
        $code = 404,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
