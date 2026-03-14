<?php

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Hertzogjr\FipePhpSdk\Model\DTOs\ModelsByMakePayloadDTO;
use Hertzogjr\FipePhpSdk\Model\DTOs\ModelsByYearPayloadDTO;
use Hertzogjr\FipePhpSdk\Model\DTOs\ModelYearsPayloadDTO;
use Hertzogjr\FipePhpSdk\Model\Entities\FipeModelEntity;
use Hertzogjr\FipePhpSdk\Model\Entities\FipeYearEntity;
use Hertzogjr\FipePhpSdk\Model\FipeModelException;
use Hertzogjr\FipePhpSdk\Vehicle\Enums\FipeVehicleTypeEnum;

describe('Models via FipeClient', function () {
    describe('all()', function () {
        it('returns models and years for a given make', function () {
            $payload = [
                'Modelos' => [
                    ['Label' => 'Gol', 'Value' => '5941'],
                    ['Label' => 'Polo', 'Value' => '10932'],
                    ['Label' => 'Golf', 'Value' => '4756'],
                ],
                'Anos' => [
                    ['Label' => '2023 Gasolina', 'Value' => '2023-1'],
                    ['Label' => '2022 Gasolina', 'Value' => '2022-1'],
                ],
            ];

            $dto = new ModelsByMakePayloadDTO(
                makeCode: '59',
                referenceCode: '331',
                vehicleType: FipeVehicleTypeEnum::CAR,
            );

            $client = mockFipeClient([new Response(200, [], json_encode($payload))]);

            $result = $client->model()->all($dto);

            expect($result['data']['Modelos'])->toHaveCount(3)
                ->and($result['data']['Anos'])->toHaveCount(2)
                ->and($result['data']['Modelos'][0])->toBeInstanceOf(FipeModelEntity::class)
                ->and($result['data']['Modelos'][0]->label)->toBe('Gol')
                ->and($result['data']['Anos'][0])->toBeInstanceOf(FipeYearEntity::class)
                ->and($result['data']['Anos'][0]->value)->toBe('2023-1');
        });

        it('throws invalidParameters when the make code is invalid', function () {
            $dto = new ModelsByMakePayloadDTO(
                makeCode: '99999',
                referenceCode: '331',
                vehicleType: FipeVehicleTypeEnum::CAR,
            );

            $client = mockFipeClient([new Response(200, [], json_encode(['codigo' => '2']))]);

            expect(fn () => $client->model()->all($dto))
                ->toThrow(FipeModelException::class, 'Invalid parameters provided');
        });

        it('throws fetchFailed when the API is unreachable', function () {
            $dto = new ModelsByMakePayloadDTO(
                makeCode: '59',
                referenceCode: '331',
                vehicleType: FipeVehicleTypeEnum::CAR,
            );

            $client = mockFipeClient([
                new RequestException('cURL error 6: Could not resolve host', new Request('POST', 'test')),
            ]);

            expect(fn () => $client->model()->all($dto))
                ->toThrow(FipeModelException::class, '[Fipe Model Failed]');
        });

        it('throws notFound when the make has no models', function () {
            $dto = new ModelsByMakePayloadDTO(
                makeCode: '99999',
                referenceCode: '331',
                vehicleType: FipeVehicleTypeEnum::CAR,
            );

            $client = mockFipeClient([new Response(200, [], json_encode(['codigo' => '0']))]);

            expect(fn () => $client->model()->all($dto))
                ->toThrow(FipeModelException::class, 'Model not found');
        });
    });

    describe('years()', function () {
        it('returns available years for a model', function () {
            $payload = [
                ['Label' => '2023 Gasolina', 'Value' => '2023-1'],
                ['Label' => '2022 Gasolina', 'Value' => '2022-1'],
                ['Label' => '2021 Gasolina', 'Value' => '2021-1'],
            ];

            $dto = new ModelYearsPayloadDTO(
                makeCode: '59',
                referenceCode: '331',
                vehicleType: FipeVehicleTypeEnum::CAR,
                modelCode: '5941',
            );

            $client = mockFipeClient([new Response(200, [], json_encode($payload))]);

            $result = $client->model()->years($dto);

            expect($result['data'])->toHaveCount(3)
                ->and($result['data'][0]->label)->toBe('2023 Gasolina')
                ->and($result['data'][0]->value)->toBe('2023-1');
        });

        it('throws notFound when the model code does not exist', function () {
            $dto = new ModelYearsPayloadDTO(
                makeCode: '59',
                referenceCode: '331',
                vehicleType: FipeVehicleTypeEnum::CAR,
                modelCode: '99999',
            );

            $client = mockFipeClient([new Response(200, [], json_encode(['codigo' => '0']))]);

            expect(fn () => $client->model()->years($dto))
                ->toThrow(FipeModelException::class, 'Model not found');
        });
    });

    describe('byYear()', function () {
        it('returns models filtered by a given year and fuel type', function () {
            $payload = [
                ['Label' => 'Gol 1.0', 'Value' => '5941'],
                ['Label' => 'Gol 1.6', 'Value' => '5942'],
            ];

            $dto = new ModelsByYearPayloadDTO(
                makeCode: '59',
                referenceCode: '331',
                vehicleType: FipeVehicleTypeEnum::CAR,
                yearCode: '2020-1',
            );

            $client = mockFipeClient([new Response(200, [], json_encode($payload))]);

            $result = $client->model()->byYear($dto);

            expect($result['data'])->toHaveCount(2)
                ->and($result['data'][0]->label)->toBe('Gol 1.0');
        });

        it('throws invalidParameters when the year code is invalid', function () {
            $dto = new ModelsByYearPayloadDTO(
                makeCode: '59',
                referenceCode: '331',
                vehicleType: FipeVehicleTypeEnum::CAR,
                yearCode: '9999-9',
            );

            $client = mockFipeClient([new Response(200, [], json_encode(['codigo' => '2']))]);

            expect(fn () => $client->model()->byYear($dto))
                ->toThrow(FipeModelException::class, 'Invalid parameters provided');
        });

        it('throws notFound when no models exist for the given year', function () {
            $dto = new ModelsByYearPayloadDTO(
                makeCode: '59',
                referenceCode: '331',
                vehicleType: FipeVehicleTypeEnum::CAR,
                yearCode: '9999-9',
            );

            $client = mockFipeClient([new Response(200, [], json_encode(['codigo' => '0']))]);

            expect(fn () => $client->model()->byYear($dto))
                ->toThrow(FipeModelException::class, 'Model not found');
        });
    });
});
