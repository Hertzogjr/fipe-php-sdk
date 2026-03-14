<?php

namespace Junior\FipePhpSdk\Model;

class FipeModelException extends \InvalidArgumentException
{
    public static function fetchFailed(\Throwable $e): self
    {
        $message = sprintf('[Fipe Model Failed] context: %s', $e->getMessage());

        return new self(message: $message, code: $e->getCode());
    }

    public static function invalidParameters(): self
    {
        return new self(message: '[Fipe Model Failed] context: Invalid parameters provided');
    }

    public static function notFound(): self
    {
        return new self(message: '[Fipe Model Failed] context: Model not found');
    }
}
