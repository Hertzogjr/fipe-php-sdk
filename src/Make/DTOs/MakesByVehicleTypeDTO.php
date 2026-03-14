<?php

namespace Hertzogjr\FipePhpSdk\Make\DTOs;

use JsonSerializable;
use Hertzogjr\FipePhpSdk\Vehicle\Enums\FipeVehicleTypeEnum;

readonly class MakesByVehicleTypeDTO implements JsonSerializable
{
    public function __construct(
        public FipeVehicleTypeEnum $fipeVehicleType,
        public string $referenceTableCode,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'fipeVehicleType' => $this->fipeVehicleType->value,
            'referenceTableCode' => $this->referenceTableCode,
        ];
    }
}
