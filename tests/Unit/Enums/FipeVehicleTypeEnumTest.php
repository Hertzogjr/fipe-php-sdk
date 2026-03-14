<?php

use Hertzogjr\FipePhpSdk\Vehicle\Enums\FipeVehicleTypeEnum;

describe('FipeVehicleTypeEnum', function () {
    it('has correct values', function () {
        expect(FipeVehicleTypeEnum::CAR->value)->toBe('1')
            ->and(FipeVehicleTypeEnum::MOTORCYCLE->value)->toBe('2')
            ->and(FipeVehicleTypeEnum::TRUCK->value)->toBe('3');
    });

    it('returns correct labels', function () {
        expect(FipeVehicleTypeEnum::CAR->label())->toBe('carro')
            ->and(FipeVehicleTypeEnum::MOTORCYCLE->label())->toBe('moto')
            ->and(FipeVehicleTypeEnum::TRUCK->label())->toBe('caminhão');
    });
});
