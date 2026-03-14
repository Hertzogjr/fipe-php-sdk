<?php

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Junior\FipePhpSdk\Vehicle\DTOs\VehiclePayloadDTO;
use Junior\FipePhpSdk\Vehicle\Entities\FipeVehicleEntity;
use Junior\FipePhpSdk\Vehicle\Enums\FipeVehicleTypeEnum;
use Junior\FipePhpSdk\Vehicle\FipeVehicleException;
use Junior\FipePhpSdk\Vehicle\FipeVehicleResource;

describe('FipeVehicleResource::get()', function () {
    beforeEach(function () {
        $this->dto = new VehiclePayloadDTO(
            referenceCode: '331',
            makeCode: '59',
            modelCode: '5941',
            vehicleType: FipeVehicleTypeEnum::CAR,
            yearCode: '2020-1',
        );
    });

    it('returns FipeVehicleEntity with all fields on success', function () {
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

        $resource = new FipeVehicleResource(
            mockClient([new Response(200, [], json_encode($data))])
        );

        $result = $resource->get($this->dto);

        expect($result)->toHaveKey('data')
            ->and($result['data'])->toBeInstanceOf(FipeVehicleEntity::class)
            ->and($result['data']->value)->toBe('R$ 50.000,00')
            ->and($result['data']->make)->toBe('Volkswagen')
            ->and($result['data']->model)->toBe('Gol')
            ->and($result['data']->modelYear)->toBe(2020)
            ->and($result['data']->fuel)->toBe('Gasolina')
            ->and($result['data']->fipeCode)->toBe('005340-6')
            ->and($result['data']->referenceMonth)->toBe('janeiro de 2024')
            ->and($result['data']->authentication)->toBe('abc123')
            ->and($result['data']->vehicleType)->toBe(1)
            ->and($result['data']->fuelAbbreviation)->toBe('G')
            ->and($result['data']->consultationDate)->toBe('14/03/2026');
    });

    it('throws fetchFailed on HTTP error', function () {
        $resource = new FipeVehicleResource(
            mockClient([new RequestException('connection error', new Request('POST', 'test'))])
        );

        expect(fn () => $resource->get($this->dto))
            ->toThrow(FipeVehicleException::class, '[Fipe Vehicle Failed]');
    });

    it('throws invalidParameters when codigo is 2', function () {
        $resource = new FipeVehicleResource(
            mockClient([new Response(200, [], json_encode(['codigo' => '2']))])
        );

        expect(fn () => $resource->get($this->dto))
            ->toThrow(FipeVehicleException::class, 'Invalid parameters provided');
    });

    it('throws notFound when codigo is 0', function () {
        $resource = new FipeVehicleResource(
            mockClient([new Response(200, [], json_encode(['codigo' => '0']))])
        );

        expect(fn () => $resource->get($this->dto))
            ->toThrow(FipeVehicleException::class, 'Vehicle not found');
    });
});
