<?php

/**
 * @see       https://github.com/laminas/laminas-diactoros for the canonical source repository
 * @copyright https://github.com/laminas/laminas-diactoros/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-diactoros/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;
use Throwable;

class UnauthorizedException extends RuntimeException implements ExceptionInterface
{
    public function __construct(
        string $message = 'Unauthorized JWT!',
        $code = 401,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
