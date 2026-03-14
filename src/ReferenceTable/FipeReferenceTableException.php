<?php

namespace Junior\FipePhpSdk\FipeReferenceTable;

class FipeReferenceTableException extends \InvalidArgumentException
{
    public static function fetchFailed(\Throwable $exception): self
    {
        $message = sprintf('[Fipe Reference Table Failed] context: %s', $exception->getMessage());
        return new self(message: $message, code: $exception->getCode());
    }
}