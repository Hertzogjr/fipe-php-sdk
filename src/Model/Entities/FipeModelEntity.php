<?php

namespace Junior\FipePhpSdk\Model\Entities;

readonly class FipeModelEntity implements \JsonSerializable
{
    public function __construct(
        public string $label,
        public string $value,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            label: $data['Label'],
            value: $data['Value'],
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'Label' => $this->label,
            'Value' => $this->value,
        ];
    }
}
