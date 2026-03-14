<?php

namespace Hertzogjr\FipePhpSdk\ReferenceTable\Entities;

readonly class FipeReferenceTableEntity implements \JsonSerializable
{
    public function __construct(
        public string $code,
        public string $month
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            code: $data['Codigo'],
            month: $data['Mes'],
        );
    }

    public function jsonSerialize(): mixed
    {
        return [
            'code' => $this->code,
            'month' => $this->month,
        ];
    }
}
