<?php

namespace Junior\FipePhpSdk\Make\Entities;

class FipeMakeEntity
{
    public function __construct(
        public readonly string $label,
        public readonly string $value,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            label: $data['Label'],
            value: $data['Value'],
        );
    }
}
