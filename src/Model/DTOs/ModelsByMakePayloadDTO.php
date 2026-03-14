<?php

namespace Hertzogjr\FipePhpSdk\Model\DTOs;

use Hertzogjr\FipePhpSdk\Vehicle\Enums\FipeVehicleTypeEnum;

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
