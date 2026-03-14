<?php

use Hertzogjr\FipePhpSdk\Make\Entities\FipeMakeEntity;

describe('FipeMakeEntity', function () {
    it('maps fromArray using Portuguese API keys', function () {
        $entity = FipeMakeEntity::fromArray([
            'Label' => 'Volkswagen',
            'Value' => '59',
        ]);

        expect($entity->label)->toBe('Volkswagen')
            ->and($entity->value)->toBe('59');
    });
});
