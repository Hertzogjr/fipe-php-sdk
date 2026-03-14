<?php

use Junior\FipePhpSdk\Vehicle\DTOs\VehiclePayloadDTO;
use Junior\FipePhpSdk\Vehicle\Enums\FipeVehicleTypeEnum;

describe('VehiclePayloadDTO', function () {
    it('stores constructor arguments', function () {
        $dto = new VehiclePayloadDTO(
            referenceCode: '331',
            makeCode: '59',
            modelCode: '5941',
            vehicleType: FipeVehicleTypeEnum::CAR,
            yearCode: '2020-1',
        );

        expect($dto->referenceCode)->toBe('331')
            ->and($dto->makeCode)->toBe('59')
            ->and($dto->modelCode)->toBe('5941')
            ->and($dto->vehicleType)->toBe(FipeVehicleTypeEnum::CAR)
            ->and($dto->yearCode)->toBe('2020-1');
    });

    it('derives modelYear and fuelCode from yearCode', function () {
        $dto = new VehiclePayloadDTO(
            referenceCode: '331',
            makeCode: '59',
            modelCode: '5941',
            vehicleType: FipeVehicleTypeEnum::CAR,
            yearCode: '2020-1',
        );

        expect($dto->modelYear)->toBe('2020')
            ->and($dto->fuelCode)->toBe('1');
    });

    it('serializes to correct keys', function () {
        $dto = new VehiclePayloadDTO(
            referenceCode: '331',
            makeCode: '59',
            modelCode: '5941',
            vehicleType: FipeVehicleTypeEnum::CAR,
            yearCode: '2020-1',
        );

        $serialized = $dto->jsonSerialize();

        expect($serialized)->toHaveKeys(['CodigoMarca', 'CodigoModelo', 'CodigoTabelaReferencia', 'CodigoTipoVeiculo', 'AnoModelo', 'CodigoTipoCombustivel'])
            ->and($serialized['CodigoMarca'])->toBe('59')
            ->and($serialized['CodigoModelo'])->toBe('5941')
            ->and($serialized['CodigoTabelaReferencia'])->toBe('331')
            ->and($serialized['CodigoTipoVeiculo'])->toBe('1')
            ->and($serialized['AnoModelo'])->toBe('2020-1')
            ->and($serialized['CodigoTipoCombustivel'])->toBe('1');
    });
});
