<?php

use Junior\FipePhpSdk\Vehicle\Entities\FipeVehicleEntity;

describe('FipeVehicleEntity', function () {
    it('maps fromArray using all 11 Portuguese API keys', function () {
        $entity = FipeVehicleEntity::fromArray([
            'Valor' => 'R$ 50.000,00',
            'Marca' => 'Volkswagen',
            'Modelo' => 'Gol',
            'AnoModelo' => 2020,
            'Combustivel' => 'Gasolina',
            'CodigoFipe' => '005340-6',
            'MesReferencia' => 'janeiro de 2024',
            'Autenticacao' => 'abc123',
            'TipoVeiculo' => 1,
            'SiglaCombustivel' => 'G',
            'DataConsulta' => '14/03/2026',
        ]);

        expect($entity->value)->toBe('R$ 50.000,00')
            ->and($entity->make)->toBe('Volkswagen')
            ->and($entity->model)->toBe('Gol')
            ->and($entity->modelYear)->toBe(2020)
            ->and($entity->fuel)->toBe('Gasolina')
            ->and($entity->fipeCode)->toBe('005340-6')
            ->and($entity->referenceMonth)->toBe('janeiro de 2024')
            ->and($entity->authentication)->toBe('abc123')
            ->and($entity->vehicleType)->toBe(1)
            ->and($entity->fuelAbbreviation)->toBe('G')
            ->and($entity->consultationDate)->toBe('14/03/2026');
    });

    it('casts modelYear and vehicleType to int', function () {
        $entity = FipeVehicleEntity::fromArray([
            'Valor' => 'R$ 50.000,00',
            'Marca' => 'Volkswagen',
            'Modelo' => 'Gol',
            'AnoModelo' => 2020,
            'Combustivel' => 'Gasolina',
            'CodigoFipe' => '005340-6',
            'MesReferencia' => 'janeiro de 2024',
            'Autenticacao' => 'abc123',
            'TipoVeiculo' => 1,
            'SiglaCombustivel' => 'G',
            'DataConsulta' => '14/03/2026',
        ]);

        expect($entity->modelYear)->toBeInt()
            ->and($entity->vehicleType)->toBeInt();
    });

    it('round-trips through jsonSerialize', function () {
        $data = [
            'Valor' => 'R$ 50.000,00',
            'Marca' => 'Volkswagen',
            'Modelo' => 'Gol',
            'AnoModelo' => 2020,
            'Combustivel' => 'Gasolina',
            'CodigoFipe' => '005340-6',
            'MesReferencia' => 'janeiro de 2024',
            'Autenticacao' => 'abc123',
            'TipoVeiculo' => 1,
            'SiglaCombustivel' => 'G',
            'DataConsulta' => '14/03/2026',
        ];

        $entity = FipeVehicleEntity::fromArray($data);

        expect($entity->jsonSerialize())->toBe($data);
    });
});
