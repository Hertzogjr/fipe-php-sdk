<?php

use Hertzogjr\FipePhpSdk\Model\DTOs\ModelsByYearPayloadDTO;
use Hertzogjr\FipePhpSdk\Vehicle\Enums\FipeVehicleTypeEnum;

describe('ModelsByYearPayloadDTO', function () {
    it('stores constructor arguments', function () {
        $dto = new ModelsByYearPayloadDTO(
            makeCode: '59',
            referenceCode: '331',
            vehicleType: FipeVehicleTypeEnum::CAR,
            yearCode: '2020-1',
        );

        expect($dto->makeCode)->toBe('59')
            ->and($dto->referenceCode)->toBe('331')
            ->and($dto->vehicleType)->toBe(FipeVehicleTypeEnum::CAR)
            ->and($dto->yearCode)->toBe('2020-1');
    });

    it('derives modelYear and fuelTypeCode from yearCode', function () {
        $dto = new ModelsByYearPayloadDTO(
            makeCode: '59',
            referenceCode: '331',
            vehicleType: FipeVehicleTypeEnum::CAR,
            yearCode: '2020-1',
        );

        expect($dto->modelYear)->toBe('2020')
            ->and($dto->fuelTypeCode)->toBe('1');
    });

    it('serializes to correct keys', function () {
        $dto = new ModelsByYearPayloadDTO(
            makeCode: '59',
            referenceCode: '331',
            vehicleType: FipeVehicleTypeEnum::CAR,
            yearCode: '2020-1',
        );

        $serialized = $dto->jsonSerialize();

        expect($serialized)->toHaveKeys(['CodigoMarca', 'CodigoTabelaReferencia', 'CodigoTipoVeiculo', 'Ano', 'CodigoTipoCombustivel', 'AnoModelo'])
            ->and($serialized['Ano'])->toBe('2020-1')
            ->and($serialized['AnoModelo'])->toBe('2020')
            ->and($serialized['CodigoTipoCombustivel'])->toBe('1');
    });
});
