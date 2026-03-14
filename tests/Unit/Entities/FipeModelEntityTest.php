<?php

use Hertzogjr\FipePhpSdk\Model\Entities\FipeModelEntity;

describe('FipeModelEntity', function () {
    it('maps fromArray using Portuguese API keys', function () {
        $entity = FipeModelEntity::fromArray([
            'Label' => 'Gol',
            'Value' => '5941',
        ]);

        expect($entity->label)->toBe('Gol')
            ->and($entity->value)->toBe('5941');
    });

    it('round-trips through jsonSerialize', function () {
        $entity = FipeModelEntity::fromArray([
            'Label' => 'Gol',
            'Value' => '5941',
        ]);

        expect($entity->jsonSerialize())->toBe(['Label' => 'Gol', 'Value' => '5941']);
    });
});
