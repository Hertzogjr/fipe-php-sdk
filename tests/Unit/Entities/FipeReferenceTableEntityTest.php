<?php

use Junior\FipePhpSdk\ReferenceTable\Entities\FipeReferenceTableEntity;

describe('FipeReferenceTableEntity', function () {
    it('maps fromArray using Portuguese API keys', function () {
        $entity = FipeReferenceTableEntity::fromArray([
            'Codigo' => '331',
            'Mes' => 'janeiro/2024',
        ]);

        expect($entity->code)->toBe('331')
            ->and($entity->month)->toBe('janeiro/2024');
    });

    it('round-trips through jsonSerialize', function () {
        $entity = FipeReferenceTableEntity::fromArray([
            'Codigo' => '331',
            'Mes' => 'janeiro/2024',
        ]);

        $serialized = $entity->jsonSerialize();

        expect($serialized)->toBe(['code' => '331', 'month' => 'janeiro/2024']);
    });
});
