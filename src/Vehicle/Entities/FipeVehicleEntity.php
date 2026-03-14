<?php

namespace Hertzogjr\FipePhpSdk\Vehicle\Entities;

class FipeVehicleEntity implements \JsonSerializable
{
    public function __construct(
        public string $value,
        public string $make,
        public string $model,
        public int $modelYear,
        public string $fuel,
        public string $fipeCode,
        public string $referenceMonth,
        public string $authentication,
        public int $vehicleType,
        public string $fuelAbbreviation,
        public string $consultationDate,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            value: $data['Valor'],
            make: $data['Marca'],
            model: $data['Modelo'],
            modelYear: $data['AnoModelo'],
            fuel: $data['Combustivel'],
            fipeCode: $data['CodigoFipe'],
            referenceMonth: $data['MesReferencia'],
            authentication: $data['Autenticacao'],
            vehicleType: $data['TipoVeiculo'],
            fuelAbbreviation: $data['SiglaCombustivel'],
            consultationDate: $data['DataConsulta'],
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'Valor' => $this->value,
            'Marca' => $this->make,
            'Modelo' => $this->model,
            'AnoModelo' => $this->modelYear,
            'Combustivel' => $this->fuel,
            'CodigoFipe' => $this->fipeCode,
            'MesReferencia' => $this->referenceMonth,
            'Autenticacao' => $this->authentication,
            'TipoVeiculo' => $this->vehicleType,
            'SiglaCombustivel' => $this->fuelAbbreviation,
            'DataConsulta' => $this->consultationDate,
        ];
    }
}
