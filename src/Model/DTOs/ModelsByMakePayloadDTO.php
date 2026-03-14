<?php

namespace Junior\FipePhpSdk\Model\DTOs;

use Junior\FipePhpSdk\Vehicle\Enums\FipeVehicleTypeEnum;

readonly class ModelsByMakePayloadDTO implements \JsonSerializable
{
    public function __construct(
        public string $makeCode,
        public string $referenceCode,
        public FipeVehicleTypeEnum $vehicleType,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'CodigoMarca' => $this->makeCode,
            'CodigoTabelaReferencia' => $this->referenceCode,
            'CodigoTipoVeiculo' => $this->vehicleType->value,
        ];
    }
}
