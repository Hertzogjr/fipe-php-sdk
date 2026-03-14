<?php

use Hertzogjr\FipePhpSdk\Model\Entities\FipeYearEntity;

describe('FipeYearEntity', function () {
    it('maps fromArray using Portuguese API keys', function () {
        $entity = FipeYearEntity::fromArray([
            'Label' => '2020 Gasolina',
            'Value' => '2020-1',
        ]);

        expect($entity->label)->toBe('2020 Gasolina')
            ->and($entity->value)->toBe('2020-1');
    });

    it('round-trips through jsonSerialize', function () {
        $entity = FipeYearEntity::fromArray([
            'Label' => '2020 Gasolina',
            'Value' => '2020-1',
        ]);

        expect($entity->jsonSerialize())->toBe(['Label' => '2020 Gasolina', 'Value' => '2020-1']);
    });
});
