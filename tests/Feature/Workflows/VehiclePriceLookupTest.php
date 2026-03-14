<?php

use GuzzleHttp\Psr7\Response;
use Junior\FipePhpSdk\Make\DTOs\MakesByVehicleTypeDTO;
use Junior\FipePhpSdk\Make\Entities\FipeMakeEntity;
use Junior\FipePhpSdk\Model\DTOs\ModelYearsPayloadDTO;
use Junior\FipePhpSdk\Model\Entities\FipeYearEntity;
use Junior\FipePhpSdk\ReferenceTable\Entities\FipeReferenceTableEntity;
use Junior\FipePhpSdk\Vehicle\DTOs\VehiclePayloadDTO;
use Junior\FipePhpSdk\Vehicle\Entities\FipeVehicleEntity;
use Junior\FipePhpSdk\Vehicle\Enums\FipeVehicleTypeEnum;

describe('Full vehicle price lookup workflow', function () {
    it('chains reference table → make → model years → vehicle price through one FipeClient', function () {
        // Step 1: fetch reference tables → pick the latest (331)
        $referenceTablesPayload = [
            ['Codigo' => '331', 'Mes' => 'janeiro/2024'],
            ['Codigo' => '330', 'Mes' => 'dezembro/2023'],
        ];

        // Step 2: fetch car makes for reference table 331 → pick Volkswagen (59)
        $makesPayload = [
            ['Label' => 'Fiat', 'Value' => '21'],
            ['Label' => 'Volkswagen', 'Value' => '59'],
        ];

        // Step 3: fetch model years for Gol (5941) → pick 2020-1
        $modelYearsPayload = [
            ['Label' => '2023 Gasolina', 'Value' => '2023-1'],
            ['Label' => '2020 Gasolina', 'Value' => '2020-1'],
        ];

        // Step 4: fetch vehicle price
        $vehiclePayload = [
            'Valor' => 'R$ 50.122,00',
            'Marca' => 'Volkswagen',
            'Modelo' => 'Gol 1.0 MI Total Flex 8V 4p',
            'AnoModelo' => 2020,
            'Combustivel' => 'Gasolina',
            'CodigoFipe' => '005340-6',
            'MesReferencia' => 'janeiro de 2024',
            'Autenticacao' => 'ssaaadf7fg1',
            'TipoVeiculo' => 1,
            'SiglaCombustivel' => 'G',
            'DataConsulta' => '14/03/2026 às 10:00',
        ];

        $client = mockFipeClient([
            new Response(200, [], json_encode($referenceTablesPayload)),
            new Response(200, [], json_encode($makesPayload)),
            new Response(200, [], json_encode($modelYearsPayload)),
            new Response(200, [], json_encode($vehiclePayload)),
        ]);

        // Step 1 — reference tables
        $tables = $client->referenceTable()->all();
        $latestTable = $tables['data'][0];

        expect($latestTable)->toBeInstanceOf(FipeReferenceTableEntity::class)
            ->and($latestTable->code)->toBe('331');

        // Step 2 — makes
        $makes = $client->make()->byVehicleType(new MakesByVehicleTypeDTO(
            fipeVehicleType: FipeVehicleTypeEnum::CAR,
            referenceTableCode: $latestTable->code,
        ));

        $vw = array_values(array_filter($makes['data'], fn (FipeMakeEntity $m) => $m->label === 'Volkswagen'))[0];

        expect($vw)->toBeInstanceOf(FipeMakeEntity::class)
            ->and($vw->value)->toBe('59');

        // Step 3 — model years
        $years = $client->model()->years(new ModelYearsPayloadDTO(
            makeCode: $vw->value,
            referenceCode: $latestTable->code,
            vehicleType: FipeVehicleTypeEnum::CAR,
            modelCode: '5941',
        ));

        $year2020 = array_values(array_filter($years['data'], fn (FipeYearEntity $y) => str_starts_with($y->value, '2020')))[0];

        expect($year2020)->toBeInstanceOf(FipeYearEntity::class)
            ->and($year2020->value)->toBe('2020-1');

        // Step 4 — vehicle price
        $vehicle = $client->vehicle()->get(new VehiclePayloadDTO(
            referenceCode: $latestTable->code,
            makeCode: $vw->value,
            modelCode: '5941',
            vehicleType: FipeVehicleTypeEnum::CAR,
            yearCode: $year2020->value,
        ));

        expect($vehicle['data'])->toBeInstanceOf(FipeVehicleEntity::class)
            ->and($vehicle['data']->make)->toBe('Volkswagen')
            ->and($vehicle['data']->modelYear)->toBe(2020)
            ->and($vehicle['data']->fipeCode)->toBe('005340-6')
            ->and($vehicle['data']->value)->toBe('R$ 50.122,00');
    });

    it('resolves the correct make and year when multiple options are present', function () {
        $makesPayload = [
            ['Label' => 'Fiat', 'Value' => '21'],
            ['Label' => 'Honda', 'Value' => '15'],
            ['Label' => 'Volkswagen', 'Value' => '59'],
            ['Label' => 'Toyota', 'Value' => '77'],
        ];

        $modelYearsPayload = [
            ['Label' => '2023 Gasolina', 'Value' => '2023-1'],
            ['Label' => '2022 Álcool', 'Value' => '2022-3'],
            ['Label' => '2020 Gasolina', 'Value' => '2020-1'],
        ];

        $vehiclePayload = [
            'Valor' => 'R$ 89.990,00',
            'Marca' => 'Honda',
            'Modelo' => 'Civic EXL 2.0 16V',
            'AnoModelo' => 2023,
            'Combustivel' => 'Gasolina',
            'CodigoFipe' => '014241-8',
            'MesReferencia' => 'janeiro de 2024',
            'Autenticacao' => 'abc001xyz',
            'TipoVeiculo' => 1,
            'SiglaCombustivel' => 'G',
            'DataConsulta' => '14/03/2026 às 11:00',
        ];

        $client = mockFipeClient([
            new Response(200, [], json_encode($makesPayload)),
            new Response(200, [], json_encode($modelYearsPayload)),
            new Response(200, [], json_encode($vehiclePayload)),
        ]);

        // Pick Honda from the makes list
        $makes = $client->make()->byVehicleType(new MakesByVehicleTypeDTO(
            fipeVehicleType: FipeVehicleTypeEnum::CAR,
            referenceTableCode: '331',
        ));

        $honda = array_values(array_filter($makes['data'], fn (FipeMakeEntity $m) => $m->label === 'Honda'))[0];

        expect($honda->value)->toBe('15');

        // Pick the 2023 year
        $years = $client->model()->years(new ModelYearsPayloadDTO(
            makeCode: $honda->value,
            referenceCode: '331',
            vehicleType: FipeVehicleTypeEnum::CAR,
            modelCode: '4408',
        ));

        $year2023 = array_values(array_filter($years['data'], fn (FipeYearEntity $y) => str_starts_with($y->value, '2023')))[0];

        expect($year2023->value)->toBe('2023-1');

        // Fetch price
        $vehicle = $client->vehicle()->get(new VehiclePayloadDTO(
            referenceCode: '331',
            makeCode: $honda->value,
            modelCode: '4408',
            vehicleType: FipeVehicleTypeEnum::CAR,
            yearCode: $year2023->value,
        ));

        expect($vehicle['data']->value)->toBe('R$ 89.990,00')
            ->and($vehicle['data']->model)->toBe('Civic EXL 2.0 16V')
            ->and($vehicle['data']->modelYear)->toBe(2023);
    });
});
