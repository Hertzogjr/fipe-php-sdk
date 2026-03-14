<?php

namespace Junior\FipePhpSdk\Make;

class FipeMakeException extends \InvalidArgumentException
{
    public static function fetchFailed(\Throwable $exception): self
    {
        $message = sprintf('[Fipe Make Failed] context: %s', $exception->getMessage());

        return new self(message: $message, code: $exception->getCode());
    }

    public static function invalidParameters(): self
    {
        return new self(message: '[Fipe Make Failed] context: Invalid parameters provided');
    }

    public static function notFound(): self
    {
        return new self(message: '[Fipe Make Failed] context: Make not found');
    }
}
