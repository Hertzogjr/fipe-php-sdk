<?php

namespace Junior\FipePhpSdk\Vehicle\DTOs;

use Junior\FipePhpSdk\Vehicle\Enums\FipeVehicleTypeEnum;

class VehiclePayloadDTO implements \JsonSerializable
{
    public readonly string $fuelCode;

    public readonly string $modelYear;

    public function __construct(
        public string $referenceCode,
        public string $makeCode,
        public string $modelCode,
        public FipeVehicleTypeEnum $vehicleType,
        public string $yearCode,
    ) {
        [$modelYear, $fuelCode] = explode('-', $yearCode);

        $this->fuelCode = $fuelCode;
        $this->modelYear = $modelYear;
    }

    public function jsonSerialize(): array
    {
        return [
            'CodigoMarca' => $this->makeCode,
            'CodigoModelo' => $this->modelCode,
            'CodigoTabelaReferencia' => $this->referenceCode,
            'CodigoTipoVeiculo' => $this->vehicleType->value,
            'AnoModelo' => $this->yearCode,
            'CodigoTipoCombustivel' => $this->fuelCode,
        ];
    }
}
