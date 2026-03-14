<?php

namespace Junior\FipePhpSdk\Model\DTOs;

use Junior\FipePhpSdk\FipeVehicleTypeEnum;

class ModelsByYearPayloadDTO implements \JsonSerializable
{
    public readonly string $modelYear;
    public readonly string $fuelTypeCode;
    public function __construct(
        public string $makeCode,
        public string $referenceCode,
        public FipeVehicleTypeEnum $vehicleType,
        public string $yearCode,
    ) {
        [$modelYear, $fuelTypeCode] = explode('-', $yearCode);

        $this->modelYear = $modelYear;
        $this->fuelTypeCode = $fuelTypeCode;
    }

    public function jsonSerialize(): array
    {
        return [
            'CodigoMarca' => $this->makeCode,
            'CodigoTabelaReferencia' => $this->referenceCode,
            'CodigoTipoVeiculo' => $this->vehicleType->value,
            'Ano' => $this->yearCode,
            'CodigoTipoCombustivel' => $this->fuelTypeCode,
            'AnoModelo' => $this->modelYear,
        ];
    }
}