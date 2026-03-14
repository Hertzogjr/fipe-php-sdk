<?php

namespace Hertzogjr\FipePhpSdk\ReferenceTable;

class FipeReferenceTableException extends \InvalidArgumentException
{
    public static function fetchFailed(\Throwable $exception): self
    {
        $message = sprintf('[Fipe Reference Table Failed] context: %s', $exception->getMessage());

        return new self(message: $message, code: $exception->getCode());
    }

    public static function notFound(): self
    {
        return new self(message: '[Fipe Reference Table Failed] context: Reference table not found');
    }
}
