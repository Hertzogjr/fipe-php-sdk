<?php

use Junior\FipePhpSdk\Make\DTOs\MakesByVehicleTypeDTO;
use Junior\FipePhpSdk\Vehicle\Enums\FipeVehicleTypeEnum;

describe('MakesByVehicleTypeDTO', function () {
    it('stores constructor arguments', function () {
        $dto = new MakesByVehicleTypeDTO(
            fipeVehicleType: FipeVehicleTypeEnum::CAR,
            referenceTableCode: '331',
        );

        expect($dto->fipeVehicleType)->toBe(FipeVehicleTypeEnum::CAR)
            ->and($dto->referenceTableCode)->toBe('331');
    });

    it('serializes to correct keys', function () {
        $dto = new MakesByVehicleTypeDTO(
            fipeVehicleType: FipeVehicleTypeEnum::MOTORCYCLE,
            referenceTableCode: '330',
        );

        $serialized = $dto->jsonSerialize();

        expect($serialized)->toHaveKeys(['fipeVehicleType', 'referenceTableCode'])
            ->and($serialized['fipeVehicleType'])->toBe('2')
            ->and($serialized['referenceTableCode'])->toBe('330');
    });
});
