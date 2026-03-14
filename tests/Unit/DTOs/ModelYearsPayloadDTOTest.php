<?php

use Hertzogjr\FipePhpSdk\Model\DTOs\ModelYearsPayloadDTO;
use Hertzogjr\FipePhpSdk\Vehicle\Enums\FipeVehicleTypeEnum;

describe('ModelYearsPayloadDTO', function () {
    it('stores constructor arguments', function () {
        $dto = new ModelYearsPayloadDTO(
            makeCode: '59',
            referenceCode: '331',
            vehicleType: FipeVehicleTypeEnum::CAR,
            modelCode: '5941',
        );

        expect($dto->makeCode)->toBe('59')
            ->and($dto->referenceCode)->toBe('331')
            ->and($dto->vehicleType)->toBe(FipeVehicleTypeEnum::CAR)
            ->and($dto->modelCode)->toBe('5941');
    });

    it('serializes to correct keys', function () {
        $dto = new ModelYearsPayloadDTO(
            makeCode: '59',
            referenceCode: '331',
            vehicleType: FipeVehicleTypeEnum::CAR,
            modelCode: '5941',
        );

        $serialized = $dto->jsonSerialize();

        expect($serialized)->toHaveKeys(['CodigoMarca', 'CodigoTabelaReferencia', 'CodigoTipoVeiculo', 'CodigoModelo'])
            ->and($serialized['CodigoMarca'])->toBe('59')
            ->and($serialized['CodigoTabelaReferencia'])->toBe('331')
            ->and($serialized['CodigoTipoVeiculo'])->toBe('1')
            ->and($serialized['CodigoModelo'])->toBe('5941');
    });
});
