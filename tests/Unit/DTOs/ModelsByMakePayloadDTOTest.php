<?php

use Hertzogjr\FipePhpSdk\Model\DTOs\ModelsByMakePayloadDTO;
use Hertzogjr\FipePhpSdk\Vehicle\Enums\FipeVehicleTypeEnum;

describe('ModelsByMakePayloadDTO', function () {
    it('stores constructor arguments', function () {
        $dto = new ModelsByMakePayloadDTO(
            makeCode: '59',
            referenceCode: '331',
            vehicleType: FipeVehicleTypeEnum::CAR,
        );

        expect($dto->makeCode)->toBe('59')
            ->and($dto->referenceCode)->toBe('331')
            ->and($dto->vehicleType)->toBe(FipeVehicleTypeEnum::CAR);
    });

    it('serializes to correct keys', function () {
        $dto = new ModelsByMakePayloadDTO(
            makeCode: '59',
            referenceCode: '331',
            vehicleType: FipeVehicleTypeEnum::CAR,
        );

        $serialized = $dto->jsonSerialize();

        expect($serialized)->toHaveKeys(['CodigoMarca', 'CodigoTabelaReferencia', 'CodigoTipoVeiculo'])
            ->and($serialized['CodigoMarca'])->toBe('59')
            ->and($serialized['CodigoTabelaReferencia'])->toBe('331')
            ->and($serialized['CodigoTipoVeiculo'])->toBe('1');
    });
});
