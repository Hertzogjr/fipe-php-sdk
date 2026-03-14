<?php

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Hertzogjr\FipePhpSdk\Vehicle\DTOs\VehiclePayloadDTO;
use Hertzogjr\FipePhpSdk\Vehicle\Entities\FipeVehicleEntity;
use Hertzogjr\FipePhpSdk\Vehicle\Enums\FipeVehicleTypeEnum;
use Hertzogjr\FipePhpSdk\Vehicle\FipeVehicleException;

describe('Vehicle via FipeClient', function () {
    beforeEach(function () {
        $this->dto = new VehiclePayloadDTO(
            referenceCode: '331',
            makeCode: '59',
            modelCode: '5941',
            vehicleType: FipeVehicleTypeEnum::CAR,
            yearCode: '2020-1',
        );

        $this->apiResponse = [
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
    });

    it('returns a fully populated FipeVehicleEntity', function () {
        $client = mockFipeClient([new Response(200, [], json_encode($this->apiResponse))]);

        $result = $client->vehicle()->get($this->dto);

        expect($result)->toHaveKey('data')
            ->and($result['data'])->toBeInstanceOf(FipeVehicleEntity::class)
            ->and($result['data']->value)->toBe('R$ 50.122,00')
            ->and($result['data']->make)->toBe('Volkswagen')
            ->and($result['data']->model)->toBe('Gol 1.0 MI Total Flex 8V 4p')
            ->and($result['data']->modelYear)->toBe(2020)
            ->and($result['data']->fuel)->toBe('Gasolina')
            ->and($result['data']->fipeCode)->toBe('005340-6')
            ->and($result['data']->referenceMonth)->toBe('janeiro de 2024')
            ->and($result['data']->authentication)->toBe('ssaaadf7fg1')
            ->and($result['data']->vehicleType)->toBe(1)
            ->and($result['data']->fuelAbbreviation)->toBe('G')
            ->and($result['data']->consultationDate)->toBe('14/03/2026 às 10:00');
    });

    it('returns different pricing for a truck', function () {
        $dto = new VehiclePayloadDTO(
            referenceCode: '331',
            makeCode: '102',
            modelCode: '4884',
            vehicleType: FipeVehicleTypeEnum::TRUCK,
            yearCode: '2019-3',
        );

        $payload = [
            'Valor' => 'R$ 180.000,00',
            'Marca' => 'Volkswagen',
            'Modelo' => 'Delivery 9.170',
            'AnoModelo' => 2019,
            'Combustivel' => 'Diesel',
            'CodigoFipe' => '820031-9',
            'MesReferencia' => 'janeiro de 2024',
            'Autenticacao' => 'xyz999',
            'TipoVeiculo' => 3,
            'SiglaCombustivel' => 'D',
            'DataConsulta' => '14/03/2026 às 10:00',
        ];

        $client = mockFipeClient([new Response(200, [], json_encode($payload))]);

        $result = $client->vehicle()->get($dto);

        expect($result['data']->vehicleType)->toBe(3)
            ->and($result['data']->fuelAbbreviation)->toBe('D')
            ->and($result['data']->value)->toBe('R$ 180.000,00');
    });

    it('throws invalidParameters when any parameter is wrong', function () {
        $client = mockFipeClient([new Response(200, [], json_encode(['codigo' => '2']))]);

        expect(fn () => $client->vehicle()->get($this->dto))
            ->toThrow(FipeVehicleException::class, 'Invalid parameters provided');
    });

    it('throws notFound when the vehicle combination does not exist', function () {
        $client = mockFipeClient([new Response(200, [], json_encode(['codigo' => '0']))]);

        expect(fn () => $client->vehicle()->get($this->dto))
            ->toThrow(FipeVehicleException::class, 'Vehicle not found');
    });

    it('throws fetchFailed when the API is unreachable', function () {
        $client = mockFipeClient([
            new RequestException('cURL error 6: Could not resolve host', new Request('POST', 'test')),
        ]);

        expect(fn () => $client->vehicle()->get($this->dto))
            ->toThrow(FipeVehicleException::class, '[Fipe Vehicle Failed]');
    });
});
