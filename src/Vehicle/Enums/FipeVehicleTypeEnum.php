<?php

namespace Hertzogjr\FipePhpSdk\Vehicle\Enums;

enum FipeVehicleTypeEnum: string
{
    case CAR = '1';
    case MOTORCYCLE = '2';
    case TRUCK = '3';

    public function label(): string
    {
        return match ($this) {
            self::CAR => 'carro',
            self::MOTORCYCLE => 'moto',
            self::TRUCK => 'caminhão',
        };
    }
}
