<?php

namespace Etsy\Exceptions;

use RuntimeException;
use Throwable;

/**
 * Class MissingTaxonomyException
 *
 * @package Etsy\Exceptions
 */
class MissingTaxonomyException extends RuntimeException
{
    public function __construct(string $message = null, int $code = 0, ?Throwable $previous = null)
    {
        if ($message === null) {
            $message = 'Please run `artisan etsy:taxonomy` to sync new taxonomies.';
        }

        parent::__construct($message, $code, $previous);
    }
}
