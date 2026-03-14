<?php

namespace Hertzogjr\FipePhpSdk\Vehicle;

class FipeVehicleException extends \InvalidArgumentException
{
    public static function fetchFailed(\Throwable $e): self
    {
        $message = sprintf('[Fipe Vehicle Failed] context: %s', $e->getMessage());

        return new self(message: $message, code: $e->getCode());
    }

    public static function invalidParameters(): self
    {
        return new self(message: '[Fipe Vehicle Failed] context: Invalid parameters provided');
    }

    public static function notFound(): self
    {
        return new self(message: '[Fipe Vehicle Failed] context: Vehicle not found');
    }
}
